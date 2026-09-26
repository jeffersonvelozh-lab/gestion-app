# Comandos Docker — Gestión de Clientes y Pedidos

Todos los comandos se ejecutan desde la raíz del proyecto (`gestion-app/`).

## Requisitos previos

```bash
# Crear el archivo de variables de entorno (solo la primera vez)
cp .env.example .env
# Editar .env con tus valores antes de levantar
```

---

## Levantar servicios

```bash
# Levantar todos los servicios
docker compose up -d

# Levantar todos los servicios con rebuild de imágenes
# (usar después de cambiar código)
docker compose up --build -d
```

### Levantar un servicio específico

```bash
docker compose up -d postgres
docker compose up -d auth-service
docker compose up -d clientes-pedidos-service
docker compose up -d frontend
```

### Rebuild de un servicio específico

```bash
docker compose up --build -d auth-service
docker compose up --build -d clientes-pedidos-service
docker compose up --build -d frontend
```

> `postgres` no requiere rebuild ya que usa una imagen oficial sin modificaciones.

---

## Detener servicios

```bash
# Detener todos los servicios (conserva los datos)
docker compose down

# Detener y eliminar volúmenes (⚠️ borra la base de datos)
docker compose down -v

# Detener un servicio específico
docker compose stop auth-service
docker compose stop clientes-pedidos-service
```

---

## Estado y logs

```bash
# Ver estado de todos los contenedores
docker compose ps

# Ver logs de un servicio
docker compose logs auth-service
docker compose logs clientes-pedidos-service
docker compose logs frontend
docker compose logs postgres

# Ver logs en tiempo real (Ctrl+C para salir)
docker compose logs -f auth-service
docker compose logs -f clientes-pedidos-service
```

---

## Otros comandos útiles

```bash
# Reiniciar un servicio sin rebuild
docker compose restart clientes-pedidos-service

# Entrar al contenedor PHP (para depuración)
docker compose exec clientes-pedidos-service bash

# Entrar al contenedor .NET
docker compose exec auth-service sh

# Conectarse a la base de datos PostgreSQL
docker compose exec postgres psql -U gestion_user -d GestionDB
```

---

## URLs de acceso

| Servicio             | URL                            |
|----------------------|--------------------------------|
| Frontend             | http://localhost:4200          |
| Auth API             | http://localhost:5001          |
| Clientes/Pedidos API | http://localhost:8080          |
| Swagger Auth         | http://localhost:5001/swagger  |
| Swagger Clientes     | http://localhost:8080/api/docs |
| PostgreSQL           | localhost:5433                 |

---

## Nombres de servicios en docker-compose.yml

| Nombre en compose         | Contenedor                  |
|---------------------------|-----------------------------|
| `postgres`                | gestion_postgres            |
| `auth-service`            | gestion_auth                |
| `clientes-pedidos-service`| gestion_clientes_pedidos    |
| `frontend`                | gestion_frontend            |
