CREATE TABLE IF NOT EXISTS "Users" (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL UNIQUE,
    password_hash TEXT          NOT NULL,
    role        VARCHAR(20)     NOT NULL DEFAULT 'user',
    is_active   BOOLEAN         NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS "Clientes" (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL UNIQUE,
    telefono    VARCHAR(30)     NOT NULL DEFAULT '',
    direccion   VARCHAR(250)    NOT NULL DEFAULT '',
    activo      BOOLEAN         NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS "Pedidos" (
    id              SERIAL PRIMARY KEY,
    cliente_id      INT             NOT NULL REFERENCES "Clientes"(id),
    descripcion     VARCHAR(500)    NOT NULL,
    total           NUMERIC(10,2)   NOT NULL DEFAULT 0.00,
    estado          VARCHAR(20)     NOT NULL DEFAULT 'pendiente',
    fecha_pedido    DATE            NOT NULL DEFAULT CURRENT_DATE,
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    CONSTRAINT ck_pedidos_estado CHECK (estado IN ('pendiente', 'completado', 'cancelado')),
    CONSTRAINT ck_pedidos_total  CHECK (total >= 0)
);

CREATE INDEX IF NOT EXISTS ix_pedidos_cliente_id  ON "Pedidos" (cliente_id);
CREATE INDEX IF NOT EXISTS ix_pedidos_estado      ON "Pedidos" (estado);
CREATE INDEX IF NOT EXISTS ix_pedidos_fecha       ON "Pedidos" (fecha_pedido);
