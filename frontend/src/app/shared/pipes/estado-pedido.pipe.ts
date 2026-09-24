import { Pipe, PipeTransform } from '@angular/core';
import { EstadoPedido } from '@core/models/gestion.model';

const LABELS: Record<EstadoPedido, string> = {
  pendiente: 'Pendiente',
  completado: 'Completado',
  cancelado: 'Cancelado',
};

@Pipe({ name: 'estadoPedido' })
export class EstadoPedidoPipe implements PipeTransform {
  transform(value: EstadoPedido): string {
    return LABELS[value] ?? value;
  }
}
