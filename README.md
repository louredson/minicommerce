# MiniCommerce - Angular + PHP + SQL

## Arquitetura

- Frontend: `frontend/` (Angular)
  - `core/`: serviços, guards e estado de sessão
  - `shared/`: componentes reutilizáveis (`navbar`, `alert`, `product-card`)
  - `features/`: páginas por domínio (`auth`, `catalog`, `cart`, `profile`, `admin`)
  - `services` consomem API HTTP JSON

- Backend: `backend/` (PHP puro)
  - `public/index.php`: entrypoint e roteamento HTTP
  - `src/Controllers`: endpoints
  - `src/Services`: regras de negócio
  - `src/Repositories`: acesso a dados
  - `src/Core`: utilitários (DB, JSON response)
  - `src/Middleware`: autenticação/autorização

- Base de dados SQL: `sql/database.sql`

## API JSON (exemplos)

- `POST /backend/public/auth/register`
- `POST /backend/public/auth/login`
- `GET /backend/public/products?category_id=1`
- `POST /backend/public/cart/add`
- `GET /backend/public/cart`
- `POST /backend/public/checkout`
- `GET /backend/public/orders/my`
- `GET /backend/public/admin/dashboard`

Todas respostas são JSON no formato:

```json
{
  "success": true,
  "message": "...",
  "data": {}
}
```

## Execução

1. Importar `sql/database.sql` no MySQL.
2. Ajustar credenciais em `backend/src/Config/config.php`.
3. Servir o backend em Apache/XAMPP com o projeto em `htdocs/teste_front`.
4. Rodar frontend:

```bash
cd frontend
npm.cmd install
npm.cmd start
```

Frontend: `http://localhost:4200`
Backend: `http://localhost/teste_front/backend/public`

## Observação

- Se o Apache tiver `mod_rewrite` ativo, as rotas amigáveis funcionam via `backend/public/.htaccess`.
- Caso haja bloqueio local para build Angular (`spawn EPERM`), execute terminal como administrador ou ajuste permissões do antivírus/Windows Defender para `node` e `ng`.
