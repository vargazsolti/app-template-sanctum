<?php

namespace App\Providers;

use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Logout::class => [
            \App\Listeners\RevokeUiSessionTokens::class,
        ],
    ];
}
