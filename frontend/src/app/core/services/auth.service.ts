import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { ApiResponse, User } from '../../shared/models/types';
import { environment } from '../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly base = environment.apiBase;

  constructor(private http: HttpClient) {}

  me(): Observable<ApiResponse<User>> {
    return this.http.get<ApiResponse<User>>(`${this.base}/auth/me`, { withCredentials: true });
  }

  login(payload: { email: string; password: string }) {
    return this.http.post<ApiResponse<User>>(`${this.base}/auth/login`, payload, { withCredentials: true });
  }

  register(payload: { first_name: string; last_name: string; email: string; password: string; confirm_password: string }) {
    return this.http.post<ApiResponse<User>>(`${this.base}/auth/register`, payload, { withCredentials: true });
  }

  forgotPassword(email: string) {
    return this.http.post<any>(`${this.base}/auth/forgot-password`, { email }, { withCredentials: true });
  }

  resetPassword(payload: { token: string; password: string; confirm_password: string }) {
    return this.http.post<any>(`${this.base}/auth/reset-password`, payload, { withCredentials: true });
  }

  logout() {
    return this.http.post<ApiResponse<null>>(`${this.base}/auth/logout`, {}, { withCredentials: true });
  }
}
