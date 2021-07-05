<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecitersResources;
use App\Http\Resources\TimingResources;
use App\Http\Resources\TransResource;
use App\Models\Ayat;
use App\Models\Ayat2;
use App\Models\Book;
use App\Models\BookContent;
use App\Models\Reciters;
use App\Models\Tafasir;
use App\Models\Tafsir;
use App\Models\Tracks;
use App\Models\Transes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $key
     * @param $sura_number
     * @param $aya_number
     * @return array
     */
    public function getTafsir($key, $sura_number, $aya_number)
    {
        return Tafsir::getTafsirByKeys($key, $sura_number, $aya_number);
    }

    /**
     * @param $key
     * @param $sura_number
     * @param $aya_number
     * @return array
     * get translation by aya
     */

    public function getTrans($key, $sura_number, $aya_number)
    {

        return Transes::getTransByKeys($key, $sura_number, $aya_number);
    }

    /**
     * Get list of definitions for full page
     *
     * @param $keys
     * @param $sura_number
     * @return array
     */
    public function getPageTafsir($keys, $page_number)
    {
        $captions = [];
        $body = [];
        $data = Tafasir::where('key', '=', $keys)->where('page', '=', $page_number)->select('key')->get();
        if (count($data) > 0) {
            $data[0]->caption()->where('morph', '=', 'tafsir')->get()->each(function ($c) use (&$captions, $keys) {
                $captions[$keys][$c->lang] = $c->caption;
            });

            Tafasir::where('key', '=', $keys)->where('page', '=', $page_number)
                ->select('key', 'sura', 'aya', 'nass')->get()->map(function ($b) use (&$body) {
                    $body[$b->sura . '_' . $b->aya] = [$b->key => $b->nass];
                });
        }

        $results = [
            'meta' => $captions,
            'body' => $body
        ];
        return $results;
    }


    /**
     * Get list of All Tafasir
     *
     * @return mixed
     */
    public function getTafasir()
    {
        //
        // return Tafasir::all();
        $data = Tafasir::select('key')->first();
        $captions = [];
        $data->caption()->where('morph', '=', 'tafsir')->get()->each(function ($c) use (&$captions) {
            $captions[$c->lang] = $c->caption;
        });
        $data['caption'] = $captions;
        return [$data];
    }

    /**
     * Get list of ayat based on page number
     *
     * @param $page_number
     * @return mixed
     */
    public function getAyatByPageNumber($page_number)
    {
        //
        return Ayat::getAyatByPageNumber($page_number);
    }

    /**
     * Get list of ayat characters codes based on page number (according to new Al-Madina Mosshaf)
     *
     * @param $page_number
     * @return mixed
     */
    public function getAyat2($page_number)
    {
        //
        return Ayat2::getAyat2ByPageNumber($page_number);
    }

    /**
     * @return array
     * get transes with caption
     */
    public function gettranses()
    {
        $data = Transes::select('key')->first();
        $captions = [];
        $data->caption()->where('morph', '=', 'trans')->get()->each(function ($c) use (&$captions) {
            $captions[$c->lang] = $c->caption;
        });
        $data['caption'] = $captions;
        return [$data];
    }


    /**
     * @param $keys
     * @param $sura_number
     * @return array|array[]
     */
    public function getPageTranses($keys, $page_number)
    {
        $captions = [];
        $body = [];
        $data = Transes::where('key', '=', $keys)->where('page', '=', $page_number)->select('key')->get();
        if (count($data) > 0) {
            $data[0]->caption()->where('morph', '=', 'trans')->get()->each(function ($c) use (&$captions, $keys) {
                $captions[$keys][$c->lang] = $c->caption;
            });

            Transes::where('key', '=', $keys)->where('page', '=', $page_number)
                ->select('key', 'sura', 'aya', 'nass')->get()->map(function ($b) use (&$body) {
                    $body[$b->sura . '_' . $b->aya] = [$b->key => $b->nass];
                });
        }

        $results = [
            'meta' => $captions,
            'body' => $body
        ];

        return $results;
    }

    public function getQuranText()
    {
        $obj = [];
        for ($i = 1; $i <= 604; $i++) {
            $data = DB::table('ayah')->where('page_id', '=', $i)->where('masahef_id', '=', 2)->get();

            $res = [];

            foreach ($data as $val) {
                $res[] = [$val->surah_id, $val->ayah_id, "{$val->uthmani_text}"];
            }
            $obj["{$i}"] = $res;
        }
        return json_encode($obj);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * get reciters
     */
    public function getReciters()
    {
        return RecitersResources::collection(Reciters::all());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     * get audios timing
     */

    public function getAudioTiming(Request $request)
    {
        $this->validate($request, [
            'track' => 'required|url'
        ]);
        $track = Tracks::where('url', $request->track)->first();
        return TimingResources::collection($track->timing);
    }

    public function getTrack(Request $request)
    {
        $this->validate($request, [
            'sura_id' => 'required',
            'reciter_id' => 'required'
        ]);
        return Tracks::where('surah_id', $request->sura_id)
            ->where('audio_reciters_id', $request->reciter_id)
            ->select('url')->first();
    }

    public function printRequest()
    {
        $validator = Validator::make(request()->all(), [
            'name'    => 'required',
            'email'   => 'required|email',
            'description' => 'required',
        ]);

        if ($validator->fails())
            return ['status' => false, 'message' => 'Please provide all the required fields.'];

        $message = 'Phone: ' . request()->phone . '<br/>   Organization: ' . request()->organization . '<br/>';
        $message .= request()->description;

        try {
            Mail::send([], [], function ($email) use ($message) {
                $email->to(env('MAIL_TO'))
                    ->from(request()->email, request()->name)
                    ->subject("Email For Print Copy")
                    ->setBody($message, 'text/html');
            });

            return ['status' => true, 'message' => 'Email sent successfully.'];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }
}
