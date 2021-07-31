<?php

namespace App\Http\Controllers;

class DownloadController extends Controller
{
    public function download()
    {
        $url = request()->get("url");
        $name = request()->get("name");

        header("Content-disposition:attachment; filename=$name");
        readfile($url);
    }
}
