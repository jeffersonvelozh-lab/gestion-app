INSERT INTO "Clientes" (nombre, email, telefono, direccion, activo) VALUES
('Juan Pérez',       'juan.perez@example.com',    '0991234567', 'Av. Principal 123, Quito',        TRUE),
('María García',     'maria.garcia@example.com',  '0987654321', 'Calle 5 de Junio 456, Guayaquil', TRUE),
('Carlos López',     'carlos.lopez@example.com',  '0976543210', 'Av. América 789, Cuenca',         TRUE),
('Ana Martínez',     'ana.martinez@example.com',  '0965432109', 'Calle Bolívar 321, Loja',         TRUE),
('Pedro Rodríguez',  'pedro.rodriguez@example.com','0954321098', 'Av. Malecón 654, Manta',         TRUE)
ON CONFLICT (email) DO NOTHING;

INSERT INTO "Pedidos" (cliente_id, descripcion, total, estado, fecha_pedido)
SELECT c.id, 'Pedido de laptops empresariales', 2500.00, 'completado', NOW() - INTERVAL '30 days'
FROM "Clientes" c WHERE c.email = 'juan.perez@example.com' ON CONFLICT DO NOTHING;

INSERT INTO "Pedidos" (cliente_id, descripcion, total, estado, fecha_pedido)
SELECT c.id, 'Suministros de oficina Q3', 450.00, 'pendiente', NOW() - INTERVAL '10 days'
FROM "Clientes" c WHERE c.email = 'juan.perez@example.com' ON CONFLICT DO NOTHING;

INSERT INTO "Pedidos" (cliente_id, descripcion, total, estado, fecha_pedido)
SELECT c.id, 'Mobiliario para sala de reuniones', 3200.00, 'completado', NOW() - INTERVAL '25 days'
FROM "Clientes" c WHERE c.email = 'maria.garcia@example.com' ON CONFLICT DO NOTHING;

INSERT INTO "Pedidos" (cliente_id, descripcion, total, estado, fecha_pedido)
SELECT c.id, 'Equipos de red y switches', 5600.00, 'completado', NOW() - INTERVAL '45 days'
FROM "Clientes" c WHERE c.email = 'carlos.lopez@example.com' ON CONFLICT DO NOTHING;

INSERT INTO "Pedidos" (cliente_id, descripcion, total, estado, fecha_pedido)
SELECT c.id, 'Tablets para equipo de ventas', 1200.00, 'pendiente', NOW() - INTERVAL '2 days'
FROM "Clientes" c WHERE c.email = 'ana.martinez@example.com' ON CONFLICT DO NOTHING;

INSERT INTO "Pedidos" (cliente_id, descripcion, total, estado, fecha_pedido)
SELECT c.id, 'Servidores de respaldo', 8900.00, 'completado', NOW() - INTERVAL '60 days'
FROM "Clientes" c WHERE c.email = 'pedro.rodriguez@example.com' ON CONFLICT DO NOTHING;
