<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\SessionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWT as JWTAuthJWT;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;





class ApiController extends Controller
{
    public function index()
    {

        $lastSession = DB::table('session_masters')->orderBy('id', 'desc')->first();
        if (!$lastSession) {
            return response()->json(['status' => false, 'messages' => 'No session data available'], 400);
        }

        $sessionId = $lastSession->id;
        $session_name = date('Y', strtotime($lastSession->startDate)) . '-' . date('y', strtotime($lastSession->endDate));
        $data['sessionId'] = $sessionId;
        $data['session_name'] = $session_name;
        return response()->json(['status' => true, 'session' => $data]);
    }


    public function login(Request $post)
    {
        $credentials = $post->only('username', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['status' => false, 'error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        $session = SessionMaster::find($post->sessionId);
        if ($session) {
            session(['sessionId' => $session->id]);
        }


        session(['userId' => $user->id]);

        $ip = $post->ip();
        $response = Http::get("http://ip-api.com/json/{$ip}");
        $geo = $response->json();

        $log = [
            'ipAddress'   => $ip,
            'userAgent'   => $post->server('HTTP_USER_AGENT'),
            'userId'      => $user->id,
            'geoLocation' => isset($geo['lat'], $geo['lon']) ? "{$geo['lat']} / {$geo['lon']}" : 'Unknown',
            'url'         => $post->fullUrl(),
            'parameters'  => 'portal',
        ];
        DB::table('user_activity_logs')->insert($log);

        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully.',
            'data' => $this->respondWithToken($token),
        ]);
    }


    public function profile()
    {
        return response()->json(auth('agent')->user());
    }





    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('agent')->factory()->getTTL() * 60, // Ensure 'api' guard is JWT
        ];
    }



}
