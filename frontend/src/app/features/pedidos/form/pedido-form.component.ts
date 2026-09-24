import { Component, OnInit, inject } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
import { PedidoService } from '@core/services/pedido.service';
import { ClienteService } from '@core/services/cliente.service';
import { Cliente, EstadoPedido } from '@core/models/gestion.model';

@Component({
  selector: 'app-pedido-form',
  templateUrl: './pedido-form.component.html',
})
export class PedidoFormComponent implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly pedidoService = inject(PedidoService);
  private readonly clienteService = inject(ClienteService);
  private readonly router = inject(Router);
  private readonly route = inject(ActivatedRoute);
  private readonly snackBar = inject(MatSnackBar);

  readonly form = this.fb.group({
    cliente_id: [null as number | null, Validators.required],
    descripcion: ['', [Validators.required, Validators.maxLength(500)]],
    total: [0, [Validators.required, Validators.min(0)]],
    estado: ['pendiente' as EstadoPedido, Validators.required],
    fecha_pedido: [new Date().toISOString().substring(0, 10), Validators.required],
  });

  readonly estadoOptions: EstadoPedido[] = ['pendiente', 'completado', 'cancelado'];

  clientes: Cliente[] = [];
  isEdit = false;
  pedidoId: number | null = null;
  loading = false;

  ngOnInit(): void {
    this.clienteService.getAll(1, 100).subscribe({
      next: ({ data }) => { this.clientes = data.data; },
    });

    const id = this.route.snapshot.paramMap.get('id');
    if (!id) return;

    this.isEdit = true;
    this.pedidoId = +id;
    this.pedidoService.getById(this.pedidoId).subscribe({
      next: ({ data }) => this.form.patchValue(data as any),
    });
  }

  submit(): void {
    if (this.form.invalid) return;
    this.loading = true;
    const data = this.form.getRawValue() as any;

    const request$ = this.isEdit
      ? this.pedidoService.update(this.pedidoId!, data)
      : this.pedidoService.create(data);

    request$.subscribe({
      next: () => {
        this.snackBar.open(this.isEdit ? 'Pedido actualizado.' : 'Pedido creado.', 'Cerrar', { duration: 3000 });
        this.router.navigate(['/pedidos']);
      },
      error: (err) => {
        this.loading = false;
        this.snackBar.open(err.error?.message ?? 'Error al guardar.', 'Cerrar', { duration: 4000 });
      },
    });
  }
}
