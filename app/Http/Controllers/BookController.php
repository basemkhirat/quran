<?php

namespace App\Http\Controllers;

use App\Models\Ayah;
use App\Models\Book;
use App\Models\BookContent;
use App\Models\Footnotes;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * Get books list,
     */
    public function index(Request $request)
    {
        $data = [];
        Book::select('books_id as id', 'short_name as en', 'title as ar', 'books_type_id')->where('is_active', 1)->get()
            ->each(function($b) use(&$data) {
                $data[$b->books_type_id == 1 ? 'interpretations':'translations'][] = ['caption' => ['ar' => $b->ar, 'en' => $b->en, 'ur' => $b->en], 'key' => $b->en, 'id' => $b->id];
            });

        return $data;
    }//..... end of customBookList() ......//

    /**
     * Get books' contents by page no.
     * @param Request $request
     */
    public function booksContentByPage(Request $request)
    {
        $bookIDs = explode(',', $request->books);

        $meta = [];

        $books = Book::select('books_id as id', 'short_name as en', 'title as ar', 'languages_id')->whereIn('books_id', $bookIDs)->get();
        $books->each(function($b) use(&$meta) {
                $meta[$b->en] = ['ar' => $b->ar, 'en' => $b->en, 'ur' => $b->en, 'dir' => $b->languages_id == 4 ? 'ltr': 'rtl' ];
            });

        $data = [];

        Ayah::select('surah_id as sura', 'ayah_number as aya')->where('page_id', $request->page)->where('masahef_id', 2)->get()
            ->each(function($q) use(&$data, $bookIDs, $books) {
                $records = BookContent::select('books_id', 'revised_text')->whereIn('books_id', $bookIDs)->where('surah_id', $q->sura)->where('ayah_from_id', $q->aya)->get();

                foreach ($books as $book) {
                    $record = $records->where('books_id', $book->id);
                    $text = optional($record->first())->revised_text ?? '';
                    $data[$q->sura.'_'.$q->aya][$book->en] = $text;
                    $data[$q->sura.'_'.$q->aya][$book->en.'_footnotes'] = (object) $this->getFootnotes($text, $book->id);
                    $data[$q->sura.'_'.$q->aya][$book->en.'_book_id'] = $book->id;
                }//..... end foreach() .....//
            });

        return ['body' => $data, 'meta' => $meta];
    }//..... end of booksContentByPage() .....//

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
    }//..... end of getFootnotes() .....//

    /**
     * @param Request $request
     * @return mixed
     * Search.
     */
    public function search(Request $request)
    {
        return Ayah::select('uthmani_text as text', 'clean_text', 'ayah_number as aya', 'surah_id as sura')->where('masahef_id', 2)->where('clean_text', 'LIKE', '%'.$request->keyword.'%')->get();
    }//.... end of search() .....//
}