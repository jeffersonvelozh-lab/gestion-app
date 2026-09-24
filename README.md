# Gestión de Clientes y Pedidos

Aplicación web de gestión de clientes y pedidos con arquitectura de microservicios.

## Stack Tecnológico

- Frontend: Angular 17 + Angular Material
- Auth Service: .NET 8 + JWT
- Clientes/Pedidos Service: PHP 8 + Slim Framework
- Base de datos: SQL Server 2022
- Contenerización: Docker + Docker Compose

## Estructura del Proyecto

```
gestion-app/
├── auth-service/          # Microservicio .NET 8 (Autenticación)
├── clientes-pedidos-service/  # Microservicio PHP 8 (Clientes y Pedidos)
├── database/              # Migraciones y seeds SQL Server
├── frontend/              # Aplicación Angular 17
├── docker-compose.yml
├── .env.example
└── README.md
```

## Requisitos

- Docker Desktop 24+
- Docker Compose v2

## Arranque rápido

```bash
cp .env.example .env
# Editar .env con tus valores

docker-compose up --build
```

| Servicio               | URL                     |
|------------------------|-------------------------|
| Frontend               | http://localhost:4200   |
| Auth API               | http://localhost:5001   |
| Clientes/Pedidos API   | http://localhost:8080   |
| Swagger Auth           | http://localhost:5001/swagger |
| Swagger Clientes       | http://localhost:8080/api/docs |

## Desarrollo local (sin Docker)

Ver los README individuales en cada carpeta de servicio.

## Tests

```bash
# .NET
cd auth-service && dotnet test

# PHP
cd clientes-pedidos-service && ./vendor/bin/phpunit

# Angular
cd frontend && ng test
```
