import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '@env/environment';
import {
  ApiResponse,
  Estadisticas,
  EstadoPedido,
  PaginatedResponse,
  Pedido,
  PedidoForm,
} from '@core/models/gestion.model';

export interface PedidoFilters {
  estado?: EstadoPedido;
  cliente_id?: number;
  fecha_desde?: string;
  fecha_hasta?: string;
  page?: number;
  per_page?: number;
}

@Injectable({ providedIn: 'root' })
export class PedidoService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = `${environment.clientesApiUrl}/pedidos`;

  getAll(filters: PedidoFilters = {}): Observable<ApiResponse<PaginatedResponse<Pedido>>> {
    let params = new HttpParams();
    Object.entries(filters).forEach(([key, val]) => {
      if (val !== undefined && val !== null && val !== '') {
        params = params.set(key, String(val));
      }
    });
    return this.http.get<ApiResponse<PaginatedResponse<Pedido>>>(this.baseUrl, { params });
  }

  getById(id: number): Observable<ApiResponse<Pedido>> {
    return this.http.get<ApiResponse<Pedido>>(`${this.baseUrl}/${id}`);
  }

  create(data: PedidoForm): Observable<ApiResponse<Pedido>> {
    return this.http.post<ApiResponse<Pedido>>(this.baseUrl, data);
  }

  update(id: number, data: PedidoForm): Observable<ApiResponse<Pedido>> {
    return this.http.put<ApiResponse<Pedido>>(`${this.baseUrl}/${id}`, data);
  }

  completar(id: number): Observable<ApiResponse<Pedido>> {
    return this.http.patch<ApiResponse<Pedido>>(`${this.baseUrl}/${id}/completar`, {});
  }

  cancelar(id: number): Observable<ApiResponse<Pedido>> {
    return this.http.patch<ApiResponse<Pedido>>(`${this.baseUrl}/${id}/cancelar`, {});
  }

  delete(id: number): Observable<ApiResponse<null>> {
    return this.http.delete<ApiResponse<null>>(`${this.baseUrl}/${id}`);
  }

  getEstadisticas(): Observable<ApiResponse<Estadisticas>> {
    return this.http.get<ApiResponse<Estadisticas>>(`${environment.clientesApiUrl}/estadisticas`);
  }
}
