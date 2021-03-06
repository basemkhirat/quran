<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Message;
use App\Models\Page;
use Illuminate\Support\Facades\Mail;
use Exception;

class PagesController extends Controller
{

    /**
     * Page details
     * @param bool $slug
     */
    public function details($slug = false)
    {
        $query = Page::where("slug", $slug);

        $page = $query->first();

        if (request()->get("with") == "translations") {
            $translations = (object)[];

            foreach (["ar", "en", "ur"] as $locale) {
                $translations->{$locale} = (object) [
                    "title" => $page->{"title_" . $locale},
                    "excerpt" => $page->{"excerpt_" . $locale},
                    "content" => $page->{"content_" . $locale}
                ];
            }

            $page->translations = $translations;
        }

        if (!$page) {
            return abort(404, "Page Not Found");
        }

        return response()->success($page);
    }


    /**
     * Contact page
     * @return mixed
     */
    public function contact()
    {
        $validator = validator()->make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->error(["errors" => $validator->errors()->all()], 422);
        }

        $data = [
            "name" => request()->get("name"),
            "email" => request()->get("email"),
            "country" => getUserCountry(),
            "message" => request()->get("message"),

        ];

        $message = new Message($data);

        $message->save();

        // Send a mail message

        try {
            Mail::send('emails.contact', $data, function ($m) {
                $m->from(config("mail.from.address"), trans("main.name"));
                $m->to(config("mail.from.address"), trans("main.name"))->subject("رسالة جديدة");
            });
        } catch (Exception $e) {
            //
        }

        return response()->success("sent");
    }

    /**
     * Faq page
     * @return mixed
     */
    public function faq()
    {
        $query = Faq::orderBy("order");

        $questions = $query->get();

        if (request()->get("with") == "translations") {

            $rows = [];

            foreach ($questions as $question) {

                $translations = (object)[];

                foreach (["ar", "en", "ur"] as $locale) {
                    $translations->{$locale} = (object) [
                        "title" => $question->{"title_" . $locale},
                        "content" => $question->{"content_" . $locale}
                    ];
                }

                $question->translations = $translations;
                $rows[] = $question;
            }

            return response()->success($rows);
        }

        return response()->success($questions);
    }
}
