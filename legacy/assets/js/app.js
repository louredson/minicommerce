const products = [
  {
    id: 1,
    name: "T-Shirt Premium",
    price: 9500,
    stock: 20,
    category: "Roupa",
    image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80",
    description: "Algodao premium com corte moderno para uso diario."
  },
  {
    id: 2,
    name: "Tenis Urban",
    price: 26500,
    stock: 12,
    category: "Calcado",
    image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80",
    description: "Conforto e estilo para rotina e passeio."
  },
  {
    id: 3,
    name: "Relogio Classic",
    price: 42000,
    stock: 8,
    category: "Acessorios",
    image: "https://images.unsplash.com/photo-1523170335258-f5ed11844a49?auto=format&fit=crop&w=900&q=80",
    description: "Design elegante com pulseira resistente."
  },
  {
    id: 4,
    name: "Mochila Pro",
    price: 18700,
    stock: 15,
    category: "Acessorios",
    image: "https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=80",
    description: "Ideal para trabalho, estudo e viagem curta."
  },
  {
    id: 5,
    name: "Headphone X",
    price: 33200,
    stock: 10,
    category: "Eletronicos",
    image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=80",
    description: "Audio limpo com isolamento de ruido eficiente."
  },
  {
    id: 6,
    name: "Smart Lamp",
    price: 14300,
    stock: 18,
    category: "Casa",
    image: "https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80",
    description: "Iluminacao inteligente e economica para o quarto."
  }
];

const adminOrders = [
  { id: 1001, customer: "Ana Silva", total: 45200, status: "Pendente", date: "2026-04-20" },
  { id: 1002, customer: "Carlos Neto", total: 18700, status: "Enviado", date: "2026-04-22" },
  { id: 1003, customer: "Maria Lopes", total: 68000, status: "Pago", date: "2026-04-24" }
];

const adminUsers = [
  { id: 1, name: "Ana Silva", email: "ana@email.com", level: "Cliente" },
  { id: 2, name: "Carlos Neto", email: "carlos@email.com", level: "Cliente" },
  { id: 3, name: "Admin Loja", email: "admin@loja.com", level: "Admin" }
];

const formatPrice = (value) => `Kz ${value.toLocaleString("pt-PT")}`;

const getCart = () => JSON.parse(localStorage.getItem("cart") || "[]");
const saveCart = (cart) => localStorage.setItem("cart", JSON.stringify(cart));

const addToCart = (productId) => {
  const cart = getCart();
  const found = cart.find((item) => item.id === productId);
  if (found) {
    found.qty += 1;
  } else {
    cart.push({ id: productId, qty: 1 });
  }
  saveCart(cart);
  updateCartCounter();
};

const removeFromCart = (productId) => {
  const cart = getCart().filter((item) => item.id !== productId);
  saveCart(cart);
  renderCartPage();
  updateCartCounter();
};

const updateQuantity = (productId, qty) => {
  const cart = getCart();
  const item = cart.find((i) => i.id === productId);
  if (!item) return;
  item.qty = Number(qty);
  if (item.qty <= 0) {
    removeFromCart(productId);
    return;
  }
  saveCart(cart);
  renderCartPage();
  updateCartCounter();
};

const updateCartCounter = () => {
  const cart = getCart();
  const totalQty = cart.reduce((acc, item) => acc + item.qty, 0);
  document.querySelectorAll("[data-cart-count]").forEach((el) => {
    el.textContent = totalQty;
  });
};

const renderProducts = (targetId, limit = null) => {
  const container = document.getElementById(targetId);
  if (!container) return;
  const list = limit ? products.slice(0, limit) : products;
  container.innerHTML = list
    .map(
      (p) => `
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <img src="${p.image}" alt="${p.name}" class="product-image card-img-top" />
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title mb-0">${p.name}</h5>
              <span class="badge badge-stock">${p.category}</span>
            </div>
            <p class="card-text text-muted">${p.description}</p>
            <p class="fw-bold fs-5 mt-auto">${formatPrice(p.price)}</p>
            <button class="btn btn-brand w-100" onclick="addToCart(${p.id})">Adicionar ao Carrinho</button>
          </div>
        </div>
      </div>`
    )
    .join("");
};

