document.addEventListener('DOMContentLoaded', () => {
    const vendorList = document.getElementById('vendorList');
    const categoryFilter = document.getElementById('categoryFilter');
    const searchBox = document.getElementById('searchBox');
    const searchBtn = document.getElementById('searchBtn');
    
    let vendors = [];
    const perPage = 10;
    let currentPage = 1;

    function renderVendors(list, page = 1) {
        if (!vendorList) return;
        vendorList.innerHTML = '';
        if (!list || list.length === 0) {
            vendorList.innerHTML = '<p>No vendors found.</p>';
            renderPager(0);
            return;
        }

        const totalPages = Math.max(1, Math.ceil(list.length / perPage));
        page = Math.max(1, Math.min(page, totalPages));
        currentPage = page;
        const start = (page - 1) * perPage;
        const slice = list.slice(start, start + perPage);

        slice.forEach(p => {
            // Prefer server-provided normalized image_url
            let imgSrc = '';
            const fallbackSrc = '../uploads/no-image.svg';
            if (p.image_url) {
                imgSrc = p.image_url;
            } else if (p.flyer || p.vendor_image) {
                const imgField = p.flyer || p.vendor_image;
                if (String(imgField).indexOf('uploads') !== -1) {
                    imgSrc = '../' + String(imgField).replace(/^\/+/, '');
                } else {
                    imgSrc = '../uploads/' + String(imgField).replace(/^\/+/, '');
                }
            } else if (p.customer_image) {
                imgSrc = String(p.customer_image).indexOf('uploads') !== -1 ? '../' + String(p.customer_image).replace(/^\/+/, '') : p.customer_image;
            } else {
                imgSrc = fallbackSrc;
            }

            const title = p.vendor_desc || p.customer_name || '';
            const category = p.category || p.vendor_type || '';
            const price = (typeof p.vendor_price !== 'undefined' && p.vendor_price !== null && p.vendor_price !== '') ? p.vendor_price : '';
            const vendorId = p.vendor_id || '';
            const isVendorRecord = (typeof p.customer_name !== 'undefined' || typeof p.vendor_desc !== 'undefined' || typeof p.vendor_type !== 'undefined');
            const userRole = (typeof window._userRole !== 'undefined') ? parseInt(window._userRole, 10) : 0;

            const wrapper = document.createElement('div');
            wrapper.className = 'product-card';

            // Build a link target for this vendor/item. Use vendor_id when available, else customer_id.
            const linkTarget = (p.vendor_id) ? ('single_vendor.php?vendor_id=' + encodeURIComponent(p.vendor_id)) : (p.customer_id ? ('single_vendor.php?customer_id=' + encodeURIComponent(p.customer_id)) : 'single_vendor.php');

            if (isVendorRecord && userRole === 2) {
                // Render a vendor card for vendor users
                const vName = p.customer_name || p.vendor_desc || 'Vendor';
                const vEmail = p.customer_email || '';
                const vContact = p.customer_contact || '';
                const vType = p.vendor_type || '';

                wrapper.innerHTML = `
                    <a class="vendor-link" href="${linkTarget}" style="text-decoration:none;color:inherit;">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3" style="height:150px;overflow:hidden;border-radius:8px;">
                                <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(vName)}" style="width:100%;height:150px;object-fit:cover;" onerror="this.onerror=null;this.src='${fallbackSrc}'" />
                            </div>
                            <h5 style="font-weight:600;color:#2d3748;margin-bottom:0.75rem;">${escapeHtml(vName)}</h5>
                            ${vType ? `<p><span class="vendor-type">${escapeHtml(vType)}</span></p>` : ''}
                            <div class="vendor-meta">
                                ${vEmail ? `<div class="vendor-contact"><i class="fas fa-envelope"></i> <span>${escapeHtml(vEmail)}</span></div>` : ''}
                                ${vContact ? `<div class="vendor-contact"><i class="fas fa-phone"></i> <span>${escapeHtml(vContact)}</span></div>` : ''}
                            </div>
                            <div><button type="button" class="btn btn-custom mt-3 w-100">View Profile</button></div>
                        </div>
                    </div>
                    </a>
                `;
            } else {
                // Default: render as vendor card
                const vName = p.customer_name || p.vendor_desc || 'Vendor';
                const vEmail = p.customer_email || '';
                const vContact = p.customer_contact || '';
                const vType = p.vendor_type || category || '';

                wrapper.innerHTML = `
                    <a class="vendor-link" href="${linkTarget}" style="text-decoration:none;color:inherit;">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3" style="height:150px;overflow:hidden;border-radius:8px;">
                                <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(vName)}" style="width:100%;height:150px;object-fit:cover;" onerror="this.onerror=null;this.src='${fallbackSrc}'" />
                            </div>
                            <h5 style="font-weight:600;color:#2d3748;margin-bottom:0.75rem;">${escapeHtml(vName)}</h5>
                            ${vType ? `<p><span class="vendor-type">${escapeHtml(vType)}</span></p>` : ''}
                            ${price !== '' ? `<p style="font-weight:600;color:#667eea;font-size:1.1rem;">GHS ${escapeHtml(String(price))}</p>` : ''}
                            <div class="vendor-meta">
                                ${vEmail ? `<div class="vendor-contact"><i class="fas fa-envelope"></i> <span>${escapeHtml(vEmail)}</span></div>` : ''}
                                ${vContact ? `<div class="vendor-contact"><i class="fas fa-phone"></i> <span>${escapeHtml(vContact)}</span></div>` : ''}
                            </div>
                            <div><button type="button" class="btn btn-custom mt-3 w-100">View Profile</button></div>
                        </div>
                    </div>
                    </a>
                `;
            }

            vendorList.appendChild(wrapper);
        });

        renderPager(totalPages);
    };

    // small helper to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/\'/g, '&#039;');
    }

    function renderPager(totalPages) {
        // create a basic pager below the vendor list
        let pager = document.getElementById('vendorPager');
        if (!pager) {
            pager = document.createElement('div');
            pager.id = 'vendorPager';
            pager.style.textAlign = 'center';
            pager.style.marginTop = '16px';
            if (vendorList && vendorList.parentNode) vendorList.parentNode.insertBefore(pager, vendorList.nextSibling);
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
        if (!categoryFilter) return;
        const cats = {};
        const brands = {};
        items.forEach(p => {
            if (p.vendor_cat) cats[p.vendor_cat] = p.category || p.vendor_cat;
        });

        categoryFilter.innerHTML = '<option value="">Filter by Category</option>';
        Object.keys(cats).forEach(id => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = cats[id];
            categoryFilter.appendChild(opt);
        });

    }

    function loadAllVendors() {
        // Fetch all vendors (customers with role=2)
        fetch('../actions/fetch_all_vendors_action.php')
            .then(res => res.json())
            .then(payload => {
                if (!payload || payload.status !== 'success' || !Array.isArray(payload.data)) {
                    if (vendorList) vendorList.innerHTML = '<p>No vendor data available.</p>';
                    return;
                }

                // payload.data is an array of rows where customer fields and optional vendor_* fields exist
                const items = payload.data.map(r => {
                    return {
                        // customer fields (as returned by eventify_customer)
                        customer_id: r.customer_id || null,
                        customer_name: r.customer_name || '',
                        customer_email: r.customer_email || '',
                        customer_contact: r.customer_contact || '',
                        customer_image: r.customer_image || r.image_url || '',
                        // vendor fields (from eventify_vendor)
                        vendor_id: r.vendor_id || null,
                        vendor_type: r.vendor_type || r.vendor_cat || '',
                        vendor_desc: r.vendor_desc || '',
                        // keep any other useful fields
                        vendor_price: r.vendor_price || r.price || '',
                        category: r.category || ''
                    };
                });

                vendors = items;
                renderVendors(vendors, 1);
                // populate category filters if present on the page
                populateFilters(vendors);
            })
            .catch(err => {
                console.error('Failed to load vendors', err);
                if (vendorList) vendorList.innerHTML = '<p>Cannot load vendor data</p>';
            });
    }

    function applyFilters(page = 1) {
        const q = (searchBox && searchBox.value || '').toLowerCase();
        const cat = categoryFilter ? categoryFilter.value : '';

        const filtered = vendors.filter(p => {
            if (cat && String(p.vendor_cat) !== String(cat)) return false;
            if (q) {
                const hay = ((p.vendor_desc||'') + ' ' + (p.category||'') + ' ' ).toLowerCase();
                return hay.indexOf(q) !== -1;
            }
            return true;
        });

        renderVendors(filtered, page);
    }

    if (searchBtn) searchBtn.addEventListener('click', () => applyFilters(1));
    if (categoryFilter) categoryFilter.addEventListener('change', () => applyFilters(1));

    loadAllVendors();

});
