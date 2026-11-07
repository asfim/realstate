<?php

test('maintenance utilities dashboard loads successfully', function () {
    $response = $this->get(route('system.maintenance'));

    $response->assertStatus(200);
    $response->assertSee('Maintenance', false);
    $response->assertSee('Cache Clear', false);
    $response->assertSee('Storage Link', false);
    $response->assertSee('Migrate Database', false);
});

test('clear-cache route clears application cache and returns 200', function () {
    $response = $this->get(route('system.clear-cache'));

    $response->assertStatus(200);
    $response->assertSee('Cache Cleared Successfully');
    $response->assertSee('optimize:clear');
});

test('clear-cache route returns json when requested', function () {
    $response = $this->getJson(route('system.clear-cache'));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'action'  => 'clear-cache',
    ]);
});

test('storage-link route executes storage symlink', function () {
    $response = $this->get(route('system.storage-link'));

    $response->assertStatus(200);
    $response->assertSee('Storage Link Result');
    $response->assertSee('storage:link');
});

test('migrate route runs database migrations safely with force', function () {
    $response = $this->get(route('system.migrate'));

    $response->assertStatus(200);
    $response->assertSee('Database Migrations Executed');
    $response->assertSee('migrate --force');
});

test('optimize route caches application configuration and routes', function () {
    $response = $this->get(route('system.optimize'));

    $response->assertStatus(200);
    $response->assertSee('Application Optimized & Cached');
});

test('seed route runs foundation seeder safely with force', function () {
    $response = $this->get(route('system.seed'));

    $response->assertStatus(200);
    $response->assertSee('ERP Foundation Seeded');
});

test('git-pull route executes git pull and returns status 200', function () {
    $response = $this->get(route('system.git-pull'));

    $response->assertStatus(200);
    $response->assertSee('Git Pull');
});