const renderCartPage = () => {
  const tableBody = document.getElementById("cart-items");
  const summary = document.getElementById("cart-summary");
  if (!tableBody || !summary) return;

  const cart = getCart();
  if (!cart.length) {
    tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">Carrinho vazio.</td></tr>`;
    summary.innerHTML = `<h5>Total: ${formatPrice(0)}</h5>`;
    return;
  }

  let total = 0;
  tableBody.innerHTML = cart
    .map((item) => {
      const product = products.find((p) => p.id === item.id);
      if (!product) return "";
      const subtotal = product.price * item.qty;
      total += subtotal;
      return `
        <tr>
          <td>${product.name}</td>
          <td>${formatPrice(product.price)}</td>
          <td style="max-width:120px;"><input type="number" min="1" class="form-control" value="${item.qty}" onchange="updateQuantity(${product.id}, this.value)" /></td>
          <td>${formatPrice(subtotal)}</td>
          <td><button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${product.id})">Remover</button></td>
        </tr>`;
    })
    .join("");

  summary.innerHTML = `
    <h5>Total: ${formatPrice(total)}</h5>
    <a href="checkout.html" class="btn btn-brand">Finalizar Compra</a>
  `;
};

const renderCheckout = () => {
  const el = document.getElementById("checkout-total");
  if (!el) return;
  const total = getCart().reduce((acc, item) => {
    const product = products.find((p) => p.id === item.id);
    return acc + (product ? product.price * item.qty : 0);
  }, 0);
  el.textContent = formatPrice(total);

  const form = document.getElementById("checkout-form");
  form?.addEventListener("submit", (e) => {
    e.preventDefault();
    localStorage.removeItem("cart");
    updateCartCounter();
    const msg = document.getElementById("checkout-msg");
    if (msg) {
      msg.innerHTML = "<div class='alert alert-success'>Pedido enviado com sucesso. Pronto para integrar com PHP.</div>";
    }
    form.reset();
  });
};

const renderAdminStats = () => {
  const productsCount = document.getElementById("stat-products");
  const ordersCount = document.getElementById("stat-orders");
  const revenueCount = document.getElementById("stat-revenue");

  if (productsCount) productsCount.textContent = products.length;
  if (ordersCount) ordersCount.textContent = adminOrders.length;
  if (revenueCount) {
    const total = adminOrders.reduce((acc, order) => acc + order.total, 0);
    revenueCount.textContent = formatPrice(total);
  }
};

const renderAdminProducts = () => {
  const body = document.getElementById("admin-products");
  if (!body) return;
  body.innerHTML = products
    .map(
      (p) => `
      <tr>
        <td>${p.id}</td>
        <td>${p.name}</td>
        <td>${p.category}</td>
        <td>${formatPrice(p.price)}</td>
        <td>${p.stock}</td>
      </tr>`
    )
    .join("");
};

const renderAdminOrders = () => {
  const body = document.getElementById("admin-orders");
  if (!body) return;
  body.innerHTML = adminOrders
    .map(
      (o) => `
      <tr>
        <td>#${o.id}</td>
        <td>${o.customer}</td>
        <td>${o.date}</td>
        <td>${formatPrice(o.total)}</td>
        <td><span class="badge text-bg-secondary">${o.status}</span></td>
      </tr>`
    )
    .join("");
};

const renderAdminUsers = () => {
  const body = document.getElementById("admin-users");
  if (!body) return;
  body.innerHTML = adminUsers
    .map(
      (u) => `
      <tr>
        <td>${u.id}</td>
        <td>${u.name}</td>
        <td>${u.email}</td>
        <td>${u.level}</td>
      </tr>`
    )
    .join("");
};

document.addEventListener("DOMContentLoaded", () => {
  updateCartCounter();
  renderProducts("featured-products", 3);
  renderProducts("all-products");
  renderCartPage();
  renderCheckout();
  renderAdminStats();
  renderAdminProducts();
  renderAdminOrders();
  renderAdminUsers();
});
