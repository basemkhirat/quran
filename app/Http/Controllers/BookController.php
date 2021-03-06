<?php

namespace App\Http\Controllers;

use App\Models\Ayah;
use App\Models\Book;
use App\Models\BookContent;
use App\Models\Footnotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Report;
use Illuminate\Support\Facades\Mail;

class BookController extends Controller
{

    /**
     * @param Request $request
     * @return array
     * Get books list,
     */
    public function find()
    {

        $order = request()->filled("order") ? request()->get("order") : "alphabetic";
        $query = Book::select('books_id as id', 'short_name as slug', 'arabic_title as title', 'description_ar as description', 'pdf_url', 'books_type_id as type')
            ->where('is_active', 1);

        switch ($order) {
            case "alphabetic":
                $query->orderBy("arabic_title", "asc");
                break;
            case "favorites":
                $query->withCount('favorites')->orderBy("favorites_count", "desc");
                break;
            case "popular":
                $query->orderBy("featured", "desc");
                break;
        }

        if (request()->filled("type")) {
            $query->where("books_type_id", request()->get("type"));
        }

        if (request()->filled("q")) {
            $query->where("arabic_title", "like", "%" . request()->get("q") . "%");
        }

        if (request()->filled("is_favorited")) {
            $query->whereHas("favorites", function ($query) {
                $query->where("user_id", auth("api")->user()->id);
            });
        }

        return response()->success($query->get());
    }

    public function details($book_id)
    {
        $query = Book::where("books_id", $book_id)
            ->select('books_id as id', 'short_name as slug', 'arabic_title as title', 'description_ar as description', 'pdf_url', 'books_type_id as type')
            ->where('is_active', 1);
        return response()->success($query->first());
    }


    /**
     * Get books' contents by page no.
     * @param Request $request
     */
    public function booksContentByPage(Request $request)
    {
        $bookIDs = explode(',', $request->books);


        $books = Book::select('books_id as id', 'books_type_id as type', 'short_name as slug',  'arabic_title as title', 'languages_id')->whereIn('books_id', $bookIDs)->get();

        $data = [];

        if($request->page) {
            $ayat = Ayah::select('ayah_id as id', 'uthmani_text as text', 'surah_id as sura', 'ayah_number as aya')
            ->where('page_id', $request->page)
            ->where('masahef_id', config("main.moshaf_id"))->get();
        }else{
            $ayat = Ayah::select('ayah_id as id', 'uthmani_text as text', 'surah_id as sura', 'ayah_number as aya')
            ->where('surah_id', $request->sura)
            ->where('ayah_number', $request->aya)
            ->where('masahef_id', config("main.moshaf_id"))->get();
        }

        $ayat->each(function ($q) use (&$data, $bookIDs, $books) {

            $records = BookContent::select('books_id as id', 'books_contents_id as page_id', 'revised_text as text')

                ->whereIn('books_id', $bookIDs)
                ->where('surah_id', $q->sura)
                ->where('ayah_from_id', $q->aya)->get();

            $records = $records->map(function ($record) use ($records, $books) {
                $book = $books->where("id", $record->id)->first();
                $text = $record->text;
                $record->type = $book->type;
                $record->title = $book->title;
                $record->footnotes =  $this->getFootnotes($text, $record->id);
                $record->direction = in_array($book->languages_id, [1, 2, 11, 35, 49]) ? "rtl" : "ltr";
                return $record;
            });

            $data[] = (object) [
                "id" => $q->id,
                "text" => $q->text,
                "sura" => $q->sura,
                "aya" => $q->aya,
                "books" => $records
            ];
        });

        return response()->success($data);
    }

    public function getFootnotes($text, $book_id)
    {
        preg_match_all('/{\[\d+\]}/', $text, $matches);

        if (empty($matches[0])) return [];

        $map = array_map(function ($str) {
            $str = str_replace('{[', '', $str);
            $str = str_replace(']}', '', $str);
            return $str;
        }, $matches[0]);

        return !empty($map) ?
            Footnotes::where('books_id', $book_id)->whereIn('footnote_number', $map)->pluck('footnote', 'footnote_number') : [];
    }


    public function favorite($book_id)
    {
        $is_favorited = DB::table("books_favorites")->where("book_id", $book_id)
            ->where("user_id", auth("api")->user()->id)
            ->count();

        if ($is_favorited) {
            DB::table("books_favorites")->where("book_id", $book_id)
                ->where("user_id", auth("api")->user()->id)->delete();
        } else {
            DB::table("books_favorites")->insert([
                "user_id" => auth("api")->user()->id,
                "book_id" => $book_id,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
        }

        return response()->success([
            "is_favorited" => !$is_favorited
        ]);
    }

    public function reportContent($book_id, $sura, $aya)
    {
        $validator = validator()->make(request()->all(), [
            "name" => "required",
            "email" => "required|email",
            'message' => 'required|max:500',
            'linguist_stars' => 'required',
            'legal_stars' => 'required',
            'wording_stars' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->error([
                "message" => "Validation Error",
                "errors" => $validator->errors()->all()
            ], 422);
        }

        $report = new Report();

        $report->book_id = $book_id;
        $report->sura = $sura;
        $report->aya = $aya;
        $report->name = request("name");
        $report->email = request("email");
        $report->message = request("message");
        $report->legal_stars = request("legal_stars", 0);
        $report->wording_stars = request("wording_stars", 0);
        $report->linguist_stars = request("linguist_stars", 0);
        $report->country = getUserCountry();

        $report->save();

        // Send a mail message

        try {
            Mail::send('emails.report', ["name" => request("name"), "country" => getCountryTitle($report->country), "text" => request("message")], function ($m) {
                $m->from(config("mail.from.address"),  trans("main.name"));
                $m->to(config("mail.from.address"), trans("main.name"))->subject("?????????? ???????? ???? ?????? ????????????");
            });
        } catch (Exception $e) {
            //
        }

        return response()->success(true);
    }

    /**
     * @param Request $request
     * @return mixed
     * Search.
     */
    public function search(Request $request)
    {
        return Ayah::select('uthmani_text as text', 'clean_text', 'ayah_number as aya', 'surah_id as sura')->where('masahef_id', 2)->where('clean_text', 'LIKE', '%' . $request->keyword . '%')->get();
    }
}
