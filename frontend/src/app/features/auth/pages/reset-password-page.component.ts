import { Component } from '@angular/core';
import { AuthService } from '../../../core/services/auth.service';
import { AlertService } from '../../../core/services/alert.service';
import { Router } from '@angular/router';

@Component({
  standalone: false,
  selector: 'app-reset-password-page',
  templateUrl: './reset-password-page.component.html'
})
export class ResetPasswordPageComponent {
  email = '';
  token = '';
  password = '';
  confirm_password = '';

  constructor(private auth: AuthService, private alerts: AlertService, private router: Router) {}

  submit() {
    this.auth.resetPassword({ email: this.email, token: this.token, password: this.password, confirm_password: this.confirm_password }).subscribe({
      next: (res) => {
        this.alerts.show('success', res.message || 'Senha redefinida.');
        this.router.navigate(['/login']);
      },
      error: (err) => this.alerts.show('danger', err.error?.message || 'Falha ao redefinir senha.')
    });
  }
}
