import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { ApiResponse, Category, Product } from '../../shared/models/types';
import { environment } from '../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class CatalogService {
  private readonly base = environment.apiBase;

  constructor(private http: HttpClient) {}

  categories() {
    return this.http.get<ApiResponse<Category[]>>(`${this.base}/categories`, { withCredentials: true });
  }

  products(categoryId?: number) {
    let params = new HttpParams();
    if (categoryId) params = params.set('category_id', categoryId);
    return this.http.get<ApiResponse<Product[]>>(`${this.base}/products`, { params, withCredentials: true });
  }

  productById(id: number) {
    const params = new HttpParams().set('id', id);
    return this.http.get<ApiResponse<Product>>(`${this.base}/products/item`, { params, withCredentials: true });
  }
}
