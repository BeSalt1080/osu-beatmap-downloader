<?php

namespace App\Http\Controllers;

use App\BeatmapCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class HttpClientController extends Controller
{
    static function GetToken()
    {
        $response = Http::post('https://osu.ppy.sh/oauth/token', [
            'client_id' => Config::get('services.osu.client_id'),
            'client_secret' => Config::get('services.osu.client_secret'),
            'grant_type' => 'client_credentials',
            'scope' => 'public',
        ]);
        return $response->json();
    }

    static function handleApiResponse($response)
    {
        if ($response->serverError()) {
            abort(503, 'Can\'t connect with the osu api server');
        }
        if ($response->clientError()) {
            abort(404, 'Item not found');
        }
        return $response->json();
    }

    function GetBeatmap(Request $request)
    {
        $request->validate([
            'beatmap_id' => 'required|numeric'
        ]);
        $response = Http::withToken($request->token)->get('https://osu.ppy.sh/api/v2/beatmapsets/' . $request->beatmap_id);
        return self::handleApiResponse($response);
    }

    function GetUser(Request $request)
    {
        $response = Http::withToken($request->token)->get('https://osu.ppy.sh/api/v2/users/' . $request->user_id);
        return self::handleApiResponse($response);
    }

    function GetUserBeatmaps(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'type' => ['required', Rule::enum(BeatmapCategory::class)],
            'limit' => 'required|numeric'
        ]);
        $queryParams = array_filter([
            'limit' => $request->limit
        ]);
        $response = Http::withToken($request->token)->get('https://osu.ppy.sh/api/v2/users/' . $request->user_id . '/beatmapsets/' . $request->type, $queryParams);
        return self::handleApiResponse($response);
    }

    function GetUserScores(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'type' => 'required|in:best,firsts,recent',
            'mode' => 'nullable|in:fruits,mania,osu,taiko',
            'limit' => 'nullable|numeric',
            'offset' => 'nullable|numeric',
            'legacy_only' => 'nullable',
            'include_fails' => 'nullable'
        ]);
        $queryParams = array_filter([
            'limit' => $request->limit,
            'offset' => $request->offset,
            'legacy_only' => $request->legacy_only,
            'include_fails' => $request->include_fails
        ]);
        $response = Http::withToken($request->token)->get('https://osu.ppy.sh/api/v2/users/' . $request->user_id . '/scores/' . $request->type, $queryParams);
        return self::handleApiResponse($response);
    }
}
