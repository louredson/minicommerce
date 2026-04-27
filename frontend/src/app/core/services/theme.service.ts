import { Injectable } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class ThemeService {
  dark = false;

  toggle() {
    this.dark = !this.dark;
    document.body.classList.toggle('dark-mode', this.dark);
    localStorage.setItem('theme', this.dark ? 'dark' : 'light');
  }

  init() {
    this.dark = localStorage.getItem('theme') === 'dark';
    document.body.classList.toggle('dark-mode', this.dark);
  }
}
