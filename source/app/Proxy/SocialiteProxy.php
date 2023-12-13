<?php
declare(strict_types=1);

namespace App\Proxy;

use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialiteProxy
{
    private $socialite;

    public function __construct(Socialite $socialite)
    {
        $this->socialite = $socialite;
    }

    public function redirect(?string $driver): RedirectResponse
    {
        return $this->socialite->driver($driver)->redirect();
    }

    public function getUser(?string $driver): User
    {
        return $this->socialite->driver($driver)->user();
    }

}
