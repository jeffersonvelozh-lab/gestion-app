export interface Cliente {
  id: number;
  nombre: string;
  email: string;
  telefono: string;
  direccion: string;
  activo: boolean;
  created_at: string;
  updated_at: string;
}

export interface ClienteForm {
  nombre: string;
  email: string;
  telefono: string;
  direccion: string;
}

export type EstadoPedido = 'pendiente' | 'completado' | 'cancelado';

export interface Pedido {
  id: number;
  cliente_id: number;
  cliente_nombre?: string;
  descripcion: string;
  total: number;
  estado: EstadoPedido;
  fecha_pedido: string;
  created_at: string;
  updated_at: string;
}

export interface PedidoForm {
  cliente_id: number;
  descripcion: string;
  total: number;
  estado: EstadoPedido;
  fecha_pedido: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  page: number;
  per_page: number;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface Estadisticas {
  total_pedidos: number;
  completados: number;
  pendientes: number;
  cancelados: number;
  clientes_activos: number;
  actividad_por_dia: { fecha: string; total: number }[];
}
