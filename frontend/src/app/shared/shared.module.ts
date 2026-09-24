import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ReactiveFormsModule } from '@angular/forms';
import { MaterialModule } from './material.module';
import { ConfirmDialogComponent } from './components/confirm-dialog/confirm-dialog.component';
import { LayoutModule } from './components/layout/layout.module';
import { EstadoPedidoPipe } from './pipes/estado-pedido.pipe';

@NgModule({
  declarations: [ConfirmDialogComponent, EstadoPedidoPipe],
  imports: [CommonModule, RouterModule, ReactiveFormsModule, MaterialModule, LayoutModule],
  exports: [
    CommonModule, RouterModule, ReactiveFormsModule, MaterialModule,
    LayoutModule, ConfirmDialogComponent, EstadoPedidoPipe,
  ],
})
export class SharedModule {}
