import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { SessionService } from '../../../core/services/session.service';
import { ThemeService } from '../../../core/services/theme.service';
import { I18nService } from '../../../core/services/i18n.service';

@Component({
  standalone: false,
  selector: 'app-navbar',
  templateUrl: './navbar.component.html'
})
export class NavbarComponent implements OnInit {
  isMenuOpen = false;

  constructor(
    public session: SessionService,
    public theme: ThemeService,
    public i18n: I18nService,
    private auth: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.auth.me().subscribe({
      next: (res) => (this.session.user = res.data),
      error: () => (this.session.user = null)
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
}
