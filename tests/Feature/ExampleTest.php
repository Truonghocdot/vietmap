<?php

use Database\Seeders\DemoStorefrontSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $this->seed(DemoStorefrontSeeder::class);

    $response = $this->get('/');

    $response->assertStatus(200);
});
