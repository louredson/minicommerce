import { Component } from '@angular/core';
import { ThemeService } from './core/services/theme.service';
import { I18nService } from './core/services/i18n.service';

@Component({
  standalone: false,
  selector: 'app-root',
  template: `
    <app-navbar></app-navbar>
    <main class="container py-4 min-vh-100">
      <app-alert></app-alert>
      <router-outlet></router-outlet>
    </main>
    <footer class="app-footer">
      <div class="container py-3">MiniCommerce | Angular + PHP + SQL</div>
    </footer>
  `
})
export class AppComponent {
  constructor(private theme: ThemeService, private i18n: I18nService) {
    this.theme.init();
    this.i18n.init();
  }
}
