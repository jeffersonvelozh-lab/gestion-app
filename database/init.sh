#!/bin/bash
set -e

echo "Waiting for SQL Server to be ready..."
for i in {1..30}; do
  /opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P "$SA_PASSWORD" -Q "SELECT 1" > /dev/null 2>&1 && break
  echo "Attempt $i/30..."
  sleep 3
done

echo "Running migrations..."
for f in /migrations/*.sql; do
  echo "  -> $f"
  /opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P "$SA_PASSWORD" -i "$f"
done

echo "Running seeds..."
for f in /seeds/*.sql; do
  echo "  -> $f"
  /opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P "$SA_PASSWORD" -i "$f"
done

echo "Database initialization complete."
