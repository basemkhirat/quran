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

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () use ($router) {
    return 'Main home page';
});

Route::group(['prefix' => 'v1/'], function ($route) {    // Authentication

    // Authentication

    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/facebook', 'AuthController@facebook');
    Route::post('auth/google', 'AuthController@google');
    Route::post('auth/register', 'AuthController@register');
    Route::post('auth/forgot', 'AuthController@forgot');
    Route::post('auth/reset', 'AuthController@reset');
    Route::get('auth/user', ['uses' => 'AuthController@user', 'middleware' => "auth:api"]);
    Route::post('auth/profile', ['uses' => 'AuthController@profile', 'middleware' => "auth:api"]);

    // Reciters

    Route::get('reciters', [
        'as' => 'reciters',
        'uses' => 'RecitersController@find'
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

    Route::post('tracks/{track_id}/favorite', [
        'as' => 'tracks_favorite',
        'uses' => 'TracksController@favorite'
    ]);

    Route::get('rewayat', [
        'as' => 'rewayat',
        'uses' => 'RewayatController@find'
    ]);

    // Track

    Route::get('track', [
        'as' => 'track',
        'uses' => 'ApiController@getTrack'
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
    Route::post('books/{book_id}/favorite', [
        'as' => 'books_favorite',
        'uses' => 'BookController@favorite'
    ]);
    Route::get('book-contents', 'BookController@booksContentByPage');
    Route::post('book-contents/{content_id}/report', 'BookController@reportContent');

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

    $route->get('pages/{slug}', 'PagesController@details');
    $route->post('messages/send', 'PagesController@contact');

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