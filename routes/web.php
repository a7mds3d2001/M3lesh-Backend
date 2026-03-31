<?php

use Illuminate\Support\Facades\Route;

// Redirect root to Filament admin dashboard (avoids showing Laravel welcome when deployed)
Route::get('/', function () {
    return redirect('/dashboard', 302);
});

// Note: Public files are served directly via the `public/storage` symlink
// created by `php artisan storage:link`, so we don't need a custom /storage route.
