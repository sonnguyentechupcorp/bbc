<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Observers\CategoryObserver;
use App\Observers\PostObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //Use Observers.
        User::observe(UserObserver::class);
        Category::observe(CategoryObserver::class);
        Post::observe(PostObserver::class);
    }
}
