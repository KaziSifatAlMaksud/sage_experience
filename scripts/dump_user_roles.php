<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\User;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Dump all users and their Spatie roles
foreach (User::with('roles')->get() as $u) {
    echo $u->id . " | " . $u->name . " | " . json_encode($u->roles->pluck('name')) . "\n";
}
