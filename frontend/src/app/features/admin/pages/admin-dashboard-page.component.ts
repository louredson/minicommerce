import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { AlertService } from '../../../core/services/alert.service';

type AdminSection = 'products' | 'orders' | 'users';

@Component({
  standalone: false,
  selector: 'app-admin-dashboard-page',
  templateUrl: './admin-dashboard-page.component.html'
})
export class AdminDashboardPageComponent implements OnInit {
  activeSection: AdminSection = 'products';

  stats: any = {};
  users: any[] = [];
  orders: any[] = [];
  products: any[] = [];
  categories: any[] = [];
  from = '';
  to = '';
  editingProductId: number | null = null;

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

  setSection(section: AdminSection) {
    this.activeSection = section;
  }

  private buildParams(): HttpParams {
    let params = new HttpParams();
    if (this.from) params = params.set('from', this.from);
    if (this.to) params = params.set('to', this.to);
    return params;
  }

  load() {
    this.http.get<any>('/api/admin/dashboard', { withCredentials: true }).subscribe((res) => (this.stats = res.data));

    this.http.get<any>('/api/admin/users', { withCredentials: true }).subscribe((res) => {
      const raw = res.data || [];
      this.users = raw.map((u: any) => ({
        ...u,
        isAdmin: Number(u.is_admin) === 1,
        isActive: Number(u.is_active) === 1
      }));
    });

    this.http.get<any>('/api/admin/orders', { params: this.buildParams(), withCredentials: true }).subscribe((res) => (this.orders = res.data || []));
    this.http.get<any>('/api/admin/products', { withCredentials: true }).subscribe((res) => (this.products = res.data || []));
  }

  downloadPdf() {
    const q = new URLSearchParams();
    if (this.from) q.set('from', this.from);
    if (this.to) q.set('to', this.to);
    const suffix = q.toString() ? `?${q.toString()}` : '';
    window.open(`/api/admin/orders/report.pdf${suffix}`, '_blank');
  }

  saveProduct() {
    const payload = {
      ...this.newProduct,
      id: this.editingProductId ?? undefined,
      category_id: Number(this.newProduct.category_id),
      price: Number(this.newProduct.price),
      stock: Number(this.newProduct.stock)
    };

    const request = this.editingProductId
      ? this.http.put<any>('/api/admin/products', payload, { withCredentials: true })
      : this.http.post<any>('/api/admin/products', payload, { withCredentials: true });

    request.subscribe({
      next: (res) => {
        this.alerts.show('success', res.message || (this.editingProductId ? 'Produto atualizado.' : 'Produto criado.'));
        this.newProduct = { category_id: '', name: '', description: '', price: '', stock: '', image_url: '' };
        this.editingProductId = null;
        this.load();
      },
      error: (err) => this.alerts.show('danger', err.error?.message || 'Falha ao guardar produto.')
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

  deleteProduct(product: any) {
    this.http.request<any>('delete', '/api/admin/products', { body: { id: product.id }, withCredentials: true }).subscribe({
      next: (res) => {
        this.alerts.show('warning', res.message || 'Produto removido.');
        this.load();
      },
      error: (err) => this.alerts.show('danger', err.error?.message || 'Falha ao remover produto.')
    });
  }

  editProduct(product: any) {
    this.editingProductId = Number(product.id);
    this.newProduct = {
      category_id: String(product.category_id ?? ''),
      name: product.name ?? '',
      description: product.description ?? '',
      price: String(product.price ?? ''),
      stock: String(product.stock ?? ''),
      image_url: product.image_url ?? ''
    };
  }

  cancelEdit() {
    this.editingProductId = null;
    this.newProduct = { category_id: '', name: '', description: '', price: '', stock: '', image_url: '' };
  }

  imageSrc(url: string | undefined | null): string {
    if (!url) return '';
    return `/api/image-proxy?url=${encodeURIComponent(url)}`;
  }
}
