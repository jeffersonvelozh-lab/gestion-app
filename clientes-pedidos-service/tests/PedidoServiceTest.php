<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Repositories\ClienteRepository;
use App\Repositories\PedidoRepository;
use App\Services\PedidoService;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PedidoServiceTest extends TestCase
{
    private PedidoService $service;
    private MockInterface $pedidoRepo;
    private MockInterface $clienteRepo;

    protected function setUp(): void
    {
        $this->pedidoRepo = Mockery::mock(PedidoRepository::class);
        $this->clienteRepo = Mockery::mock(ClienteRepository::class);
        $this->service = new PedidoService($this->pedidoRepo, $this->clienteRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_crear_with_valid_data_returns_pedido(): void
    {
        $cliente = new Cliente(1, 'Ana', 'ana@test.com', '', '', true, '2024-01-01', '2024-01-01');
        $pedido = $this->makePedido(1, 1, Pedido::ESTADO_PENDIENTE);

        $this->clienteRepo->shouldReceive('findById')->once()->with(1)->andReturn($cliente);
        $this->pedidoRepo->shouldReceive('create')->once()->andReturn($pedido);

        $data = ['cliente_id' => 1, 'descripcion' => 'Pedido test', 'total' => 100.0];
        $result = $this->service->crear($data);

        $this->assertSame(1, $result['id']);
    }

    public function test_crear_with_nonexistent_client_throws_exception(): void
    {
        $this->clienteRepo->shouldReceive('findById')->once()->with(99)->andReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->service->crear(['cliente_id' => 99, 'descripcion' => 'Test', 'total' => 10.0]);
    }

    public function test_crear_with_negative_total_throws_exception(): void
    {
        $cliente = new Cliente(1, 'Ana', 'ana@test.com', '', '', true, '2024-01-01', '2024-01-01');
        $this->clienteRepo->shouldReceive('findById')->once()->andReturn($cliente);

        $this->expectException(InvalidArgumentException::class);
        $this->service->crear(['cliente_id' => 1, 'descripcion' => 'Test', 'total' => -10.0]);
    }

    public function test_completar_changes_estado(): void
    {
        $pedido = $this->makePedido(1, 1, Pedido::ESTADO_PENDIENTE);
        $completado = $this->makePedido(1, 1, Pedido::ESTADO_COMPLETADO);

        $this->pedidoRepo->shouldReceive('findById')->once()->with(1)->andReturn($pedido);
        $this->pedidoRepo->shouldReceive('updateEstado')->once()->with(1, Pedido::ESTADO_COMPLETADO)->andReturn($completado);

        $result = $this->service->completar(1);

        $this->assertSame(Pedido::ESTADO_COMPLETADO, $result['estado']);
    }

    public function test_completar_already_completed_throws_exception(): void
    {
        $pedido = $this->makePedido(1, 1, Pedido::ESTADO_COMPLETADO);
        $this->pedidoRepo->shouldReceive('findById')->once()->with(1)->andReturn($pedido);

        $this->expectException(InvalidArgumentException::class);
        $this->service->completar(1);
    }

    private function makePedido(int $id, int $clienteId, string $estado): Pedido
    {
        return new Pedido($id, $clienteId, 'Test', 100.0, $estado, '2024-01-01', '2024-01-01', '2024-01-01');
    }
}
