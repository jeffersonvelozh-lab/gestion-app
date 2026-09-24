<?php

declare(strict_types=1);

namespace App\Models;

class Cliente
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly string $email,
        public readonly string $telefono,
        public readonly string $direccion,
        public readonly bool $activo,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            nombre: $data['nombre'],
            email: $data['email'],
            telefono: $data['telefono'] ?? '',
            direccion: $data['direccion'] ?? '',
            activo: (bool)$data['activo'],
            createdAt: $data['created_at'] ?? '',
            updatedAt: $data['updated_at'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'activo' => $this->activo,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
