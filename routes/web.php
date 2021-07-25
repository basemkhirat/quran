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
        'as' => 'reciters',
        'uses' => 'RecitersController@favorite',
        'middleware' => "auth:api"
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
    //tafasir
    Route::get('tafasir', [
        'as' => 'api_tafasir',
        'uses' => 'ApiController@getTafasir'
    ]);

//  {key1}/{surah_number}/{aya_number}
    Route::get('tafsir/{key}/{sura_number}/{aya_number}', [
        'as' => 'api_tafsir',
        'uses' => 'ApiController@getTafsir'
    ]);
    //  {key1}/{surah_number}/{aya_number}
    Route::get('trans/{key}/{sura_number}/{aya_number}', [
        'as' => 'api_trans',
        'uses' => 'ApiController@getTrans'
    ]);

//  {page_tafsir}/{key1,key2}/{sura_number}
    Route::get('page_tafsir/{keys}/{page_number}', [
        'as' => 'page_tafsir',
        'uses' => 'ApiController@getPageTafsir'
    ]);
//  {page_trans}/{key1,key2}/{sura_number}
    Route::get('page_trans/{keys}/{page_number}', [
        'as' => 'page_trans',
        'uses' => 'ApiController@getPageTranses'
    ]);

//  /{page_number}
    Route::get('ayat/{page_number}', [
        'as' => 'api_aya',
        'uses' => 'ApiController@getAyat'
    ]);

//  /{page_number}
    Route::get('ayat2/{page_number}', [
        'as' => 'api_ayat2',
        'uses' => 'ApiController@getAyat2'
    ]);
    Route::get('transes', [
        'as' => 'transes',
        'uses' => 'ApiController@gettranses'
    ]);
    Route::get('qurantext',[
        'as' => 'quran-text',
        'uses' => 'ApiController@getQuranText'
    ]);
    Route::get('singleTrack', 'ApiController@getTrack');
    Route::get('books', 'BookController@index');
    Route::get('book-contents', 'BookController@booksContentByPage');
    Route::get('search', 'BookController@search');
    Route::post('pdf-request', 'ApiController@printRequest');
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