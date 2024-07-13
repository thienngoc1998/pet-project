<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoginSuccessEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private readonly Authenticatable|null $user, private readonly array $agentData)
    {
    }

    public function getUser(): Authenticatable
    {
        return $this->user;
    }

    public function getAgentData(): array
    {
        return $this->agentData;
    }
}
