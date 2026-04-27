import { Component } from '@angular/core';
import { AlertService } from '../../../core/services/alert.service';

@Component({
  standalone: false,
  selector: 'app-alert',
  template: `
    <div *ngIf="alerts.message" class="toast-window alert alert-{{ alerts.type }} shadow">
      <div class="d-flex justify-content-between align-items-center gap-3">
        <span>{{ alerts.message }}</span>
        <button class="btn-close" (click)="alerts.clear()"></button>
      </div>
    </div>
  `
})
export class AlertComponent {
  constructor(public alerts: AlertService) {}
}
