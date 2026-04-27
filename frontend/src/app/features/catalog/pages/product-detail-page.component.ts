import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { CatalogService } from '../../../core/services/catalog.service';
import { Product } from '../../../shared/models/types';

@Component({
  standalone: false,
  selector: 'app-product-detail-page',
  templateUrl: './product-detail-page.component.html'
})
export class ProductDetailPageComponent implements OnInit {
  product: Product | null = null;

  constructor(private route: ActivatedRoute, private catalog: CatalogService) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (!id) return;
    this.catalog.productById(id).subscribe((res) => (this.product = res.data));
  }
}
