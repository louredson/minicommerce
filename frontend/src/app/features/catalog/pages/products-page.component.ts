import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CatalogService } from '../../../core/services/catalog.service';
import { CartService } from '../../../core/services/cart.service';
import { AlertService } from '../../../core/services/alert.service';
import { SessionService } from '../../../core/services/session.service';
import { Category, Product } from '../../../shared/models/types';

@Component({
  standalone: false,
  selector: 'app-products-page',
  templateUrl: './products-page.component.html'
})
export class ProductsPageComponent implements OnInit {
  categories: Category[] = [];
  products: Product[] = [];
  filteredProducts: Product[] = [];
  selectedCategory = '';
  searchTerm = '';

  constructor(
    private catalog: CatalogService,
    private cart: CartService,
    private alerts: AlertService,
    private router: Router,
    private route: ActivatedRoute,
    public session: SessionService
  ) {}

  ngOnInit(): void {
    this.catalog.categories().subscribe((res) => (this.categories = res.data));
    this.route.queryParamMap.subscribe((params) => {
      this.searchTerm = (params.get('q') || '').trim().toLowerCase();
      this.applyFilters();
    });
    this.loadProducts();
  }

  loadProducts() {
    const category = this.selectedCategory ? Number(this.selectedCategory) : undefined;
    this.catalog.products(category).subscribe((res) => {
      this.products = res.data;
      this.applyFilters();
    });
  }

  private applyFilters() {
    if (!this.searchTerm) {
      this.filteredProducts = this.products;
      return;
    }

    this.filteredProducts = this.products.filter((p) => {
      const haystack = `${p.name} ${p.category_name} ${p.description || ''}`.toLowerCase();
      return haystack.includes(this.searchTerm);
    });
  }

  addToCart(productId: number) {
    this.cart.add(productId).subscribe({
      next: () => this.alerts.show('success', 'Produto adicionado ao carrinho'),
      error: (err) => this.alerts.show('danger', err.error?.message ?? 'Falha ao adicionar.')
    });
  }

  openProduct(productId: number) {
    this.router.navigate(['/products', productId]);
  }
}
