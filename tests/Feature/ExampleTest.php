<?php

test('guest diarahkan ke halaman login', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});
