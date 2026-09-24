@echo off
echo === Gestion App - Arranque ===

if not exist .env (
    copy .env.example .env
    echo .env creado desde .env.example. Edita los valores antes de continuar.
    pause
    exit /b 1
)

docker-compose down --remove-orphans
docker-compose up --build -d

echo.
echo Servicios levantados:
echo   Frontend:             http://localhost:4200
echo   Auth API:             http://localhost:5001
echo   Auth Swagger:         http://localhost:5001/swagger
echo   Clientes/Pedidos API: http://localhost:8080
echo.
echo Para ver logs: docker-compose logs -f
