import { Component, OnInit } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { filter } from 'rxjs/operators';
import { AuthService } from '../../../core/services/auth.service';
import { SessionService } from '../../../core/services/session.service';
import { ThemeService } from '../../../core/services/theme.service';
import { I18nService } from '../../../core/services/i18n.service';
import { CatalogService } from '../../../core/services/catalog.service';

@Component({
  standalone: false,
  selector: 'app-navbar',
  templateUrl: './navbar.component.html'
})
export class NavbarComponent implements OnInit {
  isMenuOpen = false;
  searchTerm = '';
  currentUrl = '';
  suggestions: Array<{ id: number; name: string; category_name: string }> = [];
  private allProducts: Array<{ id: number; name: string; category_name: string; description?: string }> = [];
  private searchTimer: any;

  constructor(
    public session: SessionService,
    public theme: ThemeService,
    public i18n: I18nService,
    private auth: AuthService,
    private router: Router,
    private catalog: CatalogService
  ) {}

  ngOnInit(): void {
    this.auth.me().subscribe({
      next: (res) => (this.session.user = res.data),
      error: () => (this.session.user = null)
    });

    this.catalog.products().subscribe({
      next: (res) => {
        this.allProducts = (res.data || []).map((p: any) => ({
          id: Number(p.id),
          name: String(p.name || ''),
          category_name: String(p.category_name || ''),
          description: String(p.description || '')
        }));
      },
      error: () => {
        this.allProducts = [];
      }
    });

    this.currentUrl = this.router.url || '';
    this.router.events.pipe(filter((e) => e instanceof NavigationEnd)).subscribe((e) => {
      this.currentUrl = (e as NavigationEnd).urlAfterRedirects || '';
    });
  }

  logout() {
    this.auth.logout().subscribe(() => {
      this.session.user = null;
      this.isMenuOpen = false;
      this.router.navigate(['/login']);
    });
  }

  toggleMenu() {
    this.isMenuOpen = !this.isMenuOpen;
  }

  closeMenu() {
    this.isMenuOpen = false;
  }

  onSearchInput() {
    const q = this.searchTerm.trim().toLowerCase();

    if (!q) {
      this.suggestions = [];
      this.navigateToSearch('');
      return;
    }

    this.suggestions = this.allProducts
      .filter((p) => `${p.name} ${p.category_name} ${p.description || ''}`.toLowerCase().includes(q))
      .slice(0, 6)
      .map((p) => ({ id: p.id, name: p.name, category_name: p.category_name }));

    clearTimeout(this.searchTimer);
    this.searchTimer = setTimeout(() => this.navigateToSearch(this.searchTerm), 200);
  }

  searchProducts() {
    this.navigateToSearch(this.searchTerm);
    this.suggestions = [];
    this.closeMenu();
  }

  selectSuggestion(name: string) {
    this.searchTerm = name;
    this.navigateToSearch(name);
    this.suggestions = [];
    this.closeMenu();
  }

  hideSuggestionsSoon() {
    setTimeout(() => {
      this.suggestions = [];
    }, 160);
  }

  private navigateToSearch(term: string) {
    const q = term.trim();
    this.router.navigate(['/products'], { queryParams: q ? { q } : {} });
  }

  get showSearch(): boolean {
    return this.currentUrl.startsWith('/products');
  }
}
