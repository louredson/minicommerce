import { Component } from '@angular/core';
import { AuthService } from '../../../core/services/auth.service';
import { AlertService } from '../../../core/services/alert.service';

@Component({
  standalone: false,
  selector: 'app-forgot-password-page',
  templateUrl: './forgot-password-page.component.html'
})
export class ForgotPasswordPageComponent {
  email = '';
  token = '';

  constructor(private auth: AuthService, private alerts: AlertService) {}

  submit() {
    this.auth.forgotPassword(this.email).subscribe({
      next: (res) => {
        this.token = res?.data?.token || '';
        this.alerts.show('info', `Token de recuperacao: ${this.token}`);
      },
      error: (err) => this.alerts.show('danger', err.error?.message || 'Falha ao gerar token.')
    });
  }
}
