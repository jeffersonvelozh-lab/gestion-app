<?php

declare(strict_types=1);

use App\Controllers\ClienteController;
use App\Controllers\PedidoController;
use App\Middleware\JwtMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app): void {
    $app->get('/health', fn($req, $res) => $res->withStatus(200));

    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->group('/clientes', function (RouteCollectorProxy $g) {
            $g->get('', [ClienteController::class, 'index']);
            $g->post('', [ClienteController::class, 'store']);
            $g->get('/{id:\d+}', [ClienteController::class, 'show']);
            $g->put('/{id:\d+}', [ClienteController::class, 'update']);
            $g->delete('/{id:\d+}', [ClienteController::class, 'destroy']);
        });

        $group->group('/pedidos', function (RouteCollectorProxy $g) {
            $g->get('', [PedidoController::class, 'index']);
            $g->post('', [PedidoController::class, 'store']);
            $g->get('/{id:\d+}', [PedidoController::class, 'show']);
            $g->put('/{id:\d+}', [PedidoController::class, 'update']);
            $g->delete('/{id:\d+}', [PedidoController::class, 'destroy']);
            $g->patch('/{id:\d+}/completar', [PedidoController::class, 'completar']);
            $g->patch('/{id:\d+}/cancelar', [PedidoController::class, 'cancelar']);
        });

        $group->get('/estadisticas', [PedidoController::class, 'estadisticas']);
    })->add(JwtMiddleware::class);
};
