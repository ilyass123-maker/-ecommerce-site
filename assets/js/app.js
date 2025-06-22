// assets/js/app.js

const API_URL = 'https://dummyjson.com/products';
const LIMIT   = 12;
let currentPage = 1, totalPages = 1;

// CART (LocalStorage)
const cartKey = 'ecomCart';
function loadCart() {
  return JSON.parse(localStorage.getItem(cartKey) || '[]');
}
function saveCart(cart) {
  localStorage.setItem(cartKey, JSON.stringify(cart));
}
function updateCartBadge() {
  document.getElementById('cart-count').innerText = loadCart().length;
}

// FETCH & RENDER PRODUCTS
async function fetchProducts(page = 1) {
  const skip = (page - 1) * LIMIT;
  const res  = await fetch(`${API_URL}?limit=${LIMIT}&skip=${skip}`);
  const { products, total } = await res.json();
  totalPages = Math.ceil(total / LIMIT);
  renderProducts(products);
  renderPagination();
}

function renderProducts(items) {
  const grid = document.getElementById('product-grid');
  grid.innerHTML = '';
  items.forEach(p => {
    const col = document.createElement('div');
    col.className = 'col-sm-6 col-md-4 col-lg-3';
    col.innerHTML = `
      <div class="card h-100">
        <img src="${p.thumbnail}" class="card-img-top" alt="${p.title}">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">${p.title}</h6>
          <p class="card-text mt-auto fw-bold">${p.price} dh</p>
          <button class="btn btn-sm btn-success add-cart">Ajouter</button>
        </div>
      </div>
    `;
    col.querySelector('.add-cart').onclick = () => {
      const cart = loadCart();
      cart.push(p.id);
      saveCart(cart);
      updateCartBadge();
    };
    grid.appendChild(col);
  });
}

// PAGINATION
function renderPagination() {
  const ul = document.getElementById('pagination');
  ul.innerHTML = '';

  // Prev
  const prev = document.createElement('li');
  prev.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
  prev.innerHTML = `<a class="page-link" href="#">‹</a>`;
  prev.onclick = e => {
    e.preventDefault();
    if (currentPage > 1) loadPage(currentPage - 1);
  };
  ul.appendChild(prev);

  // Pages
  for (let i = 1; i <= totalPages; i++) {
    const li = document.createElement('li');
    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
    li.onclick = e => {
      e.preventDefault();
      loadPage(i);
    };
    ul.appendChild(li);
  }

  // Next
  const next = document.createElement('li');
  next.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
  next.innerHTML = `<a class="page-link" href="#">›</a>`;
  next.onclick = e => {
    e.preventDefault();
    if (currentPage < totalPages) loadPage(currentPage + 1);
  };
  ul.appendChild(next);
}

function loadPage(page) {
  currentPage = page;
  fetchProducts(page);
}

// HEADER LINK HANDLERS
document.addEventListener('DOMContentLoaded', () => {
  // Initial load
  fetchProducts();
  updateCartBadge();

  // Accueil = go to page 1
  document.getElementById('accueil-link').onclick = e => {
    e.preventDefault();
    loadPage(1);
  };

  // Profil = placeholder (you can redirect to your profil.html)
  document.getElementById('profil-link').onclick = e => {
    e.preventDefault();
    alert('Page Profil non implémentée.');
  };

  // Cart button
  document.getElementById('cart-btn').onclick = () => {
    alert(`Vous avez ${loadCart().length} article(s) dans le panier.`);
  };

  // (Optional) put your filter logic here…
});
