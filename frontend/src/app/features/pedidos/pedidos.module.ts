import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SharedModule } from '@shared/shared.module';
import { LayoutComponent } from '@shared/components/layout/layout.component';
import { PedidoListComponent } from './list/pedido-list.component';
import { PedidoFormComponent } from './form/pedido-form.component';

const routes: Routes = [
  {
    path: '',
    component: LayoutComponent,
    children: [
      { path: '', component: PedidoListComponent },
      { path: 'nuevo', component: PedidoFormComponent },
      { path: ':id/editar', component: PedidoFormComponent },
    ],
  },
];

@NgModule({
  declarations: [PedidoListComponent, PedidoFormComponent],
  imports: [SharedModule, RouterModule.forChild(routes)],
})
export class PedidosModule {}
