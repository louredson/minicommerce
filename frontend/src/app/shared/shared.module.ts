import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { NavbarComponent } from './components/navbar/navbar.component';
import { AlertComponent } from './components/alert/alert.component';
import { ProductCardComponent } from './components/product-card/product-card.component';

@NgModule({
  declarations: [NavbarComponent, AlertComponent, ProductCardComponent],
  imports: [CommonModule, RouterModule, FormsModule],
  exports: [CommonModule, RouterModule, FormsModule, NavbarComponent, AlertComponent, ProductCardComponent]
})
export class SharedModule {}



