import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginPageComponent } from './features/auth/pages/login-page.component';
import { RegisterPageComponent } from './features/auth/pages/register-page.component';
import { ForgotPasswordPageComponent } from './features/auth/pages/forgot-password-page.component';
import { ResetPasswordPageComponent } from './features/auth/pages/reset-password-page.component';
import { ProductsPageComponent } from './features/catalog/pages/products-page.component';
import { ProductDetailPageComponent } from './features/catalog/pages/product-detail-page.component';
import { CartPageComponent } from './features/cart/pages/cart-page.component';
import { ProfilePageComponent } from './features/profile/pages/profile-page.component';
import { AdminDashboardPageComponent } from './features/admin/pages/admin-dashboard-page.component';
import { authGuard } from './core/guards/auth.guard';
import { adminGuard } from './core/guards/admin.guard';

const routes: Routes = [
  { path: '', redirectTo: 'products', pathMatch: 'full' },
  { path: 'products', component: ProductsPageComponent },
  { path: 'products/:id', component: ProductDetailPageComponent },
  { path: 'login', component: LoginPageComponent },
  { path: 'register', component: RegisterPageComponent },
  { path: 'forgot-password', component: ForgotPasswordPageComponent },
  { path: 'reset-password', component: ResetPasswordPageComponent },
  { path: 'cart', component: CartPageComponent, canActivate: [authGuard] },
  { path: 'profile', component: ProfilePageComponent, canActivate: [authGuard] },
  { path: 'admin', component: AdminDashboardPageComponent, canActivate: [adminGuard] },
  { path: '**', redirectTo: 'products' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {}





