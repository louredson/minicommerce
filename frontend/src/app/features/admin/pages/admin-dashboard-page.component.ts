import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { AlertService } from '../../../core/services/alert.service';

@Component({
  standalone: false,
  selector: 'app-admin-dashboard-page',
  templateUrl: './admin-dashboard-page.component.html'
})
export class AdminDashboardPageComponent implements OnInit {
  stats: any = {};
  users: any[] = [];
  orders: any[] = [];
  categories: any[] = [];
  from = '';
  to = '';

  newProduct = {
    category_id: '',
    name: '',
    description: '',
    price: '',
    stock: '',
    image_url: ''
  };

  constructor(private http: HttpClient, private alerts: AlertService) {}

  ngOnInit(): void {
    this.load();
    this.http.get<any>('/api/categories', { withCredentials: true }).subscribe((res) => (this.categories = res.data || []));
  }

  private buildParams(): HttpParams {
    let params = new HttpParams();
    if (this.from) params = params.set('from', this.from);
    if (this.to) params = params.set('to', this.to);
    return params;
  }

  load() {
    this.http.get<any>('/api/admin/dashboard', { withCredentials: true }).subscribe((res) => (this.stats = res.data));
    this.http.get<any>('/api/admin/users', { withCredentials: true }).subscribe((res) => (this.users = res.data));
    this.http.get<any>('/api/admin/orders', { params: this.buildParams(), withCredentials: true }).subscribe((res) => (this.orders = res.data));
  }

  downloadPdf() {
    const q = new URLSearchParams();
    if (this.from) q.set('from', this.from);
    if (this.to) q.set('to', this.to);
    const suffix = q.toString() ? `?${q.toString()}` : '';
    window.open(`/api/admin/orders/report.pdf${suffix}`, '_blank');
  }

  addProduct() {
    const payload = {
      ...this.newProduct,
      category_id: Number(this.newProduct.category_id),
      price: Number(this.newProduct.price),
      stock: Number(this.newProduct.stock)
    };

    this.http.post<any>('/api/admin/products', payload, { withCredentials: true }).subscribe({
      next: (res) => {
        this.alerts.show('success', res.message || 'Produto criado.');
        this.newProduct = { category_id: '', name: '', description: '', price: '', stock: '', image_url: '' };
        this.load();
      },
      error: (err) => this.alerts.show('danger', err.error?.message || 'Falha ao criar produto.')
    });
  }

  updateOrderStatus(order: any, status: string) {
    this.http.put<any>('/api/admin/orders/status', { order_id: order.id, status }, { withCredentials: true }).subscribe({
      next: (res) => {
        this.alerts.show('info', res.message || 'Status atualizado.');
        this.load();
      },
      error: (err) => this.alerts.show('danger', err.error?.message || 'Falha ao atualizar status.')
    });
  }

  toggleUser(user: any) {
    this.http.put<any>('/api/admin/users/toggle', { user_id: user.id }, { withCredentials: true }).subscribe({
      next: (res) => {
        this.alerts.show('info', res.message || 'Usuario atualizado.');
        this.load();
      },
      error: (err) => this.alerts.show('danger', err.error?.message || 'Falha ao atualizar usuario.')
    });
  }
}
