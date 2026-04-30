<?php

it('redireciona para login ao acessar dashboard sem autenticação', function () {
    $this->get('/dashboard')
        ->assertRedirect('/login');
});
