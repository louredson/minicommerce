import { Injectable } from '@angular/core';

type Lang = 'pt' | 'en' | 'es';

@Injectable({ providedIn: 'root' })
export class I18nService {
  lang: Lang = 'pt';

  private dict: Record<Lang, Record<string, string>> = {
    pt: { products: 'Produtos', login: 'Entrar', register: 'Criar Conta', cart: 'Carrinho', admin: 'Admin', logout: 'Sair', dark: 'Modo Escuro', light: 'Modo Claro', forgot: 'Recuperar Senha' },
    en: { products: 'Products', login: 'Login', register: 'Create Account', cart: 'Cart', admin: 'Admin', logout: 'Logout', dark: 'Dark Mode', light: 'Light Mode', forgot: 'Forgot Password' },
    es: { products: 'Productos', login: 'Iniciar sesión', register: 'Crear Cuenta', cart: 'Carrito', admin: 'Admin', logout: 'Salir', dark: 'Modo Oscuro', light: 'Modo Claro', forgot: 'Recuperar Contraseña' }
  };

  set(lang: Lang) { this.lang = lang; localStorage.setItem('lang', lang); }
  init() { const saved = localStorage.getItem('lang') as Lang | null; if (saved && ['pt', 'en', 'es'].includes(saved)) this.lang = saved; }
  t(key: string) { return this.dict[this.lang][key] ?? key; }
}
