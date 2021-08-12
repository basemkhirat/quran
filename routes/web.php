<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;
use App\Models\Ayah;
use Illuminate\Support\Facades\DB;

Route::group(['prefix' => 'v1/'], function ($route) {

    // $route->get("/", function () {
    //     $rows = Ayah::get();

    //     foreach ($rows as $row) {

    //         $data = DB::table("ayat")->where("surat_id", $row->surah_id)
    //             ->where("numberinsurat", $row->ayah_number)
    //             ->first();

    //         $row->part = $data->juz_id;
    //         $row->hizb = ceil($data->hizbQuarter_id * 60 / 240);

    //         $row->save();
    //     }

    //     return "done";
    // });

    // Authentication

    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/facebook', 'AuthController@facebook');
    Route::post('auth/google', 'AuthController@google');
    Route::post('auth/register', 'AuthController@register');
    Route::post('auth/forgot', 'AuthController@forgot');
    Route::post('auth/reset', 'AuthController@reset');
    Route::get('auth/user', ['uses' => 'AuthController@user', 'middleware' => "auth:api"]);
    Route::post('auth/settings', ['uses' => 'AuthController@settings', 'middleware' => "auth:api"]);
    Route::post('auth/profile', ['uses' => 'AuthController@profile', 'middleware' => "auth:api"]);

    // Reciters

    Route::get('reciters', [
        'as' => 'reciters',
        'uses' => 'RecitersController@find'
    ]);

    Route::get('reciters/{reciter_id}', [
        'as' => 'reciter_details',
        'uses' => 'RecitersController@details'
    ]);

    Route::post('reciters/{reciter_id}/favorite', [
        'as' => 'reciter_favorite',
        'uses' => 'RecitersController@favorite',
        'middleware' => "auth:api"
    ]);

    Route::get('tracks', [
        'as' => 'tracks',
        'uses' => 'TracksController@find'
    ]);
    

    Route::get('track', [
        'as' => 'track_info',
        'uses' => 'TracksController@details'
    ]);

    Route::post('tracks/{track_id}/favorite', [
        'as' => 'tracks_favorite',
        'uses' => 'TracksController@favorite'
    ]);

    Route::get('rewayat', [
        'as' => 'rewayat',
        'uses' => 'RewayatController@find'
    ]);

    // Timing

    Route::get('timing', [
        'as' => 'timing',
        'uses' => 'ApiController@getAudioTiming'
    ]);

    Route::get('ayat/{page_number}', [
        'as' => 'api_aya',
        'uses' => 'ApiController@getAyat'
    ]);

    Route::get('ayat2/{page_number}', [
        'as' => 'api_ayat2',
        'uses' => 'ApiController@getAyat2'
    ]);
    Route::get('transes', [
        'as' => 'transes',
        'uses' => 'ApiController@gettranses'
    ]);
    Route::get('qurantext', [
        'as' => 'quran-text',
        'uses' => 'ApiController@getQuranText'
    ]);
    Route::get('singleTrack', 'ApiController@getTrack');
    Route::get('books', 'BookController@find');
    Route::get('books/{book_id}', 'BookController@details');
    Route::post('books/{book_id}/favorite', [
        'as' => 'books_favorite',
        'uses' => 'BookController@favorite'
    ]);
    Route::get('book-contents', 'BookController@booksContentByPage');
    Route::post('book-contents/{book_id}/{sura}/{aya}/report', 'BookController@reportContent');

    Route::get('search', 'BookController@search');
    Route::post('pdf-request', 'ApiController@printRequest');

    // Posts

    $route->get('posts', 'PostsController@index');
    $route->get('posts/sections', 'PostsController@sections');
    $route->get('posts/sections/{id}', 'PostsController@section');
    $route->get('posts/tags/{id}', 'PostsController@tag');
    $route->get('posts/{id}', 'PostsController@details');
    $route->put('posts/{id}/view', 'PostsController@view');

    // Pages

    $route->get('pages/faq', 'PagesController@faq');
    $route->get('pages/{slug}', 'PagesController@details');
    $route->post('messages/send', 'PagesController@contact');

    // Quran

    Route::get('quran', [
        'as' => 'ayat',
        'uses' => 'QuranController@find'
    ]);


    Route::get('quran/comments', [
        'as' => 'comments',
        'uses' => 'QuranController@comments',
        'middleware' => "auth:api"
    ]);

    Route::post('quran/{sura}/{aya}/favorite', [
        'as' => 'ayah_favorite',
        'uses' => 'QuranController@favorite'
    ]);

    Route::post('quran/{sura}/{aya}/comment', [
        'as' => 'ayah_comment',
        'uses' => 'QuranController@comment'
    ]);

    Route::delete('quran/{sura}/{aya}/comment', [
        'as' => 'ayah_remove_comment',
        'uses' => 'QuranController@removeComment'
    ]);

    Route::get('download', [
        'as' => 'download',
        'uses' => 'DownloadController@download'
    ]);
});

// Route::get('load-font/{font}', function ($font) {
//     $file = base_path('public') . '/fonts2/' . $font;

//     if (file_exists($file)) {
//         header('Content-Type: application/octet-stream');
//         header('Content-Disposition: inline; filename="' . basename($file) . '"');
//         header('Content-Length: ' . filesize($file));
//         readfile($file);
//         exit;
//     }
// });