<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: "Booking API", version: "1.0.0")]
#[OA\Server(url: 'http://localhost', description: 'Local server')]
abstract class Controller
{
    #[OA\Get(path: '/api/test', responses: [new OA\Response(response: 200, description: 'OK')])]
    public function test() {}
}
