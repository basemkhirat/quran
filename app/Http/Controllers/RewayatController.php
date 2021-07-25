<?php

namespace App\Http\Controllers;

use App\Models\Reciters;
use App\Models\Rewaya;
use Illuminate\Http\Request;

class RewayatController extends Controller
{

    public function find(Request $request)
    {
        $query = Rewaya::select("rewaya.rewaya_id as id", "short_name as slug", "rewaya_trans_name.translation as name");

        $query->join("rewaya_trans_name", "rewaya.rewaya_id", "=", "rewaya_trans_name.rewaya_id");
        $query->where("rewaya_trans_name.languages_id", config("main.locales." . app()->getLocale() . ".id"));
        $query->orderBy("rewaya.order", "asc");

        $rewayat = $query->get();
        
        return $rewayat;
    }
}
