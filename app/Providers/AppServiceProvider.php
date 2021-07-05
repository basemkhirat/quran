<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Illuminate\Contracts\Routing\ResponseFactory::class, function() {
            return new \Laravel\Lumen\Http\ResponseFactory();
        });
    }

  /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
     
        URL::forceRootUrl(config('app.url'));

        if (Str::contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Response::macro('ok', function ($data) {

            $data["status"] = true;
            $data["code"] = 200;

            return Response::json($data);
        });

        Response::macro('success', function ($data) {
            return Response::json([
                "status" => true,
                "code" => 200,
                'data' => $data
            ]);
        });

        Response::macro('error', function ($message, $code = 500) {

            $data = [
                "status" => false,
                "code" => $code,
            ];

            if (is_array($message)) {
                $data = array_merge($data, $message);
            } else {
                $data["message"] = $message;
            }

            return Response::json($data, $code);
        });
    }
}
