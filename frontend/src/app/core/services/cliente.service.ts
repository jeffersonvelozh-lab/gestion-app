import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '@env/environment';
import { ApiResponse, Cliente, ClienteForm, PaginatedResponse } from '@core/models/gestion.model';

@Injectable({ providedIn: 'root' })
export class ClienteService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = `${environment.clientesApiUrl}/clientes`;

  getAll(page = 1, perPage = 15): Observable<ApiResponse<PaginatedResponse<Cliente>>> {
    const params = new HttpParams().set('page', page).set('per_page', perPage);
    return this.http.get<ApiResponse<PaginatedResponse<Cliente>>>(this.baseUrl, { params });
  }

  getById(id: number): Observable<ApiResponse<Cliente>> {
    return this.http.get<ApiResponse<Cliente>>(`${this.baseUrl}/${id}`);
  }

  create(data: ClienteForm): Observable<ApiResponse<Cliente>> {
    return this.http.post<ApiResponse<Cliente>>(this.baseUrl, data);
  }

  update(id: number, data: ClienteForm): Observable<ApiResponse<Cliente>> {
    return this.http.put<ApiResponse<Cliente>>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<ApiResponse<null>> {
    return this.http.delete<ApiResponse<null>>(`${this.baseUrl}/${id}`);
  }
}
