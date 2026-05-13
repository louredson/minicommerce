import { Component, OnInit } from '@angular/core';
import { CartService } from '../../../core/services/cart.service';
import { AlertService } from '../../../core/services/alert.service';

@Component({
  standalone: false,
  selector: 'app-cart-page',
  templateUrl: './cart-page.component.html'
})
export class CartPageComponent implements OnInit {
  items: any[] = [];
  total = 0;

  constructor(private cart: CartService, private alerts: AlertService) {}

  ngOnInit(): void { this.load(); }

  load() {
    this.cart.summary().subscribe((res) => {
      this.items = res.data.items;
      this.total = res.data.total;
    });
  }

  update(item: any) {
    this.cart.update(item.id, item.qty).subscribe({
      next: () => this.load(),
      error: (err) => {
        this.alerts.show('danger', err.error?.message ?? 'Falha ao atualizar quantidade.');
        this.load();
      }
    });
  }

  remove(item: any) {
    this.cart.remove(item.id).subscribe({
      next: (res) => { this.alerts.show('info', res.message || 'Produto removido.'); this.load(); }
    });
  }

  clearCart() {
    this.cart.clear().subscribe({
      next: (res) => { this.alerts.show('warning', res.message || 'Carrinho limpo.'); this.load(); }
    });
  }

  checkout() {
    this.cart.checkout().subscribe({
      next: (res) => { this.alerts.show('success', res.message ?? 'Compra concluida.'); this.load(); },
      error: (err) => this.alerts.show('danger', err.error?.message ?? 'Falha no checkout.')
    });
  }
}
