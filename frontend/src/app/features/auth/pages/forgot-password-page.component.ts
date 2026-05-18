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

  constructor(private auth: AuthService, private alerts: AlertService) {}

  submit() {
    this.auth.forgotPassword(this.email).subscribe({
      next: (res) => {
        this.alerts.show('success', res?.message || 'Se o email existir e estiver ativo, o codigo foi enviado.');
      },
      error: (err) => this.alerts.show('danger', err.error?.message || 'Falha ao enviar codigo.')
    });
  }
}
