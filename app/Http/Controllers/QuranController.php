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
use App\Models\UserSetting;
use Illuminate\Support\Facades\Mail;

class QuranController extends Controller
{

    public function find()
    {
        $query = Ayah::where("masahef_id", config("main.moshaf_id"));

        $query->select(
            "ayah_id as id",
            "page_id as page",
            "surah_id as sura",
            "ayah_number as aya",
            "part as juz",
            "hizb as hizb",
            "clean_text as text",
            "youtube_video_id"
        );

        if (request()->filled("page")) {
            $query->where("page_id", request("page"));
        }

        if (request()->filled("q")) {
            $query->where(function ($query) {
                return $query->where("clean_text", "LIKE", "%" . request()->get("q") . "%")
                    ->orWhere("search_text", "LIKE", "%" . request()->get("q") . "%")
                    ->orWhere("uthmani_text", "LIKE", "%" . request()->get("q") . "%");
            });
        }

        if (request()->filled("is_favorited")) {
            $query->whereHas("favorites", function ($query) {
                $query->where("user_id", auth("api")->user()->id);
            });
        }

        if (request()->filled("is_commented")) {
            $query->whereHas("comments", function ($query) {
                $query->where("user_id", auth("api")->user()->id);
            });
        }

        if (request()->filled("last_update")) {

            $date = request()->get("last_update");

            if (is_numeric($date)) {
                $date = date("Y-m-d H:i:s", $date);
            }

            $query->where('updated_date', ">=", $date);
        }


        $ayat = $query->get();

        if (auth("api")->check()) {
            $ayat = $ayat->each(function ($row) {
                $row->comment = DB::table("ayah_comments")
                    ->select('id', 'title')
                    ->where("sura", $row->sura)
                    ->where("aya", $row->aya)
                    ->where("user_id", auth("api")->user()->id)
                    ->first();

                return $row;
            });
        }


        // $ayat = $query->get()->each(function($row) {

        //     $aya_coords = DB::table("aya_text_highlights")
        //         ->select("x1", "y1", "x2", "y2")
        //         ->where("sura", $row->sura)
        //         ->where("aya", $row->aya)->get();

        //     $number_coords = DB::table("aya_number_highlights")
        //         ->select("x1", "y1", "x2", "y2")
        //         ->where("sura", $row->sura)
        //         ->where("aya", $row->aya)->first();

        //     $row->coords = (object) [
        //         "aya" => $aya_coords,
        //         "number" => $number_coords
        //     ];
        // });

        return response()->success($ayat);
    }

    public function favorite($sura, $aya)
    {

        $aya_row = Ayah::where("masahef_id", config("main.moshaf_id"))->where("surah_id", $sura)
            ->where("ayah_number", $aya)->first();

        $is_favorited = DB::table("ayah_favorites")
            ->where("sura", $sura)
            ->where("aya", $aya)
            ->where("user_id", auth("api")->user()->id)
            ->count();

        if ($is_favorited) {
            DB::table("ayah_favorites")
                ->where("sura", $sura)
                ->where("aya", $aya)
                ->where("user_id", auth("api")->user()->id)
                ->delete();
        } else {
            DB::table("ayah_favorites")->insert([
                "user_id" => auth("api")->user()->id,
                "page" => $aya_row->page_id,
                "sura" => $sura,
                "aya" => $aya,
                "ayah_id" => $aya_row->ayah_id,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
        }

        return response()->success([
            "is_favorited" => !$is_favorited
        ]);
    }

    public function comments()
    {
        $query = DB::table("ayah_comments")->orderBy("created_at", "desc");

        $query->where("ayah_comments.user_id", auth("api")->user()->id);

        $query->select(
            "id",
            "title",
            "sura",
            "aya",
            "uthmani_text as text",
            "part",
            "hizb",
            "page_id as page",
            "created_at"
        );

        $query->join("ayah", function ($query) {
            $query->on("ayah_comments.sura", "=", "ayah.surah_id")
                ->on("ayah_comments.aya", "=", "ayah.ayah_number")
                ->where("ayah.masahef_id", config("main.moshaf_id"));
        });

        return response()->success($query->get());
    }

    public function comment($sura, $aya)
    {
        $title = request("title");
        $aya_row = Ayah::where("masahef_id", config("main.moshaf_id"))->where("surah_id", $sura)->where("ayah_number", $aya)->first();

        $is_commented = DB::table("ayah_comments")
            ->where("sura", $sura)
            ->where("aya", $aya)
            ->where("user_id", auth("api")->user()->id)
            ->count();

        if ($is_commented) {
            // update comment
            DB::table("ayah_comments")->where("user_id", auth("api")->user()->id)
                ->where("sura", $sura)
                ->where("aya", $aya)
                ->update([
                    "title" => $title
                ]);
        } else {
            DB::table("ayah_comments")->insert([
                "user_id" => auth("api")->user()->id,
                "page" => $aya_row->page_id,
                "sura" => $sura,
                "aya" => $aya,
                "title" => $title,
                "ayah_id" => $aya_row->ayah_id,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
        }

        return response()->success(true);
    }

    public function removeComment($sura, $aya)
    {
        DB::table("ayah_comments")->where("user_id", auth("api")->user()->id)
            ->where("sura", $sura)
            ->where("aya", $aya)
            ->delete();

        return response()->success(true);
    }
}
