<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('contact page loads successfully with property types', function () {
    \App\Models\PropertyType::firstOrCreate(
        ['title' => 'Apartment'],
        ['icon_class' => 'fa-solid fa-building']
    );

    $response = $this->get('/contact');

    $response->assertStatus(200);
    $response->assertSee('Apartment');
    $response->assertSee('Property Type');
});
