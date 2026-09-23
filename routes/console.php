<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

Artisan::command('portfolio:create-admin', function () {
    $name = $this->ask('Name');
    $email = $this->ask('Email');
    $password = $this->secret('Password (at least 12 characters)');

    $validation = Validator::make(compact('name', 'email', 'password'), [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', Password::min(12)],
    ]);

    if ($validation->fails()) {
        $this->error($validation->errors()->first());

        return 1;
    }

    User::create(compact('name', 'email', 'password'));
    $this->info('Admin account created. You can now log in at /login.');

    return 0;
})->purpose('Create a private portfolio admin login');

Artisan::command('portfolio:reset-admin-password', function () {
    $email = $this->ask('Existing admin email');
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->error('No MySQL account exists for that email. Run portfolio:create-admin instead.');

        return 1;
    }

    $password = $this->secret('New password (at least 12 characters)');
    $validation = Validator::make(compact('password'), [
        'password' => ['required', Password::min(12)],
    ]);

    if ($validation->fails()) {
        $this->error($validation->errors()->first());

        return 1;
    }

    $user->update(['password' => $password]);
    $this->info('Password updated. You can now log in at /login.');

    return 0;
})->purpose('Reset a portfolio admin password locally');

Artisan::command('portfolio:sync-admin', function () {
    $credentials = config('portfolio.admin');
    $validation = Validator::make($credentials, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', Password::min(12)],
    ]);

    if ($validation->fails()) {
        $this->error('Set PORTFOLIO_ADMIN_NAME, PORTFOLIO_ADMIN_EMAIL, and a password of at least 12 characters.');

        return 1;
    }

    User::updateOrCreate(
        ['email' => $credentials['email']],
        ['name' => $credentials['name'], 'password' => $credentials['password']]
    );

    $this->info('Production administrator account is ready. Remove the temporary admin environment variables now.');

    return 0;
})->purpose('Create or update the production administrator from temporary environment variables');
