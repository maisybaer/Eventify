document.addEventListener('DOMContentLoaded', () => {
    const productList = document.getElementById('productList');
    const categoryFilter = document.getElementById('categoryFilter');
    const brandFilter = document.getElementById('brandFilter');
    const searchBox = document.getElementById('searchBox');
    const searchBtn = document.getElementById('searchBtn');
    let products = [];
    const perPage = 10;
    let currentPage = 1;

    function renderProducts(list, page = 1) {
        if (!productList) return;
        productList.innerHTML = '';
        if (!list || list.length === 0) {
            productList.innerHTML = '<p>No products found.</p>';
            renderPager(0);
            return;
        }

        const totalPages = Math.ceil(list.length / perPage) || 1;
        page = Math.max(1, Math.min(page, totalPages));
        currentPage = page;
        const start = (page - 1) * perPage;
        const slice = list.slice(start, start + perPage);

        slice.forEach(p => {
            const card = document.createElement('div');
            card.className = 'product-card';
            // Prefer server-provided normalized image_url (added by fetch_product_action.php)
            let imgSrc = '';
            if (p.image_url) {
                imgSrc = p.image_url;
            } else if (p.product_image) {
                if (String(p.product_image).indexOf('uploads') !== -1) {
                    imgSrc = '../' + String(p.product_image).replace(/^\/+/, '');
                } else {
                    imgSrc = '../uploads/' + String(p.product_image).replace(/^\/+/, '');
                }
            }
            const link = document.createElement('a');
            link.href = `single_product.php?product_id=${encodeURIComponent(p.product_id)}`;
            link.style.textDecoration = 'none';
            link.style.color = 'inherit';
            const fallbackSrc = p.image_url || '../uploads/no-image.svg';
            card.innerHTML = `
                <div style="height:150px;overflow:hidden;">
                    <img src="${imgSrc}" alt="${(p.product_title||'Product')}" onerror="this.onerror=null;this.src='${fallbackSrc}'" />
                </div>
                <h5>${p.product_title || ''}</h5>
                <p class="text-muted">${p.category || ''} • ${p.brand || ''}</p>
                <p><strong>$${p.product_price || ''}</strong></p>
            `;
            link.appendChild(card);
            productList.appendChild(link);
        });

        renderPager(totalPages);
    };

    function renderPager(totalPages) {
        // create a basic pager below the product list
        let pager = document.getElementById('productPager');
        if (!pager) {
            pager = document.createElement('div');
            pager.id = 'productPager';
            pager.style.textAlign = 'center';
            pager.style.marginTop = '16px';
            productList.parentNode.insertBefore(pager, productList.nextSibling);
        }
        pager.innerHTML = '';
        if (totalPages <= 1) return;

        const createBtn = (label, page, disabled) => {
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm btn-outline-secondary m-1';
            btn.textContent = label;
            if (disabled) btn.disabled = true;
            btn.addEventListener('click', () => {
                applyFilters(page);
            });
            return btn;
        };

        pager.appendChild(createBtn('Prev', Math.max(1, currentPage - 1), currentPage === 1));
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm m-1 ' + (i === currentPage ? 'btn-primary' : 'btn-outline-secondary');
            btn.textContent = i;
            btn.addEventListener('click', () => applyFilters(i));
            pager.appendChild(btn);
        }
        pager.appendChild(createBtn('Next', Math.min(totalPages, currentPage + 1), currentPage === totalPages));
    }

    function populateFilters(items) {
        if (!categoryFilter || !brandFilter) return;
        const cats = {};
        const brands = {};
        items.forEach(p => {
            if (p.product_cat) cats[p.product_cat] = p.category || p.product_cat;
            if (p.product_brand) brands[p.product_brand] = p.brand || p.product_brand;
        });

        categoryFilter.innerHTML = '<option value="">Filter by Category</option>';
        Object.keys(cats).forEach(id => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = cats[id];
            categoryFilter.appendChild(opt);
        });

        brandFilter.innerHTML = '<option value="">Filter by Brand</option>';
        Object.keys(brands).forEach(id => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = brands[id];
            brandFilter.appendChild(opt);
        });
    }

    function loadAllProducts() {
        fetch('../actions/fetch_product_action.php')
            .then(res => res.json())
            .then(data => {
                products = data || [];
                renderProducts(products, 1);
                populateFilters(products);
            })
            .catch(err => {
                console.error('Failed to load products', err);
                if (productList) productList.innerHTML = '<p>Cannot load items</p>';
            });
    }

    function applyFilters(page = 1) {
        const q = (searchBox && searchBox.value || '').toLowerCase();
        const cat = categoryFilter ? categoryFilter.value : '';
        const brand = brandFilter ? brandFilter.value : '';

        const filtered = products.filter(p => {
            if (cat && String(p.product_cat) !== String(cat)) return false;
            if (brand && String(p.product_brand) !== String(brand)) return false;
            if (q) {
                const hay = ((p.product_title||'') + ' ' + (p.product_desc||'') + ' ' + (p.category||'') + ' ' + (p.brand||'')).toLowerCase();
                return hay.indexOf(q) !== -1;
            }
            return true;
        });

        renderProducts(filtered, page);
    }

    if (searchBtn) searchBtn.addEventListener('click', () => applyFilters(1));
    if (categoryFilter) categoryFilter.addEventListener('change', () => applyFilters(1));
    if (brandFilter) brandFilter.addEventListener('change', () => applyFilters(1));

    loadAllProducts();

});
