import { Component, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { CatalogService } from '../../../core/services/catalog.service';
import { CartService } from '../../../core/services/cart.service';
import { AlertService } from '../../../core/services/alert.service';
import { SessionService } from '../../../core/services/session.service';
import { Product } from '../../../shared/models/types';

@Component({
  standalone: false,
  selector: 'app-product-detail-page',
  templateUrl: './product-detail-page.component.html'
})
export class ProductDetailPageComponent implements OnInit {
  product: Product | null = null;

  constructor(
    private route: ActivatedRoute,
    private catalog: CatalogService,
    private cart: CartService,
    private alerts: AlertService,
    private location: Location,
    public session: SessionService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (!id) return;
    this.catalog.productById(id).subscribe((res) => (this.product = res.data));
  }

  back() {
    this.location.back();
  }

  buyNow() {
    if (!this.product) return;
    this.cart.add(this.product.id).subscribe({
      next: () => this.alerts.show('success', 'Produto adicionado ao carrinho'),
      error: (err) => this.alerts.show('danger', err.error?.message ?? 'Falha ao adicionar.')
    });
  }

  imageSrc(url: string | undefined | null): string {
    if (!url) return '';
    return `/api/image-proxy?url=${encodeURIComponent(url)}`;
  }
}

