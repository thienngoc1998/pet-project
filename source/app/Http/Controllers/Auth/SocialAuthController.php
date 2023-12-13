<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Proxy\SocialiteProxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SocialAccountService;

class SocialAuthController extends Controller
{
    private SocialiteProxy $socialiteProxy;

    public function __construct(SocialiteProxy $socialiteProxy)
    {
        $this->socialiteProxy = $socialiteProxy;
    }

    public function redirect(string $social): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->socialiteProxy->redirect($social);
    }

    public function callback($social)
    {
        $socialiteUser = $this->socialiteProxy->getUser($social);

        $user = SocialAccountService::createOrGetUser($socialiteUser, $social);

        if (Auth::attempt([
            'email'    => $user->email,
            'password' => $user->name
        ])) {
            return redirect()->route('get.user.dashboard');
        }

        return redirect()->to('/');
    }
}
