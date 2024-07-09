<?php

namespace App\Http\Controllers;

use App\BeatmapType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class HttpClientController extends Controller
{
    static function GetToken()
    {
        $response = Http::post('https://osu.ppy.sh/oauth/token', [
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'grant_type' => 'client_credentials',
            'scope' => 'public',
        ]);
        return $response->json();
    }

    function GetBeatmap(Request $request)
    {
        $request->validate([
            'beatmap_id'=>'required|numeric'
        ]);
        $response = Http::withToken($request->token)->get('https://osu.ppy.sh/api/v2/beatmapsets/' . $request->beatmap_id);
        if($response->serverError()){
            abort(503, 'Can\'t connect with osu api server');
        }
        if($response->clientError()){
            abort(404, 'Beatmap not found');
        }
        return $response;
    }

    function GetUser(Request $request)
    {
        $response = Http::withToken($request->token)->get('https://osu.ppy.sh/api/v2/users/' . $request->user_id);
        if($response->serverError()){
            abort(503, 'Can\'t connect with osu api server');
        }
        if($response->clientError()){
            abort(404, 'User not found');
        }
        return $response->json();
    }

    function GetUserBeatmaps(Request $request)
    {
        $request->validate([
            'user_id'=>'required|numeric',
            'type'=>['required', Rule::enum(BeatmapType::class)],
            'limit'=>'required|numeric'
        ]);
        $response = Http::withToken($request->token)->get('https://osu.ppy.sh/api/v2/users/' . $request->user_id . '/beatmapsets/' . $request->type . '?limit='. $request->limit);
        if($response->serverError()){
            abort(503, 'Can\'t connect with osu api server');
        }
        if($response->clientError()){
            abort(404, 'User beatmaps not found');
        }
        return $response->json();
    }

}
