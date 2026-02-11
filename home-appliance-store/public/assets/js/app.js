// assets/js/app.js
const BASE_URL = '/api';
const TIMEOUT = 10000;

function timeoutFetch(ms, promise) {
  return Promise.race([
    promise,
    new Promise((_, rej) => setTimeout(() => rej(new Error('timeout')), ms))
  ]);
}

function getAuthToken() { return localStorage.getItem('authToken'); }
function setAuthToken(token) { localStorage.setItem('authToken', token); }
function clearAuthToken() { localStorage.removeItem('authToken'); }

async function request(path, {method='GET', body=null, headers={}} = {}) {
  const url = `${BASE_URL}${path}`;
  const opts = { method, headers: {...headers} };
  if (body) {
    opts.body = JSON.stringify(body);
    opts.headers['Content-Type'] = 'application/json';
  }
  const token = getAuthToken();
  if (token) opts.headers['Authorization'] = `Bearer ${token}`;

  const res = await timeoutFetch(TIMEOUT, fetch(url, opts));
  const text = await res.text();
  const data = text ? JSON.parse(text) : null;
  if (!res.ok) {
    const err = new Error(data?.message || 'Request failed');
    err.status = res.status;
    err.body = data;
    throw err;
  }
  return data;
}

const api = {
  get: (p) => request(p, {method:'GET'}),
  post: (p, b) => request(p, {method:'POST', body:b}),
  put: (p, b) => request(p, {method:'PUT', body:b}),
  del: (p) => request(p, {method:'DELETE'}),

  auth: {
    login: async (creds) => {
      const data = await request('/auth/login.php', {method:'POST', body:creds});
      if (data.token) setAuthToken(data.token);
      return data;
    },
    register: (payload) => request('/auth/register.php', {method:'POST', body:payload}),
    logout: () => { clearAuthToken(); }
  },

  products: {
    list: () => request('/products/read.php'),
    create: (payload) => request('/products/create.php', {method:'POST', body:payload})
  },

  orders: {
    create: (payload) => request('/orders/create.php', {method:'POST', body:payload}),
    list: () => request('/orders/read.php')
  }
};

window.api = api;

// Backward-compatible product fetch for pages that used the old helper
async function fetchProducts(){
  try{
    const data = await api.products.list();
    const el = document.getElementById('products');
    if(!el) return;
    if(data.products){
      el.innerHTML = data.products.map(p=>`<div class="product"><strong>${p.name}</strong> — $${p.price} <br> <button onclick="addToCart(${p.id},'${p.name}',${p.price})">Add</button></div>`).join('');
    } else el.textContent = 'No products';
  }catch(e){ console.error(e); }
}
function addToCart(id,name,price){
  alert('Added '+name+' to cart (client-only stub)');
}
if(document.getElementById('products')) fetchProducts();
