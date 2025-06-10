<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\CategoryComposer;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Chỉ inject khi view kế thừa layouts.app (header có dùng category)
        View::composer('layouts.app', CategoryComposer::class);
    }

    public function register(): void
    {
        //
    }
}
