// Unified Event Management Script
// Handles both admin and user-facing event operations
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#productTable tbody') || document.querySelector('#eventTable tbody');
    let currentUpdateEventId = null;

    // Helper functions to get form values from multiple possible selectors
    const getVal = (selectors) => {
        for (const s of selectors) {
            const el = document.querySelector(s);
            if (el) return el.value;
        }
        return '';
    };

    const getFile = (selectors) => {
        for (const s of selectors) {
            const el = document.querySelector(s);
            if (el && el.files && el.files[0]) return el.files[0];
        }
        return null;
    };

    // ========== ADD EVENT FORM HANDLER ==========
    const addForm = document.getElementById('addEventForm') || document.getElementById('addeventForm');
    if (addForm) {
        addForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const eventCat = getVal(['#event_cat', '#eventCat']);
            const eventDes = getVal(['#event_desc', '#eventDes']);
            const eventLoc = getVal(['#event_location', '#eventLocation']);
            const eventDate = getVal(['#event_date']) || '';
            const eventStart = getVal(['#event_start', '#eventStart']);
            const eventEnd = getVal(['#event_end', '#eventEnd']);
            const eventPrice = getVal(['#eventPrice', '#event_price']) || '';
            const eventKey = getVal(['#eventKey']) || '';
            const user_id = getVal(['#user_id']) || '';
            const flyer = getFile(['#flyer', '#flyerInput']);

            // Validation
            if (!eventCat || !eventDes || !eventLoc || !eventStart || !eventEnd) {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please fill in the required fields!' });
                return;
            }

            if (eventPrice !== '' && !/^[0-9]+(\.[0-9]+)?$/.test(eventPrice)) {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'The price must be a number!' });
                return;
            }

            const formData = new FormData();
            formData.append('eventCat', eventCat);
            formData.append('eventDes', eventDes);
            formData.append('eventLocation', eventLoc);
            formData.append('eventDate', eventDate);
            formData.append('eventStart', eventStart);
            formData.append('eventEnd', eventEnd);
            formData.append('eventPrice', eventPrice);
            formData.append('eventKey', eventKey);
            formData.append('user_id', user_id);
            if (flyer) formData.append('flyer', flyer);

            // Use jQuery if available, otherwise use fetch
            if (window.jQuery && typeof $.ajax === 'function') {
                $.ajax({
                    url: '../actions/add_event_action.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json'
                }).done(function (response) {
                    if (response.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Success', text: response.message }).then(() => {
                            window.location.href = 'event.php';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to add event' });
                    }
                }).fail(function (xhr, status, err) {
                    console.error('Add Event error', status, err, xhr.responseText);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while adding the event.' });
                });
            } else {
                fetch('../actions/add_event_action.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(response => {
                        if (response.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'Success', text: response.message }).then(() => {
                                window.location.href = 'event.php';
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to add event' });
                        }
                    })
                    .catch(err => {
                        console.error('Add Event fetch error', err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while adding the event.' });
                    });
            }
        });
    }

    // ========== UPDATE POPUP CREATION ==========
    function createUpdatePopup() {
        if (document.getElementById('updatePopupContainer')) return;

        const popup = document.createElement('div');
        popup.className = 'form-popup';
        popup.id = 'updatePopupContainer';
        popup.style.display = 'none';

        popup.innerHTML = `
            <div class="card" style="max-width:auto;margin:auto;">
                <div class="card-body">
                    <form id="updateeventForm">
                        <div class="mb-3">
                            <input type="hidden" id="updateeventID">

                            <label for="updateEventCat" class="form-label">Event Category</label>
                            <select class="form-control" id="updateEventCat" name="updateEventCat" required>
                                <option value="">Select Category</option>
                            </select>

                            <label for="updateEventTitle" class="form-label">Event Title</label>
                            <input type="text" class="form-control" id="updateEventTitle" name="updateEventTitle" required>

                            <label for="updateEventDes" class="form-label">Event Description</label>
                            <textarea class="form-control" id="updateEventDes" name="updateEventDesc" required></textarea>

                            <label for="updateEventPrice" class="form-label">Event Price</label>
                            <input type="number" step="0.01" class="form-control" id="updateEventPrice" name="updateEventPrice" required>

                            <label for="updateEventLocation" class="form-label">Event Location</label>
                            <input type="text" class="form-control" id="updateEventLocation" name="updateEventLocation" required>

                            <label for="updateEventDate" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="updateEventDate" name="updateEventDate">

                            <label for="updateEventStart" class="form-label">Event Start Time</label>
                            <input type="time" class="form-control" id="updateEventStart" name="updateEventStart" required>

                            <label for="updateEventEnd" class="form-label">Event End Time</label>
                            <input type="time" class="form-control" id="updateEventEnd" name="updateEventEnd" required>

                            <label for="updateflyer" class="form-label">Event Flyer</label>
                            <input type="file" class="form-control" id="updateflyer" name="updateflyer">

                            <label for="updateEventKey" class="form-label">Event Keywords</label>
                            <input type="text" class="form-control" id="updateEventKey" name="updateEventKey">
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Update</button>
                        <button type="button" class="btn btn-secondary w-100 mt-2" id="closeEditPopup">Cancel</button>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(popup);
        document.getElementById('closeEditPopup').onclick = closeForm;

        // UPDATE FORM SUBMIT HANDLER
        const updateFormEl = document.getElementById('updateeventForm');
        if (updateFormEl) {
            updateFormEl.onsubmit = function (e) {
                e.preventDefault();

                const read = (ids) => {
                    for (const id of ids) {
                        const el = document.getElementById(id);
                        if (el) return el.value;
                    }
                    return '';
                };

                const getFileLocal = (ids) => {
                    for (const id of ids) {
                        const el = document.getElementById(id);
                        if (el && el.files && el.files[0]) return el.files[0];
                    }
                    return null;
                };

                const eventCat = read(['updateEventCat', 'updateProductCat']);
                const eventPrice = read(['updateEventPrice', 'updateProductPrice']) || '';
                const eventDes = read(['updateEventDes', 'updateProductDes', 'updateProductDesc']);
                const eventLoc = read(['updateEventLocation', 'updateProductLocation']);
                const eventDate = read(['updateEventDate']) || '';
                const eventStart = read(['updateEventStart']);
                const eventEnd = read(['updateEventEnd']);
                const eventKey = read(['updateEventKey', 'updateProductKey']) || '';
                const imageFile = getFileLocal(['updateflyer', 'updateProductImage']);

                const formData = new FormData();
                formData.append('event_id', currentUpdateEventId);
                formData.append('eventCat', eventCat);
                formData.append('eventPrice', eventPrice);
                formData.append('eventDes', eventDes);
                formData.append('eventLocation', eventLoc);
                formData.append('eventDate', eventDate);
                formData.append('eventStart', eventStart);
                formData.append('eventEnd', eventEnd);
                formData.append('eventKey', eventKey);
                if (imageFile) formData.append('flyer', imageFile);

                fetch('../actions/update_event_action.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(resp => {
                        Swal.fire({
                            icon: resp.status === 'success' ? 'success' : 'error',
                            title: resp.status === 'success' ? 'Updated!' : 'Error',
                            text: resp.message
                        }).then(() => {
                            closeForm();
                            loadevents();
                        });
                    })
                    .catch(err => {
                        console.error('Update Error:', err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Update failed' });
                    });
            };
        }
    }

    // ========== OPEN EDIT FORM ==========
    function openForm(event) {
        if (typeof event === 'string') {
            try {
                event = JSON.parse(event);
            } catch (e) {
                console.error('Invalid event JSON', e);
                return;
            }
        }

        currentUpdateEventId = event.event_id;
        createUpdatePopup();

        const idEl = document.getElementById('updateeventID');
        if (idEl) idEl.value = event.event_id || '';

        const setIf = (ids, value) => {
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = value;
            });
        };

        setIf(['updateEventTitle', 'updateProductTitle'], event.event_desc || event.eventDes || '');
        setIf(['updateEventPrice', 'updateProductPrice'], event.event_price || event.eventPrice || '');
        setIf(['updateEventDes', 'updateProductDes', 'updateProductDesc'], event.event_desc || event.eventDes || '');
        setIf(['updateEventLocation', 'updateProductLocation'], event.event_location || event.eventLoc || '');
        setIf(['updateEventStart'], event.event_start || event.eventStart || '');
        setIf(['updateEventEnd'], event.event_end || event.eventEnd || '');
        setIf(['updateEventKey', 'updateProductKey'], event.event_keywords || event.eventKey || '');
        setIf(['updateEventCat', 'updateProductCat'], event.event_cat || event.eventCat || '');
        setIf(['updateEventDate'], event.event_date || event.eventDate || '');

        populateDropdowns(event)
            .then(() => {
                const popup = document.getElementById('updatePopupContainer');
                if (popup) popup.style.display = 'block';
            })
            .catch(err => {
                console.error('populateDropdowns error:', err);
                const popup = document.getElementById('updatePopupContainer');
                if (popup) popup.style.display = 'block';
            });
    }

    // ========== CLOSE EDIT FORM ==========
    function closeForm() {
        const popup = document.getElementById('updatePopupContainer');
        if (popup) popup.style.display = 'none';
    }

    // ========== LOAD EVENTS TABLE ==========
    function loadevents() {
        if (!tableBody) return;

        fetch('../actions/fetch_event_action.php')
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = '';
                if (!Array.isArray(data) || data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center">No event available</td></tr>`;
                    return;
                }

                data.forEach(event => {
                    const tr = document.createElement('tr');

                    // Event Image
                    const tdImg = document.createElement('td');
                    const img = document.createElement('img');
                    let srcCandidate = event.image_url || (event.flyer ? ('uploads/' + event.flyer) : 'uploads/no-image.svg');

                    if (/^https?:\/\//i.test(srcCandidate)) {
                        img.src = srcCandidate;
                    } else if (srcCandidate.startsWith('/')) {
                        img.src = srcCandidate;
                    } else if (srcCandidate.startsWith('uploads/') || srcCandidate.startsWith('../uploads/')) {
                        img.src = '../' + srcCandidate.replace(/^\.\.?\//, '');
                    } else {
                        img.src = '../uploads/' + String(srcCandidate).replace(/^\.\//, '');
                    }

                    img.width = 50;
                    img.alt = event.event_desc || '';
                    tdImg.appendChild(img);

                    // Event Details
                    const tdTitle = document.createElement('td');
                    tdTitle.textContent = event.event_desc || '';

                    const tdCat = document.createElement('td');
                    tdCat.textContent = event.category || event.cat_name || event.event_cat || 'N/A';

                    const tdLoc = document.createElement('td');
                    tdLoc.textContent = event.event_location || '';

                    const tdDate = document.createElement('td');
                    tdDate.textContent = event.event_date || '';

                    const tdTime = document.createElement('td');
                    tdTime.textContent = (event.event_start || '') + (event.event_start && event.event_end ? ' - ' : '') + (event.event_end || '');

                    const tdPrice = document.createElement('td');
                    tdPrice.textContent = event.event_price || '';

                    // Action Buttons
                    const tdActions = document.createElement('td');

                    const editBtn = document.createElement('button');
                    editBtn.className = 'btn btn-sm btn-custom';
                    editBtn.textContent = 'Edit';
                    editBtn.addEventListener('click', () => {
                        const opener = window.openeventEditForm || window.openForm;
                        if (opener) opener(event);
                    });

                    const delBtn = document.createElement('button');
                    const isOwner = (typeof window.currentUserId !== 'undefined' && event.added_by && parseInt(event.added_by) === parseInt(window.currentUserId));
                    const isAdmin = (typeof window.currentUserRole !== 'undefined' && parseInt(window.currentUserRole) === 1);

                    if (isOwner || isAdmin) {
                        delBtn.className = 'btn btn-sm btn-danger';
                        delBtn.textContent = 'Delete';
                        delBtn.addEventListener('click', () => {
                            if (window.deleteevent) window.deleteevent(event.event_id);
                        });
                    } else {
                        delBtn.className = 'btn btn-sm btn-outline-secondary disabled';
                        delBtn.textContent = 'Delete';
                        delBtn.title = 'You are not authorized to delete this event';
                        delBtn.setAttribute('aria-disabled', 'true');
                    }

                    tdActions.appendChild(editBtn);
                    tdActions.appendChild(document.createTextNode(' '));
                    tdActions.appendChild(delBtn);

                    tr.appendChild(tdImg);
                    tr.appendChild(tdTitle);
                    tr.appendChild(tdCat);
                    tr.appendChild(tdLoc);
                    tr.appendChild(tdDate);
                    tr.appendChild(tdTime);
                    tr.appendChild(tdPrice);
                    tr.appendChild(tdActions);

                    tableBody.appendChild(tr);
                });
            })
            .catch(err => {
                console.error('Failed to load admin events', err);
                if (tableBody) {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Error loading events.</td></tr>`;
                }
            });
    }

    // ========== POPULATE CATEGORY DROPDOWN ==========
    function populateDropdowns(eventMeta = {}) {
        const { event_cat: catId, eventCat, category: catName } = eventMeta || {};
        const desiredCatId = catId ?? eventCat ?? '';
        const desiredCatName = (catName ?? '').toString().trim().toLowerCase();

        return fetch('../actions/fetch_category_action.php')
            .then(res => res.json())
            .then(cats => {
                const catSelect = document.getElementById('updateEventCat');
                if (!catSelect) return;

                catSelect.innerHTML = `<option value="">Select Category</option>`;
                cats.forEach(c => {
                    const option = document.createElement('option');
                    option.value = c.cat_id;
                    option.textContent = c.cat_name;
                    catSelect.appendChild(option);
                });

                if (desiredCatId !== undefined && desiredCatId !== null && desiredCatId !== '') {
                    catSelect.value = String(desiredCatId);
                }

                if (!catSelect.value && desiredCatName) {
                    const match = Array.from(catSelect.options).find(opt => opt.textContent.trim().toLowerCase() === desiredCatName);
                    if (match) catSelect.value = match.value;
                }
            });
    }

    // ========== DELETE EVENT ==========
    window.deleteevent = function (event_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the event.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('event_id', event_id);

                fetch('../actions/delete_event_action.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(resp => {
                        Swal.fire(
                            resp.status === 'success' ? 'Deleted!' : 'Error',
                            resp.message,
                            resp.status
                        );
                        loadevents();
                    })
                    .catch(err => {
                        console.error('Delete error:', err);
                        Swal.fire('Error', 'Failed to delete event', 'error');
                    });
            }
        });
    };

    // ========== INITIALIZE ==========
    window.openeventEditForm = openForm;
    if (!window.openForm) window.openForm = openForm;

    createUpdatePopup();
    closeForm();

    if (tableBody) loadevents();
});
