import { Component, OnInit, inject } from '@angular/core';
import { ChartConfiguration } from 'chart.js';
import { PedidoService } from '@core/services/pedido.service';
import { Estadisticas } from '@core/models/gestion.model';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
})
export class DashboardComponent implements OnInit {
  private readonly pedidoService = inject(PedidoService);

  estadisticas: Estadisticas | null = null;
  loading = true;

  lineChartData: ChartConfiguration<'line'>['data'] = {
    labels: [],
    datasets: [{ data: [], label: 'Pedidos por día', fill: false, tension: 0.4, borderColor: '#3f51b5' }],
  };

  lineChartOptions: ChartConfiguration<'line'>['options'] = {
    responsive: true,
    plugins: { legend: { display: true } },
  };

  doughnutChartData: ChartConfiguration<'doughnut'>['data'] = {
    labels: ['Completados', 'Pendientes', 'Cancelados'],
    datasets: [{ data: [0, 0, 0], backgroundColor: ['#4caf50', '#ff9800', '#f44336'] }],
  };

  ngOnInit(): void {
    this.pedidoService.getEstadisticas().subscribe({
      next: ({ data }) => {
        this.estadisticas = data;
        this.lineChartData = {
          labels: data.actividad_por_dia.map(d => d.fecha),
          datasets: [{
            data: data.actividad_por_dia.map(d => d.total),
            label: 'Pedidos por día',
            fill: false,
            tension: 0.4,
            borderColor: '#3f51b5',
          }],
        };
        this.doughnutChartData = {
          labels: ['Completados', 'Pendientes', 'Cancelados'],
          datasets: [{ data: [data.completados, data.pendientes, data.cancelados], backgroundColor: ['#4caf50', '#ff9800', '#f44336'] }],
        };
        this.loading = false;
      },
      error: () => { this.loading = false; },
    });
  }
}
