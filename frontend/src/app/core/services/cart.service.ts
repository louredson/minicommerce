import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class CartService {
  private readonly base = environment.apiBase;

  constructor(private http: HttpClient) {}

  add(productId: number) { return this.http.post<any>(`${this.base}/cart/add`, { product_id: productId }, { withCredentials: true }); }
  summary() { return this.http.get<any>(`${this.base}/cart`, { withCredentials: true }); }
  update(productId: number, qty: number) { return this.http.put<any>(`${this.base}/cart/update`, { product_id: productId, qty }, { withCredentials: true }); }
  remove(productId: number) { return this.http.request<any>('DELETE', `${this.base}/cart/remove`, { body: { product_id: productId }, withCredentials: true }); }
  clear() { return this.http.request<any>('DELETE', `${this.base}/cart/clear`, { withCredentials: true }); }
  checkout() { return this.http.post<any>(`${this.base}/checkout`, {}, { withCredentials: true }); }
}
