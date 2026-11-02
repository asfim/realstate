<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

test('admin login page renders successfully with demo credentials', function () {
    $response = $this->get(route('admin.login'));

    $response->assertStatus(200);
    $response->assertSee('VIP Master Key Pass');
    $response->assertSee('admin@example.com');
    $response->assertSee('password');
});

test('admin can login with demo credentials', function () {
    $admin = Admin::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
        ]
    );

    $response = $this->post(route('admin.login.submit'), [
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin, 'admin');
});

test('admin login fails with invalid credentials', function () {
    $response = $this->from(route('admin.login'))->post(route('admin.login.submit'), [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('admin.login'));
    $response->assertSessionHas('error');
    $this->assertGuest('admin');
});

test('logged in admin sees dashboard with sidebar menus', function () {
    $admin = Admin::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
        ]
    );

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee('Projects');
    $response->assertSee('Properties');
    $response->assertSee('Homepage Settings');
});

