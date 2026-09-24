import { Component, inject } from '@angular/core';
import { AuthService } from '@core/services/auth.service';

interface NavItem {
  label: string;
  icon: string;
  route: string;
}

@Component({
  selector: 'app-layout',
  templateUrl: './layout.component.html',
})
export class LayoutComponent {
  private readonly authService = inject(AuthService);

  readonly user$ = this.authService.user$;

  readonly navItems: NavItem[] = [
    { label: 'Dashboard',  icon: 'dashboard',    route: '/dashboard'  },
    { label: 'Clientes',   icon: 'people',        route: '/clientes'   },
    { label: 'Pedidos',    icon: 'shopping_cart', route: '/pedidos'    },
  ];

  logout(): void {
    this.authService.logout();
  }
}
