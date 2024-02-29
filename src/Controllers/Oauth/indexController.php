<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;
use App\Helpers\bHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;
use GuzzleHttp\Client;
use Carbon\Carbon;

class indexController extends Controller
{
    public function login(): \Illuminate\Http\RedirectResponse
    {
        $state = time();
        $url = sprintf(
            '%s/auth/oauth/v2/authorize?response_type=code&client_id=%s&redirect_uri=https%%3A%%2F%%2F%s%%2Foauth%%2Fcallback&scope=openid%%20profile%%20%s&state=%s',
            env('LDAP_URL'),
            env('CLIENT_ID'),
            env('SITE_URL'),
            env('LDAP_ROLE'),
            $state
        );

        return redirect($url);
    }

    public function callback(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        $tokenUrl = env('LDAP_URL') . '/auth/oauth/v2/token';
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => env('CLIENT_ID'),
            'redirect_uri' => 'https://' . env('SITE_URL') . '/oauth/callback',
            'scope' => 'openid profile ' . env('LDAP_ROLE'),
            'code' => $request->query('code'),
        ];

        $http = new Client();
        $response = $http->post($tokenUrl, ['form_params' => $params]);
        $tokenData = json_decode((string) $response->getBody(), true);
        if (!isset($tokenData['access_token'])) {
            return abort(401);
        }

        Session::put('tokenLoginVariable', $tokenData['access_token']);
        $userinfoUrl = env('LDAP_URL') . '/openid/connect/v1/userinfo';
        $userinfoResponse = $http->get($userinfoUrl, [
            'headers' => ['Authorization' => 'Bearer ' . $tokenData['access_token']],
        ]);
        $userinfo = json_decode((string) $userinfoResponse->getBody(), true);

        if (!bHelper::checkRole(explode(', ', trim($userinfo['roles'] ?? '')))) {
            return abort(401);
        }

        $profileResponse = $http->get(env('LDAP_URL') . '/ibb-hesabim-api/api/profile', [
            'headers' => ['Authorization' => 'Bearer ' . $tokenData['access_token']],
        ]);
        $profile = json_decode((string) $profileResponse->getBody(), true);

        if (empty($profile['profile'])) {
            return abort(401);
        }

        $user = User::updateOrCreate(
            ['username' => $profile['profile']['uid']],
            [
                'name' => $profile['profile']['firstName'] . ' ' . $profile['profile']['lastName'],
                'email' => $profile['profile']['email'] ?? null,
                'phone' => $profile['profile']['phoneNumber'] ?? null,
                'password' => Hash::make(env('CUSTOM_PASSWORD')), // Consider using more secure & unique password handling
            ]
        );

        $user->assignRole('personal'); // Make sure 'personal' role exists

        if ($user->is_active != 1) {
            return 'Bu uygulamaya giriş yetkiniz yoktur.';
        }

        Auth::loginUsingId($user->id);

        $user->update([
            'last_seen' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->route('admin.index');
    }

    public function logout(): \Illuminate\Http\RedirectResponse
    {
        Session::flush();
        Auth::logout();
        Artisan::call('cache:clear');

        return redirect('/');
    }
}
