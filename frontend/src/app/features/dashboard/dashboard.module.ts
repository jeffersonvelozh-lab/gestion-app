import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { NgChartsModule } from 'ng2-charts';
import { SharedModule } from '@shared/shared.module';
import { LayoutComponent } from '@shared/components/layout/layout.component';
import { LayoutModule } from '@shared/components/layout/layout.module';
import { DashboardComponent } from './dashboard.component';

const routes: Routes = [
  {
    path: '',
    component: LayoutComponent,
    children: [{ path: '', component: DashboardComponent }],
  },
];

@NgModule({
  declarations: [DashboardComponent],
  imports: [SharedModule, LayoutModule, NgChartsModule, RouterModule.forChild(routes)],
})
export class DashboardModule {}
