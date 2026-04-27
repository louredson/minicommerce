import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { AlertService } from '../../../core/services/alert.service';
import { SessionService } from '../../../core/services/session.service';

@Component({
  standalone: false,
  selector: 'app-login-page',
  templateUrl: './login-page.component.html'
})
export class LoginPageComponent {
  email = '';
  password = '';

  constructor(private auth: AuthService, private alerts: AlertService, private session: SessionService, private router: Router) {}

  submit() {
    this.auth.login({ email: this.email, password: this.password }).subscribe({
      next: (res) => {
        this.session.user = res.data;
        this.alerts.show('success', res.message ?? 'Login efetuado.');
        this.router.navigate([this.session.isAdmin ? '/admin' : '/products']);
      },
      error: (err) => this.alerts.show('danger', err.error?.message ?? 'Falha no login.')
    });
  }
}




