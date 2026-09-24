<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use App\Services\ClienteService;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ClienteServiceTest extends TestCase
{
    private ClienteService $service;
    private MockInterface $repository;

    protected function setUp(): void
    {
        $this->repository = Mockery::mock(ClienteRepository::class);
        $this->service = new ClienteService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_listar_returns_paginated_data(): void
    {
        $expected = ['data' => [], 'total' => 0, 'page' => 1, 'per_page' => 15];
        $this->repository->shouldReceive('findAll')->once()->with(1, 15)->andReturn($expected);

        $result = $this->service->listar(1, 15);

        $this->assertSame($expected, $result);
    }

    public function test_obtener_existing_cliente_returns_array(): void
    {
        $cliente = $this->makeCliente(1, 'Ana', 'ana@test.com');
        $this->repository->shouldReceive('findById')->once()->with(1)->andReturn($cliente);

        $result = $this->service->obtener(1);

        $this->assertSame('Ana', $result['nombre']);
    }

    public function test_obtener_nonexistent_throws_exception(): void
    {
        $this->repository->shouldReceive('findById')->once()->with(99)->andReturn(null);

        $this->expectException(RuntimeException::class);
        $this->service->obtener(99);
    }

    public function test_crear_with_valid_data_returns_cliente(): void
    {
        $data = ['nombre' => 'Ana', 'email' => 'ana@test.com'];
        $cliente = $this->makeCliente(1, 'Ana', 'ana@test.com');
        $this->repository->shouldReceive('create')->once()->with($data)->andReturn($cliente);

        $result = $this->service->crear($data);

        $this->assertSame('Ana', $result['nombre']);
    }

    public function test_crear_with_invalid_email_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->crear(['nombre' => 'Ana', 'email' => 'not-an-email']);
    }

    public function test_crear_with_empty_nombre_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->crear(['nombre' => '', 'email' => 'ana@test.com']);
    }

    public function test_eliminar_existing_client_succeeds(): void
    {
        $this->repository->shouldReceive('delete')->once()->with(1)->andReturn(true);
        $this->service->eliminar(1);
        $this->assertTrue(true);
    }

    public function test_eliminar_nonexistent_throws_exception(): void
    {
        $this->repository->shouldReceive('delete')->once()->with(99)->andReturn(false);

        $this->expectException(RuntimeException::class);
        $this->service->eliminar(99);
    }

    private function makeCliente(int $id, string $nombre, string $email): Cliente
    {
        return new Cliente($id, $nombre, $email, '', '', true, '2024-01-01', '2024-01-01');
    }
}
