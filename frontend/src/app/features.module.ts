import { NgModule } from '@angular/core';
import { SharedModule } from './shared/shared.module';
import { LoginPageComponent } from './features/auth/pages/login-page.component';
import { RegisterPageComponent } from './features/auth/pages/register-page.component';
import { ForgotPasswordPageComponent } from './features/auth/pages/forgot-password-page.component';
import { ResetPasswordPageComponent } from './features/auth/pages/reset-password-page.component';
import { ProductsPageComponent } from './features/catalog/pages/products-page.component';
import { ProductDetailPageComponent } from './features/catalog/pages/product-detail-page.component';
import { CartPageComponent } from './features/cart/pages/cart-page.component';
import { ProfilePageComponent } from './features/profile/pages/profile-page.component';
import { AdminDashboardPageComponent } from './features/admin/pages/admin-dashboard-page.component';

@NgModule({
  declarations: [
    LoginPageComponent,
    RegisterPageComponent,
    ForgotPasswordPageComponent,
    ResetPasswordPageComponent,
    ProductsPageComponent,
    ProductDetailPageComponent,
    CartPageComponent,
    ProfilePageComponent,
    AdminDashboardPageComponent
  ],
  imports: [SharedModule],
  exports: [
    LoginPageComponent,
    RegisterPageComponent,
    ForgotPasswordPageComponent,
    ResetPasswordPageComponent,
    ProductsPageComponent,
    ProductDetailPageComponent,
    CartPageComponent,
    ProfilePageComponent,
    AdminDashboardPageComponent
  ]
})
export class FeaturesModule {}

