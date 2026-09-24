import { Component, OnInit, inject } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { PageEvent } from '@angular/material/paginator';
import { Router } from '@angular/router';
import { ClienteService } from '@core/services/cliente.service';
import { Cliente } from '@core/models/gestion.model';
import { ConfirmDialogComponent } from '@shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-cliente-list',
  templateUrl: './cliente-list.component.html',
})
export class ClienteListComponent implements OnInit {
  private readonly clienteService = inject(ClienteService);
  private readonly dialog = inject(MatDialog);
  private readonly snackBar = inject(MatSnackBar);
  private readonly router = inject(Router);

  readonly displayedColumns = ['nombre', 'email', 'telefono', 'activo', 'acciones'];

  clientes: Cliente[] = [];
  total = 0;
  page = 1;
  perPage = 10;
  loading = false;

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading = true;
    this.clienteService.getAll(this.page, this.perPage).subscribe({
      next: ({ data }) => {
        this.clientes = data.data;
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
    this.router.navigate(['/clientes', id, 'editar']);
  }

  delete(cliente: Cliente): void {
    this.dialog.open(ConfirmDialogComponent, {
      data: {
        title: 'Eliminar cliente',
        message: `¿Eliminar a ${cliente.nombre}? Esta acción no se puede deshacer.`,
        confirmLabel: 'Eliminar',
      },
    }).afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.clienteService.delete(cliente.id).subscribe({
        next: () => {
          this.snackBar.open('Cliente eliminado.', 'Cerrar', { duration: 3000 });
          this.load();
        },
        error: () => this.snackBar.open('Error al eliminar.', 'Cerrar', { duration: 3000 }),
      });
    });
  }
}
