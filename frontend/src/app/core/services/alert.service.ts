import { Injectable } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class AlertService {
  message = '';
  type: 'success' | 'danger' | 'warning' | 'info' = 'info';
  private timer: any;

  show(type: 'success' | 'danger' | 'warning' | 'info', message: string) {
    this.type = type;
    this.message = message;
    if (this.timer) clearTimeout(this.timer);
    this.timer = setTimeout(() => this.clear(), 2000);
  }

  clear() {
    if (this.timer) clearTimeout(this.timer);
    this.message = '';
  }
}
