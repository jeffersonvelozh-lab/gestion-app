import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SharedModule } from '@shared/shared.module';
import { LayoutComponent } from '@shared/components/layout/layout.component';
import { ClienteListComponent } from './list/cliente-list.component';
import { ClienteFormComponent } from './form/cliente-form.component';

const routes: Routes = [
  {
    path: '',
    component: LayoutComponent,
    children: [
      { path: '', component: ClienteListComponent },
      { path: 'nuevo', component: ClienteFormComponent },
      { path: ':id/editar', component: ClienteFormComponent },
    ],
  },
];

@NgModule({
  declarations: [ClienteListComponent, ClienteFormComponent],
  imports: [SharedModule, RouterModule.forChild(routes)],
})
export class ClientesModule {}
