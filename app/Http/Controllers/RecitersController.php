<?php

namespace App\Http\Controllers;

use App\Models\Reciter;
use App\Models\ReciterIndex;
use App\Models\Track;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecitersController extends Controller
{

    public function find()
    {
        $order = request()->filled("order") ? request()->get("order") : "alphabetic";
        $rewaya_id = request()->filled("rewaya_id") ? request()->get("rewaya_id") : 10;
        $query = Reciter::select("id", "name_" . app()->getLocale() . " as name");

        switch ($order) {
            case "alphabetic":
                $query->orderBy("name_" . app()->getLocale(), "asc");
                break;
            case "favorites":
                $query->withCount('favorites')->orderBy("favorites_count", "desc");
                break;
            case "listeners":
                $query->orderBy("listeners", "desc");
                break;
            case "popular":
                $query->orderBy("featured", "desc");
                break;
        }

        if (request()->filled("is_favorited")) {
            $query->whereHas("favorites", function ($query) {
                $query->where("user_id", auth("api")->user()->id);
            });
        }

        if (request()->filled("is_featured")) {
            $query->where("featured", 1);
        }

        $query->whereHas("indexes", function ($query) use ($rewaya_id) {
            $query->where("rewaya_id", $rewaya_id);

            if (request()->filled("has_full_mushaf")) {
                $query->where("has_full_mushaf", 1);
            }
        });

        $query->with(["indexes" => function ($q) {
            $q->select("audio_reciters_indexes.id", "audio_reciters_indexes.reciter_id",  "audio_reciters_indexes.rewaya_id",  "audio_reciters_indexes.index_url", "audio_reciters_indexes.index_listing", "rewaya_trans_name.translation as name")->orderBy("audio_reciters_indexes.rewaya_id", "desc")
                ->join("rewaya_trans_name", "rewaya_trans_name.rewaya_id", "=", "audio_reciters_indexes.rewaya_id")
                ->where("rewaya_trans_name.languages_id", config("main.locales." . app()->getLocale() . ".id"));
        }]);

        return response()->success($query->get());
    }

    public function details($reciter_id) {
   
            $query = Reciter::where("id", $reciter_id)->select("id", "name_" . app()->getLocale() . " as name");
    
            $query->with(["indexes" => function ($q) {
                $q->select("audio_reciters_indexes.id", "audio_reciters_indexes.reciter_id",  "audio_reciters_indexes.rewaya_id",  "audio_reciters_indexes.index_url", "audio_reciters_indexes.index_listing", "rewaya_trans_name.translation as name")->orderBy("audio_reciters_indexes.rewaya_id", "desc")
                    ->join("rewaya_trans_name", "rewaya_trans_name.rewaya_id", "=", "audio_reciters_indexes.rewaya_id")
                    ->where("rewaya_trans_name.languages_id", config("main.locales." . app()->getLocale() . ".id"));
            }]);
    
            return response()->success($query->first());
        
    }

    public function favorite($reciter_id)
    {
        $is_favorited = DB::table("audio_reciters_favorites")->where("reciter_id", $reciter_id)
            ->where("user_id", auth("api")->user()->id)
            ->count();

        if ($is_favorited) {
            DB::table("audio_reciters_favorites")->where("reciter_id", $reciter_id)
                ->where("user_id", auth("api")->user()->id)->delete();
        } else {
            DB::table("audio_reciters_favorites")->insert([
                "user_id" => auth("api")->user()->id,
                "reciter_id" => $reciter_id,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
        }

        return response()->success([
            "is_favorited" => !$is_favorited
        ]);
    }
}
