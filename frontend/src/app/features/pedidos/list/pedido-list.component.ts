import { Component, OnInit, inject } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { PageEvent } from '@angular/material/paginator';
import { Router } from '@angular/router';
import { PedidoService } from '@core/services/pedido.service';
import { EstadoPedido, Pedido } from '@core/models/gestion.model';
import { ConfirmDialogComponent } from '@shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-pedido-list',
  templateUrl: './pedido-list.component.html',
})
export class PedidoListComponent implements OnInit {
  private readonly pedidoService = inject(PedidoService);
  private readonly dialog = inject(MatDialog);
  private readonly snackBar = inject(MatSnackBar);
  private readonly router = inject(Router);
  private readonly fb = inject(FormBuilder);

  readonly displayedColumns = ['descripcion', 'cliente', 'total', 'estado', 'fecha', 'acciones'];
  readonly estadoOptions: EstadoPedido[] = ['pendiente', 'completado', 'cancelado'];

  readonly filters = this.fb.group({
    estado: [''],
    fecha_desde: [''],
    fecha_hasta: [''],
  });

  pedidos: Pedido[] = [];
  total = 0;
  page = 1;
  perPage = 10;
  loading = false;

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading = true;
    const f = this.filters.getRawValue();
    this.pedidoService.getAll({
      ...f,
      estado: (f.estado as EstadoPedido) || undefined,
      fecha_desde: f.fecha_desde ?? undefined,
      fecha_hasta: f.fecha_hasta ?? undefined,
      page: this.page,
      per_page: this.perPage,
    }).subscribe({
      next: ({ data }) => {
        this.pedidos = data.data;
        this.total = data.total;
        this.loading = false;
      },
      error: () => { this.loading = false; },
    });
  }

  onPageChange(event: PageEvent): void {
    this.page = event.pageIndex + 1;
    this.perPage = event.pageSize;
    this.load();
  }

  edit(id: number): void {
    this.router.navigate(['/pedidos', id, 'editar']);
  }

  completar(pedido: Pedido): void {
    this.pedidoService.completar(pedido.id).subscribe({
      next: () => { this.snackBar.open('Pedido completado.', 'Cerrar', { duration: 3000 }); this.load(); },
      error: (err) => this.snackBar.open(err.error?.message ?? 'Error.', 'Cerrar', { duration: 3000 }),
    });
  }

  cancelar(pedido: Pedido): void {
    this.dialog.open(ConfirmDialogComponent, {
      data: { title: 'Cancelar pedido', message: '¿Cancelar este pedido?', confirmLabel: 'Cancelar pedido' },
    }).afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.pedidoService.cancelar(pedido.id).subscribe({
        next: () => { this.snackBar.open('Pedido cancelado.', 'Cerrar', { duration: 3000 }); this.load(); },
        error: (err) => this.snackBar.open(err.error?.message ?? 'Error.', 'Cerrar', { duration: 3000 }),
      });
    });
  }

  delete(pedido: Pedido): void {
    this.dialog.open(ConfirmDialogComponent, {
      data: { title: 'Eliminar pedido', message: '¿Eliminar este pedido? Esta acción no se puede deshacer.', confirmLabel: 'Eliminar' },
    }).afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.pedidoService.delete(pedido.id).subscribe({
        next: () => { this.snackBar.open('Pedido eliminado.', 'Cerrar', { duration: 3000 }); this.load(); },
        error: () => this.snackBar.open('Error al eliminar.', 'Cerrar', { duration: 3000 }),
      });
    });
  }

  getEstadoColor(estado: EstadoPedido): string {
    const colors: Record<EstadoPedido, string> = { pendiente: 'accent', completado: 'primary', cancelado: 'warn' };
    return colors[estado];
  }
}
