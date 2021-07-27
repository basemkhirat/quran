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
        $query = Ayah::where("page_id", request("page"))->where("rewaya_id", 0);

        $query->select(
            "ayah_id as id",
            "page_id as page",
            "surah_id as sura",
            "ayah_number as aya"
        );

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

        return response()->success($query->get());
    }

    public function favorite($sura, $aya)
    {

        $aya_row = Ayah::where("surah_id", $sura)->where("ayah_number", $aya)->first();

        $is_favorited = DB::table("ayah_favorites")
            ->where("sura", $sura)
            ->where("aya", $aya)
            ->where("user_id", auth("api")->user()->id)
            ->count();

        if ($is_favorited) {
            DB::table("ayah_favorites")
                ->where("sura", $sura)
                ->where("aya", $aya)
                ->where("user_id", auth("api")->user()->id)->delete();
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

    public function comment($sura, $aya)
    {
        $title = request("title");
        $aya_row = Ayah::where("surah_id", $sura)->where("ayah_number", $aya)->first();

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
