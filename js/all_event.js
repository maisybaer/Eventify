document.addEventListener('DOMContentLoaded', () => {
    const eventList = document.getElementById('eventList');
    const typeFilter = document.getElementById('typeFilter');
    const searchBox = document.getElementById('searchBox');
    const searchBtn = document.getElementById('searchBtn');
    let allEvents = [];
    const perPage = 12;
    let currentPage = 1;

    function renderEvents(events, page = 1) {
        if (!eventList) return;
        eventList.innerHTML = '';
        
        if (!events || events.length === 0) {
            eventList.innerHTML = '<div class="text-center py-5"><h4>No events found</h4><p class="text-muted">Try adjusting your search criteria</p></div>';
            renderPager(0);
            return;
        }

        const totalPages = Math.ceil(events.length / perPage) || 1;
        page = Math.max(1, Math.min(page, totalPages));
        currentPage = page;
        const start = (page - 1) * perPage;
        const slice = events.slice(start, start + perPage);

        slice.forEach(event => {
            const card = document.createElement('div');
            card.className = 'product-card animate__animated animate__fadeInUp';

            // Determine image source
            let imgSrc = '';
            const fallback = '../uploads/no-image.svg';
            if (event.flyer) {
                imgSrc = event.flyer.startsWith('uploads/') ? '../' + event.flyer : '../uploads/' + event.flyer.replace(/^\/+/, '');
            }

            // Format date and time
            const eventDate = event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }) : '';
            
            const timeRange = (event.event_start && event.event_end) ? 
                `${event.event_start} - ${event.event_end}` : 
                event.event_start || '';

            card.innerHTML = `
                <div class="card h-100 shadow-sm hover-lift">
                    <div class="event-image-container" style="height: 200px; overflow: hidden;">
                        <img src="${imgSrc || fallback}" 
                             alt="${event.event_name || 'Event'}" 
                             class="card-img-top w-100 h-100" 
                             style="object-fit: cover;"
                             onerror="this.onerror=null;this.src='${fallback}'">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2">${event.event_name || 'Untitled Event'}</h5>
                        
                        <div class="mb-2">
                            <small class="text-primary"><i class="fas fa-tag me-1"></i>${event.category || event.cat_name || 'General'}</small>
                        </div>
                        
                        ${eventDate ? `
                        <div class="mb-2">
                            <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i>${eventDate}</small>
                        </div>` : ''}
                        
                        ${timeRange ? `
                        <div class="mb-2">
                            <small class="text-muted"><i class="fas fa-clock me-1"></i>${timeRange}</small>
                        </div>` : ''}
                        
                        ${event.event_location ? `
                        <div class="mb-3">
                            <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${event.event_location}</small>
                        </div>` : ''}
                        
                        <div class="mt-auto">
                            <a href="single_event.php?event_id=${event.event_id || event.id}" 
                               class="btn btn-custom w-100">
                                <i class="fas fa-ticket-alt me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            `;

            eventList.appendChild(card);
        });

        renderPager(totalPages);
    }

    function renderPager(totalPages) {
        let pager = document.getElementById('eventPager');
        if (!pager) {
            pager = document.createElement('div');
            pager.id = 'eventPager';
            pager.className = 'text-center mt-4';
            eventList.parentNode.appendChild(pager);
        }
        
        pager.innerHTML = '';
        if (totalPages <= 1) return;

        const pagination = document.createElement('nav');
        const ul = document.createElement('ul');
        ul.className = 'pagination justify-content-center';

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>`;
        ul.appendChild(prevLi);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            ul.appendChild(li);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>`;
        ul.appendChild(nextLi);

        pagination.appendChild(ul);
        pager.appendChild(pagination);

        // Add click handlers
        pager.addEventListener('click', (e) => {
            e.preventDefault();
            if (e.target.classList.contains('page-link') && !e.target.parentNode.classList.contains('disabled')) {
                const page = parseInt(e.target.dataset.page);
                performSearch(page);
            }
        });
    }

    function loadCategories() {
        fetch('../actions/fetch_category_action.php')
            .then(res => res.json())
            .then(categories => {
                if (typeFilter && Array.isArray(categories)) {
                    typeFilter.innerHTML = '<option value="">All Event Types</option>';
                    categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.cat_id;
                        option.textContent = cat.cat_name;
                        typeFilter.appendChild(option);
                    });
                }
            })
            .catch(err => {
                console.error('Failed to load categories:', err);
            });
    }

    function performSearch(page = 1) {
        const query = searchBox ? searchBox.value.trim() : '';
        const category = typeFilter ? typeFilter.value : '';
        
        // Build URL parameters
        const params = new URLSearchParams();
        if (query) params.append('q', query);
        if (category) params.append('category', category);
        
        const url = `../actions/search_events_action.php${params.toString() ? '?' + params.toString() : ''}`;
        
        fetch(url)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    allEvents = response.data || [];
                    renderEvents(allEvents, page);
                } else {
                    throw new Error(response.message || 'Search failed');
                }
            })
            .catch(err => {
                console.error('Search failed:', err);
                if (eventList) {
                    eventList.innerHTML = '<div class="text-center py-5"><h4>Search Failed</h4><p class="text-muted">Please try again later</p></div>';
                }
            });
    }

    function loadAllEvents() {
        performSearch(1); // Load all events initially
    }

    // Event listeners
    if (searchBtn) {
        searchBtn.addEventListener('click', () => performSearch(1));
    }
    
    if (searchBox) {
        searchBox.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(1);
            }
        });
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', () => performSearch(1));
    }

    // Initialize
    loadCategories();
    loadAllEvents();
});
