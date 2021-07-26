<?php

namespace App\Http\Controllers;

use App\Models\Reciter;
use App\Models\ReciterIndex;
use App\Models\Track;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TracksController extends Controller
{

    public function find()
    {
        $query = Track::orderBy("surah_id", "asc");

        if (request()->filled("index_id")) {
            $query->where("index_id", request()->get("index_id"));
        }

        if (request()->filled("q")) {
            $query->whereHas("sura", function ($query) {
                $query->where("search_text", "like", "%" . request()->get("q") . "%");
            });
        }

        return response()->success($query->get());
    }

    public function favorite($track_id)
    {
        $is_favorited = DB::table("audio_tracks_favorites")->where("track_id", $track_id)
            ->where("user_id", auth("api")->user()->id)
            ->count();

        if ($is_favorited) {
            DB::table("audio_tracks_favorites")->where("track_id", $track_id)
                ->where("user_id", auth("api")->user()->id)->delete();
        } else {
            DB::table("audio_tracks_favorites")->insert([
                "user_id" => auth("api")->user()->id,
                "track_id" => $track_id,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
        }

        return response()->success([
            "is_favorited" => !$is_favorited
        ]);
    }
}