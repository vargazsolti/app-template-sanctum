<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class RevokeUiSessionTokens
{
    public function handle(Logout $event): void
    {
        $user = $event->user;
        if (!$user) return;

        // Laravel Sanctum personal_access_tokens tábla: name = 'ui-session'
        $user->tokens()->where('name', 'ui-session')->delete();
    }
}
