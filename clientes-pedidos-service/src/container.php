<?php

declare(strict_types=1);

use App\Database\Connection;
use App\Middleware\ErrorHandler;
use App\Middleware\JwtMiddleware;
use App\Repositories\ClienteRepository;
use App\Repositories\PedidoRepository;
use App\Services\ClienteService;
use App\Services\PedidoService;
use App\Controllers\ClienteController;
use App\Controllers\PedidoController;
use Psr\Container\ContainerInterface;

return [
    Connection::class => fn() => new Connection(
        host: $_ENV['DB_HOST'] ?? 'localhost',
        port: (int)($_ENV['DB_PORT'] ?? 1433),
        database: $_ENV['DB_DATABASE'] ?? 'GestionDB',
        username: $_ENV['DB_USERNAME'] ?? 'sa',
        password: $_ENV['DB_PASSWORD'] ?? ''
    ),

    ClienteRepository::class => fn(ContainerInterface $c) =>
        new ClienteRepository($c->get(Connection::class)),

    PedidoRepository::class => fn(ContainerInterface $c) =>
        new PedidoRepository($c->get(Connection::class)),

    ClienteService::class => fn(ContainerInterface $c) =>
        new ClienteService($c->get(ClienteRepository::class)),

    PedidoService::class => fn(ContainerInterface $c) =>
        new PedidoService($c->get(PedidoRepository::class), $c->get(ClienteRepository::class)),

    ClienteController::class => fn(ContainerInterface $c) =>
        new ClienteController($c->get(ClienteService::class)),

    PedidoController::class => fn(ContainerInterface $c) =>
        new PedidoController($c->get(PedidoService::class)),

    JwtMiddleware::class => fn() => new JwtMiddleware($_ENV['JWT_SECRET'] ?? '', $_ENV['JWT_ISSUER'] ?? ''),

    ErrorHandler::class => DI\create(ErrorHandler::class),
];
