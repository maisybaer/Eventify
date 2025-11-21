<?php
require_once '../settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
</head>
<body>

    <div class="menu-tray">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
            <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
            <a href="../view/basket.php" class="btn btn-sm btn-outline-secondary">Basket</a>
        <?php else: ?>
            <a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
            <a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
        <?php endif; ?>
    </div>

    <div class="container" style="padding-top:80px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
            </div>
            <div class="d-flex align-items-center">
                <input id="searchInput" class="form-control me-2" style="min-width:300px;" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <button id="searchBtn" class="btn btn-primary">Search</button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <select id="categoryFilter" class="form-select">
                    <option value="">All categories</option>
                </select>
            </div>
            <div class="col-md-4">
                <select id="brandFilter" class="form-select">
                    <option value="">All brands</option>
                </select>
            </div>
            <div class="col-md-4 text-end">
                <small id="resultCount" class="text-muted"></small>
            </div>
        </div>

        <div id="results" class="product-grid"></div>

        <nav>
            <ul id="pager" class="pagination"></ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Inline script adapted to match your existing style and endpoints.
    document.addEventListener('DOMContentLoaded', () => {
        const resultsEl = document.getElementById('results');
        const pagerEl = document.getElementById('pager');
        const categoryFilter = document.getElementById('categoryFilter');
        const brandFilter = document.getElementById('brandFilter');
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const resultCount = document.getElementById('resultCount');

        let allProducts = [];
        const perPage = 10;
        let currentPage = 1;

        function fetchCategories(){
            return fetch('../actions/fetch_category_action.php')
                .then(r => r.json())
                .then(data => {
                    if (!Array.isArray(data)) return;
                    data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.cat_id ?? c.id ?? c.catId ?? '';
                        opt.textContent = c.cat_name ?? c.name ?? c.category ?? 'Unknown';
                        categoryFilter.appendChild(opt);
                    });
                }).catch(()=>{});
        }

        function fetchBrands(){
            return fetch('../actions/fetch_brand_action.php')
                .then(r => r.json())
                .then(data => {
                    if (!Array.isArray(data)) return;
                    data.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = b.brand_id ?? b.id ?? b.brandId ?? '';
                        opt.textContent = b.brand_name ?? b.name ?? b.brand ?? 'Unknown';
                        brandFilter.appendChild(opt);
                    });
                }).catch(()=>{});
        }

        function performSearch(query){
            const url = `../actions/product_actions.php?action=search&query=${encodeURIComponent(query)}`;
            return fetch(url).then(r=>r.json()).catch(()=>Promise.resolve([]));
        }

        function renderPage(page){
            currentPage = page;
            const filtered = filteredProducts();
            const total = filtered.length;
            resultCount.textContent = `${total} result${total===1?'':'s'}`;
            const start = (page-1)*perPage;
            const pageItems = filtered.slice(start, start+perPage);
            resultsEl.innerHTML = '';
            if(pageItems.length===0){
                resultsEl.innerHTML = '<div class="text-muted">No products found.</div>';
                pagerEl.innerHTML = '';
                return;
            }
            pageItems.forEach(p=>{
                const id = p.event_id ?? p.id ?? p.eventId ?? '';
                const title = p.event_name ?? p.title ?? p.name ?? 'Untitled';
                const price = p.event_price ?? p.price ?? 0;
                const image = p.flyer ?? p.image ?? p.event_image ?? '';
                const category = p.cat_name ?? p.category_name ?? '';

                const card = document.createElement('div');
                card.className = 'product-card';
                card.innerHTML = `
                    <a href="single_event.php?event_id=${encodeURIComponent(id)}" style="text-decoration:none;color:inherit">
                        <img src="${image || '../images/no-image.png'}" alt="${title}">
                        <div class="mt-2"><strong>${title}</strong></div>
                    </a>
                    <div class="mt-1 text-muted">Category: ${category}</div>
                    <div class="mt-2"><strong>Price: $${Number(price).toFixed(2)}</strong></div>
                    <div class="mt-2">
                        <a href="single_event.php?event_id=${encodeURIComponent(id)}" class="btn btn-sm btn-outline-primary">View</a>
                        <button data-id="${id}" class="btn btn-sm btn-success add-to-cart">Add to Cart</button>
                    </div>
                `;
                resultsEl.appendChild(card);
            });

            renderPager(Math.ceil(filtered.length / perPage));
            attachCartHandlers();
        }

        function renderPager(totalPages){
            pagerEl.innerHTML = '';
            if(totalPages <= 1) return;
            for(let i=1;i<=totalPages;i++){
                const li = document.createElement('li');
                li.className = 'page-item'+(i===currentPage?' active':'');
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                a.textContent = i;
                a.addEventListener('click', (e)=>{ e.preventDefault(); renderPage(i); });
                li.appendChild(a);
                pagerEl.appendChild(li);
            }
        }

        function filteredProducts(){
            const cat = categoryFilter.value;
            const brand = brandFilter.value;
            return allProducts.filter(p=>{
                if(cat){
                    const pid = String(p.product_cat ?? p.cat_id ?? p.category_id ?? '');
                    if(pid !== String(cat)) return false;
                }
                if(brand){
                    const bid = String(p.product_brand ?? p.brand_id ?? p.brandId ?? '');
                    if(bid !== String(brand)) return false;
                }
                return true;
            });
        }

        function attachCartHandlers(){
            resultsEl.querySelectorAll('.add-to-cart').forEach(btn=>{
                btn.addEventListener('click', ()=>{
                    const id = btn.getAttribute('data-id');
                    Swal.fire('Add to cart','Product '+id+' would be added to cart (placeholder)','info');
                });
            });
        }

        function doSearch(q){
            if(!q || q.trim()===''){
                // load all
                fetch('../actions/fetch_product_action.php')
                    .then(r=>r.json())
                    .then(data=>{ allProducts = Array.isArray(data)?data:[]; renderPage(1); populateFilters(allProducts); })
                    .catch(()=>{ resultsEl.innerHTML = '<div class="text-muted">Cannot load items</div>'; });
                return;
            }
            performSearch(q).then(data=>{
                allProducts = Array.isArray(data)?data:[];
                renderPage(1);
                populateFilters(allProducts);
            });
        }

        function populateFilters(items){
            if (!categoryFilter || !brandFilter) return;
            categoryFilter.innerHTML = '<option value="">All categories</option>';
            brandFilter.innerHTML = '<option value="">All brands</option>';
            const cats = {};
            const brands = {};
            items.forEach(p => {
                if (p.product_cat) cats[p.product_cat] = p.category || p.product_cat;
                if (p.product_brand) brands[p.product_brand] = p.brand || p.product_brand;
            });
            Object.keys(cats).forEach(id => {
                const opt = document.createElement('option'); opt.value=id; opt.textContent=cats[id]; categoryFilter.appendChild(opt);
            });
            Object.keys(brands).forEach(id => {
                const opt = document.createElement('option'); opt.value=id; opt.textContent=brands[id]; brandFilter.appendChild(opt);
            });
        }

        // initialize
        Promise.all([fetchCategories(), fetchBrands()]).finally(()=>{
            const q = (new URLSearchParams(window.location.search)).get('q') || searchInput.value || '';
            searchInput.value = q;
            doSearch(q);
        });

        searchBtn.addEventListener('click', ()=> doSearch(searchInput.value));
        categoryFilter.addEventListener('change', ()=> renderPage(1));
        brandFilter.addEventListener('change', ()=> renderPage(1));
    });
    </script>
</body>
</html>