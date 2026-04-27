import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { AlertService } from '../../../core/services/alert.service';

@Component({
  standalone: false,
  selector: 'app-register-page',
  templateUrl: './register-page.component.html'
})
export class RegisterPageComponent {
  first_name = '';
  last_name = '';
  email = '';
  password = '';
  confirm_password = '';

  constructor(private auth: AuthService, private alerts: AlertService, private router: Router) {}

  submit() {
    const first = this.first_name.trim();
    const last = this.last_name.trim();
    const email = this.email.trim();

    if (first.length < 2 || last.length < 2) {
      this.alerts.show('warning', 'Nome e sobrenome devem ter pelo menos 2 caracteres.');
      return;
    }

    if (!email.includes('@')) {
      this.alerts.show('warning', 'Email invalido.');
      return;
    }

    if (this.password.length < 6) {
      this.alerts.show('warning', 'Senha deve ter no minimo 6 caracteres.');
      return;
    }

    if (this.password !== this.confirm_password) {
      this.alerts.show('danger', 'Senha e Confirmar Senha nao coincidem.');
      return;
    }

    const payload = {
      first_name: first,
      last_name: last,
      email,
      password: this.password,
      confirm_password: this.confirm_password
    };

    this.auth.register(payload).subscribe({
      next: (res) => {
        this.alerts.show('success', res.message ?? 'Conta criada.');
        this.router.navigate(['/login']);
      },
      error: (err) => {
        const message = err?.error?.message ?? 'Falha no registo.';
        const type = /ja registado|ja cadastrado|existe/i.test(message) ? 'warning' : 'danger';
        this.alerts.show(type, message);
      }
    });
  }
}
