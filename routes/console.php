<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:ensure-admin', function () {
    $email = env('ADMIN_EMAIL');
    $password = env('ADMIN_PASSWORD');
    $name = env('ADMIN_NAME', 'Administrateur');

    if (! $email || ! $password) {
        $this->comment('ADMIN_EMAIL ou ADMIN_PASSWORD absent, aucun admin cree.');

        return 0;
    }

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]
    );

    $this->info("Admin pret : {$user->email}");

    return 0;
})->purpose('Create or update the configured admin user');
