import { Component, OnInit, inject } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ClienteService } from '@core/services/cliente.service';

@Component({
  selector: 'app-cliente-form',
  templateUrl: './cliente-form.component.html',
})
export class ClienteFormComponent implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly clienteService = inject(ClienteService);
  private readonly router = inject(Router);
  private readonly route = inject(ActivatedRoute);
  private readonly snackBar = inject(MatSnackBar);

  readonly form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(100)]],
    email: ['', [Validators.required, Validators.email]],
    telefono: [''],
    direccion: [''],
  });

  isEdit = false;
  clienteId: number | null = null;
  loading = false;

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) return;

    this.isEdit = true;
    this.clienteId = +id;
    this.clienteService.getById(this.clienteId).subscribe({
      next: ({ data }) => this.form.patchValue(data),
    });
  }

  submit(): void {
    if (this.form.invalid) return;
    this.loading = true;
    const data = this.form.getRawValue() as any;

    const request$ = this.isEdit
      ? this.clienteService.update(this.clienteId!, data)
      : this.clienteService.create(data);

    request$.subscribe({
      next: () => {
        this.snackBar.open(this.isEdit ? 'Cliente actualizado.' : 'Cliente creado.', 'Cerrar', { duration: 3000 });
        this.router.navigate(['/clientes']);
      },
      error: (err) => {
        this.loading = false;
        this.snackBar.open(err.error?.message ?? 'Error al guardar.', 'Cerrar', { duration: 4000 });
      },
    });
  }
}
