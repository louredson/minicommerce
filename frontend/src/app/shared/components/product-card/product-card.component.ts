import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Product } from '../../models/types';

@Component({
  standalone: false,
  selector: 'app-product-card',
  templateUrl: './product-card.component.html'
})
export class ProductCardComponent {
  @Input() product!: Product;
  @Input() canBuy = true;
  @Output() add = new EventEmitter<number>();
  @Output() view = new EventEmitter<number>();

  openDetail() {
    this.view.emit(this.product.id);
  }

  addToCart(event: MouseEvent) {
    event.stopPropagation();
    this.add.emit(this.product.id);
  }
}
