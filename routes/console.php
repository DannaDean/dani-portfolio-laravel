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
