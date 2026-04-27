import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { SessionService } from '../../../core/services/session.service';

@Component({
  standalone: false,
  selector: 'app-profile-page',
  templateUrl: './profile-page.component.html'
})
export class ProfilePageComponent implements OnInit {
  orders: any[] = [];

  constructor(private http: HttpClient, public session: SessionService) {}

  ngOnInit(): void {
    this.http.get<any>('/api/orders/my', { withCredentials: true }).subscribe((res) => (this.orders = res.data || []));
  }
}
