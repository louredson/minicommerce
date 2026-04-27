import { Injectable } from '@angular/core';
import { User } from '../../shared/models/types';

@Injectable({ providedIn: 'root' })
export class SessionService {
  user: User | null = null;

  get isLoggedIn() {
    return !!this.user;
  }

  get isAdmin() {
    return Number(this.user?.is_admin) === 1;
  }
}



