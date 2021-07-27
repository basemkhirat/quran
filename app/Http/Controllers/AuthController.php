<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\UserSetting;

class AuthController extends Controller
{

    /**
     * Create a new account
     */
    public function register(\Illuminate\Http\Request $request)
    {

        $validator = Validator::make($request->all(), [
            "email" => [
                "required",
                "email",
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNull('provider');
                }),
            ],
            'password' => 'required|min:6',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->error([
                "message" => "Validation Error",
                "errors" => $validator->errors()->all()
            ], 422);
        }

        $user = new User();

        $user->username = rand(99999, 99999999);
        $user->password = app('hash')->make($request->get("password"));
        $user->email = $request->get("email");
        $user->first_name = $request->get("first_name");
        $user->last_name = $request->get("last_name");
        $user->phone = $request->get("phone");
        $user->mode = "light";
        $user->lang = app()->getLocale();
        $user->status = 1;

        if ($user->save()) {

            try {
                Mail::send('emails.welcome', ['user' => $user], function ($m) use ($user) {
                    $m->from(config("mail.from.address"),  trans("main.name"));
                    $m->to($user->email, $user->first_name)->subject(trans("main.welcome"));
                });
            } catch (Exception $e) {
                //
            }

            $credentials = [
                "email" => $request->email,
                "password" => $request->password
            ];

            $token = auth("api")->attempt($credentials);
            return $this->respondWithToken($token);
        }
    }

    /**
     * Login by email/password
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->error([
                "message" => "Validation Error",
                "errors" => $validator->errors()->all()
            ], 422);
        }

        $credentials = request(['email', 'password']);

        if (!$token = auth("api")->attempt($credentials)) {
            return response()->error('Not Authenticated', 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Build success user response
     */
    protected function respondWithToken($token)
    {

        $user = auth("api")->user();

        return response()->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth("api")->factory()->getTTL() * 60,
            'user' => $user
        ]);
    }

    public function user()
    {
        $logged_user = auth("api")->user();

        $user = User::where("users.id", $logged_user->id)
            ->select("users.*", "user_settings.page_id", "user_settings.color")
            ->leftJoin("user_settings", "users.id", "=", "user_settings.user_id")->first();

        return response()->success($user);
    }

    /**
     * Forgot password request
     * @return mixed
     */
    public function forgot()
    {

        $validator = Validator::make(request()->all(), ['email' => 'required|email']);

        if ($validator->fails()) {
            return response()->error([
                "message" => "Validation Error",
                "errors" => $validator->errors()->all()
            ], 422);
        }

        $email = request()->get("email");

        $user = User::where("email", $email)->first();

        if (!$user) {
            return response()->error([
                "message" => "Validation Error",
                "errors" => [trans("main.email_not_exist")]
            ], 422);
        }

        $code = mt_rand(10000, 90000);

        $user->email = $email;
        $user->code = $code;
        $user->save();

        try {
            Mail::send('emails.forget', ['user' => $user], function ($m) use ($user) {
                $m->from(config("mail.from.address"),  trans("main.name"));
                $m->to($user->email, $user->first_name)->subject(trans("main.forgot_my_password"));
            });
        } catch (Exception $e) {
            //
        }

        return response()->success(trans("main.verification_code_sent"));
    }

    /**
     * Reset password request
     * @return string
     */
    public function reset()
    {

        $validator = Validator::make(request()->all(), ['code' => 'required']);

        if ($validator->fails()) {
            return response()->error(
                [
                    "message" => "Validation Error",
                    "errors" => $validator->errors()->all()
                ],
                422
            );
        }

        $code = request()->get("code");

        $user = User::where("code", $code)->first();

        if (!$user) {
            return response()->error([
                "message" => "Validation Error",
                "errors" => [trans("main.invalid_verification_code")]
            ], 422);
        }

        $validator = Validator::make(request()->all(), ['password' => 'required|min:7']);

        if ($validator->fails()) {
            return response()->error([
                "message" => "Validation Error",
                "errors" => $validator->errors()->all()
            ], 422);
        }

        $user->code = "";
        $user->password = app('hash')->make(request()->get("password"));
        $user->updated_at = date("Y-m-d H:i:s");
        $user->save();

        try {
            Mail::send('emails.reset', ['user' => $user], function ($m) use ($user) {
                $m->from(config("mail.from.address"),  trans("main.name"));
                $m->to($user->email, $user->first_name)->subject(trans("main.reset_my_password"));
            });
        } catch (Exception $e) {
            //
        }

        return response()->success(trans("main.password_changed"));
    }

    /**
     * Update personal information
     */
    public function profile()
    {

        $user = auth("api")->user();

        $validator = Validator::make(request()->all(), [
            "email" => [
                "email",
                Rule::unique('users')->where(function ($query) use ($user) {
                    return $query->where('id', "!=", $user->id);
                })
            ],
            'password' => 'min:6',
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'lang' => 'in:ar,en',
            'mode' => 'in:light,dark'
        ]);

        if ($validator->fails()) {
            return response()->error(["errors" => $validator->errors()->all()], 422);
        }

        if (request()->filled("first_name")) {
            $user->first_name = request()->get("first_name");
        }

        if (request()->filled("last_name")) {
            $user->last_name = request()->get("last_name");
        }

        if (request()->filled("email")) {
            $user->email = request()->get("email");
        }

        if (request()->filled("mode")) {
            $user->mode = request()->get("mode");
        }

        if (request()->filled("lang")) {
            $user->lang = request()->get("lang");
        }

        if (request()->filled("tashkeel")) {
            $user->tashkeel = request()->get("tashkeel");
        }

        if (request()->filled("phone")) {
            $user->phone = request()->get("phone");
        }

        if (request()->get("password") != "") {
            $user->password = bcrypt(request()->get("password"));
        }

        $user->save();

        return response()->success(trans("main.profile_changed"));
    }

    /**
     * Login with google
     */
    public function google(\Illuminate\Http\Request $request)
    {

        $url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $request->get("access_token");

        $content = json_decode(file_get_contents($url));

        $user = User::where("provider", "google")
            ->where("provider_id", $content->id)
            ->first();

        if (!$user) {

            $email_exists = User::where("email", $content->email)->first();

            if ($email_exists) {
                return response()->error(["errors" => ["Email is already exist"]], 422);
            }

            $user = new User();

            $user->username = $content->given_name . "" . $content->family_name . "_" . $content->id;
            $user->password = "";
            $user->email = $content->email;
            $user->first_name = $content->given_name;
            $user->last_name = $content->family_name;
            $user->status = 1;
            $user->provider = "google";
            $user->provider_id = $content->id;
            $user->lang = app()->getLocale();

            $user->save();
        }

        auth("api")->login($user);
        $token = auth("api")->tokenById($user->id);

        return $this->respondWithToken($token);
    }

    /**
     * Login with facebook
     */
    public function facebook(\Illuminate\Http\Request $request)
    {

        $url = "https://graph.facebook.com/me?fields=email,name&access_token=" . $request->get("access_token");

        $content = json_decode(file_get_contents($url));

        $user = User::where("provider", "facebook")
            ->where("provider_id", $content->id)
            ->first();

        if (!$user) {

            $email_exists = User::where("email", $content->email)->first();

            if ($email_exists) {
                return response()->error(["errors" => ["Email is already exist"]], 422);
            }

            $user = new User();

            $user->username = snake_case($content->name) . "_" . $content->id;
            $user->password = "";
            $user->email = $content->email;
            $user->first_name = $content->name;
            $user->last_name = "";
            $user->status = 1;
            $user->provider = "facebook";
            $user->provider_id = $content->id;
            $user->lang = app()->getLocale();

            $user->save();
        }

        auth("api")->login($user);
        $token = auth("api")->tokenById($user->id);

        return $this->respondWithToken($token);
    }


    public function settings()
    {
        $user_settings = UserSetting::where("user_id", auth("api")->user()->id)->first();

        if (!$user_settings) {
            $user_settings = new UserSetting();
            $user_settings->user_id = auth("api")->user()->id;
            $user_settings->page_id = 0;
            $user_settings->color = "grey";
        }

        if (request()->filled("page_id")) {
            $user_settings->page_id = request("page_id");
        }

        if (request()->filled("color")) {
            $user_settings->color = request("color");
        }

        $user_settings->save();

        return response()->success("saved");
    }
}
