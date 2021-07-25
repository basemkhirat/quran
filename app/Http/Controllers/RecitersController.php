<?php

namespace App\Http\Controllers;

use App\Models\Reciter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecitersController extends Controller
{

    public function find(Request $request)
    {
        $query = Reciter::select("id", "name_" . app()->getLocale() . " as name");

        $query->with(["rewayat" => function ($q) {
            $q->select("audio_reciters_rewayat.id", "audio_reciters_rewayat.reciter_id",  "audio_reciters_rewayat.index_url", "audio_reciters_rewayat.index_listing", "rewaya_trans_name.translation as name")->orderBy("audio_reciters_rewayat.rewaya_id", "desc")
                ->join("rewaya_trans_name", "rewaya_trans_name.rewaya_id", "=", "audio_reciters_rewayat.rewaya_id")
                ->where("rewaya_trans_name.languages_id", config("main.locales." . app()->getLocale() . ".id"));
        }]);

        $query->orderBy("order", "asc");

        return response()->json($query->get());
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

        return response()->json([
            "is_favorited" => !$is_favorited
        ]);
    }
}
