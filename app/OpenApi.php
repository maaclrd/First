<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'API de Produtos',
    version: '1.0.0',
    description: 'Documentação da API de gestão de produtos.'
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Servidor principal'
)]
class OpenApi {}
