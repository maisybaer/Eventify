document.addEventListener('DOMContentLoaded', () => {
    const eventList = document.getElementById('eventList');
    const categoryFilter = document.getElementById('categoryFilter');
    const searchBox = document.getElementById('searchBox');
    const searchBtn = document.getElementById('searchBtn');
    let events = [];
    const perPage = 10;
    let currentPage = 1;

    function renderEvents(list, page = 1) {
        if (!eventList) return;
        eventList.innerHTML = '';
        if (!list || list.length === 0) {
            eventList.innerHTML = '<p>No events found.</p>';
            renderPager(0);
            return;
        }

        const totalPages = Math.ceil(list.length / perPage) || 1;
        page = Math.max(1, Math.min(page, totalPages));
        currentPage = page;
        const start = (page - 1) * perPage;
        const slice = list.slice(start, start + perPage);

        slice.forEach(p => {
            // Prefer server-provided normalized image_url (added by fetch_event_action.php)
            let imgSrc = '';
            if (p.image_url) {
                imgSrc = p.image_url;
            } else if (p.flyer || p.event_image) {
                const imgField = p.flyer || p.event_image;
                if (String(imgField).indexOf('uploads') !== -1) {
                    imgSrc = '../' + String(imgField).replace(/^\/+/, '');
                } else {
                    imgSrc = '../uploads/' + String(imgField).replace(/^\/+/, '');
                }
            } else {
                imgSrc = '../uploads/no-image.svg';
            }

            const title = p.event_desc || '';
            const category = p.category || '';
            const price = (typeof p.event_price !== 'undefined' && p.event_price !== null) ? p.event_price : '';
            const eventId = p.event_id || '';
            const eventDate = p.event_date || '';
            const eventLocation = p.event_location || '';
            const fallbackSrc = '../uploads/no-image.svg';

            // Format date if available
            let formattedDate = '';
            if (eventDate) {
                try {
                    const dateObj = new Date(eventDate);
                    formattedDate = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                } catch (e) {
                    formattedDate = eventDate;
                }
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'product-card';

            wrapper.innerHTML = `
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3" style="height:150px;overflow:hidden;">
                            <img src="${imgSrc}" alt="${title}" style="width:100%;height:150px;object-fit:cover;" onerror="this.onerror=null;this.src='${fallbackSrc}'" />
                        </div>
                        <h5>${title}</h5>
                        <p class="text-muted">${category}</p>
                        ${formattedDate ? `<p class="event-date"><i class="fas fa-calendar"></i> ${formattedDate}</p>` : ''}
                        ${eventLocation ? `<p class="event-location"><i class="fas fa-map-marker-alt"></i> ${eventLocation}</p>` : ''}
                        <p class="event-price">GHS ${price !== '' ? price : '0'}</p>
                        <a href="single_event.php?event_id=${encodeURIComponent(eventId)}" class="btn btn-custom mt-2">View</a>
                    </div>
                </div>
            `;

            eventList.appendChild(wrapper);
        });

        renderPager(totalPages);
    };

    function renderPager(totalPages) {
        // create a basic pager below the event list
        let pager = document.getElementById('eventPager');
        if (!pager) {
            pager = document.createElement('div');
            pager.id = 'eventPager';
            pager.style.textAlign = 'center';
            pager.style.marginTop = '16px';
            eventList.parentNode.insertBefore(pager, eventList.nextSibling);
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
            if (p.event_cat) cats[p.event_cat] = p.category || p.event_cat;
        });

        categoryFilter.innerHTML = '<option value="">Filter by Category</option>';
        Object.keys(cats).forEach(id => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = cats[id];
            categoryFilter.appendChild(opt);
        });

    }

    function loadAllEvents() {
        fetch('../actions/fetch_event_action.php')
            .then(res => res.json())
            .then(response => {
                console.log('Events response:', response);
                // Handle both formats: {status, data} or plain array
                if (response && response.status === 'success' && Array.isArray(response.data)) {
                    events = response.data;
                } else if (Array.isArray(response)) {
                    events = response;
                } else {
                    events = [];
                }
                renderEvents(events, 1);
                populateFilters(events);

                // Apply URL parameters if present
                applyURLParams();
            })
            .catch(err => {
                console.error('Failed to load events', err);
                if (eventList) eventList.innerHTML = '<p>Cannot load items</p>';
            });
    }

    function applyURLParams() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        const categoryParam = urlParams.get('category');

        if (searchParam && searchBox) {
            searchBox.value = searchParam;
        }
        if (categoryParam && categoryFilter) {
            categoryFilter.value = categoryParam;
        }

        // Apply filters if any URL parameters were present
        if (searchParam || categoryParam) {
            applyFilters(1);
        }
    }

    function applyFilters(page = 1) {
        const q = (searchBox && searchBox.value || '').toLowerCase();
        const cat = categoryFilter ? categoryFilter.value : '';

        const filtered = events.filter(p => {
            if (cat && String(p.event_cat) !== String(cat)) return false;
            if (q) {
                const hay = ((p.event_desc||'') + ' ' + (p.category||'') + ' ' + (p.event_location||'')).toLowerCase();
                return hay.indexOf(q) !== -1;
            }
            return true;
        });

        renderEvents(filtered, page);
    }

    if (searchBtn) searchBtn.addEventListener('click', () => applyFilters(1));
    if (categoryFilter) categoryFilter.addEventListener('change', () => applyFilters(1));

    loadAllEvents();

});
