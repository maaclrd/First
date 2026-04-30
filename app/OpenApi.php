<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'API de Produtos',
    version: '1.0.0',
    description: 'Documentação da API de gestão de produtos.'
)]
#[OA\Server(
    url: 'http://localhost:8080',
    description: 'Servidor principal'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token'
)]
class OpenApi {}
