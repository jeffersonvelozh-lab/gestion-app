#!/bin/bash
set -e

echo "=== Gestion App - Arranque ==="

if [ ! -f .env ]; then
  cp .env.example .env
  echo ".env creado. Edita los valores y vuelve a ejecutar."
  exit 1
fi

docker-compose down --remove-orphans
docker-compose up --build -d

echo ""
echo "Servicios levantados:"
echo "  Frontend:             http://localhost:4200"
echo "  Auth API:             http://localhost:5001"
echo "  Auth Swagger:         http://localhost:5001/swagger"
echo "  Clientes/Pedidos API: http://localhost:8080"
echo ""
echo "Logs: docker-compose logs -f"
