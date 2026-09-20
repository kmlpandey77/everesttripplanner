<?php

test('an unresolvable path returns a 404', function () {
    $response = $this->get('/');

    $response->assertStatus(404);
});
