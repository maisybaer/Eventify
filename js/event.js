document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#productTable tbody") || document.querySelector("#eventTable tbody");

//-------------------------------------
//ALL JS FOR ADMIN event PAGE
//-------------------------------------

    // ADD event — support admin form field names and legacy names
    const $addForm = $('#addEventForm').length ? $('#addEventForm') : ($('#addeventForm').length ? $('#addeventForm') : null);
    if ($addForm) {
        $addForm.submit(function(e) {
            e.preventDefault();

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

            const eventCat = getVal(['#event_cat','#eventCat']);
            const eventDes = getVal(['#event_desc','#eventDes']);
            const eventLoc = getVal(['#event_location','#eventLocation']);
            const eventStart = getVal(['#event_start','#eventStart']);
            const eventEnd = getVal(['#event_end','#eventEnd']);
            const eventPrice = getVal(['#eventPrice','#event_price']) || '';
            const eventKey = getVal(['#eventKey']) || '';
            const user_id = getVal(['#user_id']) || '';
            const flyer = getFile(['#flyer','#flyerInput']);
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
            formData.append('eventStart', eventStart);
            formData.append('eventEnd', eventEnd);
            formData.append('eventStart', eventStart);
            formData.append('eventEnd', eventEnd);
            formData.append('eventPrice', eventPrice);
            formData.append('eventKey', eventKey);
            formData.append('user_id', user_id);
            if (flyer) formData.append('flyer', flyer);

            $.ajax({
                url: '../actions/add_event_action.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Success', text: response.message }).then((result) => {
                            if (result.isConfirmed) window.location.href = 'event.php';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Oops...', text: response.message });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'An error occurred while adding the event! Please try again later.' });
                }
            });
        });
    }


    // UPDATE event (Pop up form)
    let currentUpdateEventId = null;

function createUpdatePopup() {
    if (document.getElementById("updatePopupContainer")) return;

    const popup = document.createElement("div");
    popup.className = "form-popup";
    popup.id = "updatePopupContainer";
    popup.style.display = "none";

    popup.innerHTML = `
        <div class="card" style="max-width:auto;margin:auto;">
            <div class="card-body">
                <form id="updateeventForm">
                    <div class="mb-3">
                        <input type="hidden" id="updateeventID">

                        <label for="updateEventCat" class="form-label">New event Category</label>
                        <select class="form-control" id="updateEventCat" name="updateEventCat" required>
                            <option value="">Select Category</option>
                        </select>

                        <label for="updateEventTitle" class="form-label">New event Title</label>
                        <input type="text" class="form-control" id="updateEventTitle" name="updateEventTitle" required>

                        <label for="updateEventDes" class="form-label">New event Description</label>
                        <textarea class="form-control" id="updateEventDes" name="updateEventDesc" required></textarea>

                        <label for="updateEventPrice" class="form-label">New event Price</label>
                        <input type="number" step="0.01" class="form-control" id="updateEventPrice" name="updateEventPrice" required>

                        <label for="updateEventLocation" class="form-label">New event Location</label>
                        <input type="text" class="form-control" id="updateEventLocation" name="updateEventLocation" required>

                        <label for="updateEventDate" class="form-label">New event Date</label>
                        <input type="date" class="form-control" id="updateEventDate" name="updateEventDate" required>

                        <label for="updateEventStart" class="form-label">New event Start Time</label>
                        <input type="time" class="form-control" id="updateEventStart" name="updateEventStart" required>

                        <label for="updateEventEnd" class="form-label">New event End Time</label>
                        <input type="time" class="form-control" id="updateEventEnd" name="updateEventEnd" required>

                        <label for="updateflyer" class="form-label">New event Flyer</label>
                        <input type="file" class="form-control" id="updateflyer" name="updateflyer">

                        <label for="updateEventKey" class="form-label">New event Keywords</label>
                        <input type="text" class="form-control" id="updateEventKey" name="updateEventKey" required>
                    </div>
                    <button type="submit" class="btn btn-custom w-100">Update</button>
                    <button type="button" class="btn btn-secondary w-100 mt-2" id="closeEditPopup">Cancel</button>
                </form>
            </div>
        </div>
    `;

    document.body.appendChild(popup);

    document.getElementById("closeEditPopup").onclick = closeForm;

    const updateFormEl = document.getElementById("updateeventForm") || document.getElementById('updateProductForm');
    if (updateFormEl) {
        updateFormEl.onsubmit = function(e) {
            e.preventDefault();

            const read = (ids) => { for (const id of ids) { const el = document.getElementById(id); if (el) return el.value; } return ''; };
            const getFile = (ids) => { for (const id of ids) { const el = document.getElementById(id); if (el && el.files && el.files[0]) return el.files[0]; } return null; };

            const eventCat = read(['updateEventCat','updateProductCat']);
            const eventPrice = read(['updateEventPrice','updateProductPrice']) || '';
            const eventDes = read(['updateEventDes','updateProductDes','updateProductDesc']);
            const eventStart = read(['updateEventStart']);
            const eventEnd = read(['updateEventEnd']);
            const eventKey = read(['updateEventKey','updateProductKey']) || '';
            const imageFile = getFile(['updateflyer','updateProductImage']);

            const formData = new FormData();
            formData.append('event_id', currentUpdateEventId);
            formData.append('eventCat', eventCat);
            formData.append('eventPrice', eventPrice);
            formData.append('eventDes', eventDes);
            formData.append('eventStart', eventStart);
            formData.append('eventEnd', eventEnd);
            formData.append('eventKey', eventKey);
            if (imageFile) formData.append('flyer', imageFile);

            fetch('../actions/update_event_action.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(resp => {
                    Swal.fire({ icon: resp.status === 'success' ? 'success' : 'error', title: resp.status === 'success' ? 'Updated!' : 'Error', text: resp.message })
                        .then(() => { closeForm(); loadevents(); });
                })
                .catch(err => { console.error('Update Error:', err); Swal.fire({ icon: 'error', title: 'Error', text: 'Update failed' }); });
        };
    }
}

function openForm(event) {
    // Accept either an object or a JSON string
    if (typeof event === 'string') {
        try { event = JSON.parse(event); } catch (e) { console.error('Invalid event JSON', e); return; }
    }

    currentUpdateEventId = event.event_id;

    createUpdatePopup();

    // Populate hidden id
    const idEl = document.getElementById('updateeventID'); if (idEl) idEl.value = event.event_id || '';

    // populate both updateEvent* and updateProduct* fields if present (compat)
    const setIf = (ids, value) => { ids.forEach(id=>{ const el=document.getElementById(id); if(el) el.value = value; }); };
    setIf(['updateEventTitle','updateProductTitle'], event.event_desc || event.eventDes || '');
    setIf(['updateEventPrice','updateProductPrice'], event.event_price || event.eventPrice || '');
    setIf(['updateEventDes','updateProductDes','updateProductDesc'], event.event_desc || event.eventDes || '');
    setIf(['updateEventLocation','updateProductLocation'], event.event_location || event.eventLoc || '');
    setIf(['updateEventStart'], event.event_start || event.eventStart || '');
    setIf(['updateEventEnd'], event.event_end || event.eventEnd || '');
    setIf(['updateEventKey','updateProductKey'], event.event_keywords || event.eventKey || '');
    setIf(['updateEventCat','updateProductCat'], event.event_cat || event.eventCat || '');

    // Populate dropdowns (pass ids) - wait for them to load before showing popup so selection is visible
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

function closeForm() {
    let popup = document.getElementById("updatePopupContainer");
    if (popup) popup.style.display = "none";
}


    // FETCH eventS (admin table)
    function loadevents() {
        fetch("../actions/fetch_event_action.php")
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = "";
                if (!Array.isArray(data) || data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="9" class="text-center">No event available</td></tr>`;
                    return;
                }

                data.forEach(event => {
                    const tr = document.createElement('tr');

                    const tdId = document.createElement('td'); tdId.textContent = event.event_id;
                    const tdCat = document.createElement('td'); tdCat.textContent = event.category || event.cat_name || event.event_cat || 'N/A';
                    const tdTitle = document.createElement('td'); tdTitle.textContent = event.event_desc || event.event_title || '';
                    const tdPrice = document.createElement('td'); tdPrice.textContent = event.event_price;
                    const tdDesc = document.createElement('td'); tdDesc.textContent = event.event_desc;
                    const tdLoc = document.createElement('td'); tdLoc.textContent = event.event_location;
                    const tdDate = document.createElement('td'); tdDate.textContent = (event.event_start || '') + (event.event_start && event.event_end ? ' - ' : '') + (event.event_end || '');
                    const tdStart = document.createElement('td'); tdStart.textContent = event.event_start;
                    const tdEnd = document.createElement('td'); tdEnd.textContent = event.event_end;

                    const tdImg = document.createElement('td');
                    const img = document.createElement('img');

                    // Choose src: prefer server-provided image_url, else construct from flyer
                    let srcCandidate = event.image_url || (event.flyer ? ('uploads/' + event.flyer) : 'uploads/no-image.svg');
                    if (/^https?:\/\//i.test(srcCandidate)) {
                        img.src = srcCandidate;
                    } else if (srcCandidate.startsWith('/')) {
                        img.src = srcCandidate; // root-relative
                    } else if (srcCandidate.startsWith('uploads/') || srcCandidate.startsWith('../uploads/')) {
                        img.src = '../' + srcCandidate.replace(/^\.\.?\//, '');
                    } else {
                        img.src = '../uploads/' + srcCandidate.replace(/^\.\/+/, '');
                    }

                    img.width = 50;
                    img.alt = event.event_desc || event.event_title || '';
                    img.onerror = function () { this.onerror = null; this.src = '../uploads/no-image.svg'; };
                    tdImg.appendChild(img);

                    const tdKey = document.createElement('td'); tdKey.textContent = event.event_keywords || '';
                    const tdActions = document.createElement('td');

                    const editBtn = document.createElement('button'); editBtn.className = 'btn btn-sm btn-custom'; editBtn.textContent = 'Edit';
                    // Pass the event object to the event edit opener. Prefer event-specific opener if present.
                    editBtn.addEventListener('click', () => {
                        const opener = window.openeventEditForm || window.openForm;
                        if (opener) opener(event);
                    });
                    const delBtn = document.createElement('button'); delBtn.className = 'btn btn-sm btn-danger'; delBtn.textContent = 'Delete';
                    delBtn.addEventListener('click', () => { if (window.deleteevent) window.deleteevent(event.event_id); });

                    tdActions.appendChild(editBtn); tdActions.appendChild(document.createTextNode(' ')); tdActions.appendChild(delBtn);

                    tr.appendChild(tdId);
                    tr.appendChild(tdCat);
                    tr.appendChild(tdTitle);
                    tr.appendChild(tdPrice);
                    tr.appendChild(tdDesc);
                    tr.appendChild(tdImg);
                    tr.appendChild(tdLoc);
                    tr.appendChild(tdDate);
                    tr.appendChild(tdStart);
                    tr.appendChild(tdEnd);
                    tr.appendChild(tdKey);
                    tr.appendChild(tdActions);

                    tableBody.appendChild(tr);
                });
            })
            .catch(err => {
                console.error('Failed to load admin events', err);
                tableBody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Error loading events.</td></tr>`;
            });
    }

function populateDropdowns(eventMeta = {}) {
        const {
            event_cat: catId,
            eventCat,
            category: catName,
        } = eventMeta || {};

        const desiredCatId = catId ?? eventCat ?? '';
        const desiredCatName = (catName ?? '').toString().trim().toLowerCase();
        const catPromise = fetch("../actions/fetch_category_action.php")
            .then(res => res.json())
            .then(cats => {
                const catSelect = document.getElementById("updateeventCat");
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

                // Fallback: match by name if id selection failed
                if (!catSelect.value && desiredCatName) {
                    const match = Array.from(catSelect.options).find(opt => opt.textContent.trim().toLowerCase() === desiredCatName);
                    if (match) catSelect.value = match.value;
                }
            });

        return Promise.all([catPromise]);
    }

//Delete event
    window.deleteevent = function (event_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the event.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
        }).then(result => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append("event_id", event_id);

                fetch("../actions/delete_event_action.php", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(resp => {
                        Swal.fire(resp.status === 'success' ? 'Deleted!' : 'Error', resp.message, resp.status);
                        loadevents();
                    });
            }
        });
    };

    // Expose event-specific global and also assign to openForm as a fallback
    window.openeventEditForm = openForm;
    if (!window.openForm) window.openForm = openForm;

    createUpdatePopup();
    closeForm();
    // Only try to load admin events if the table is present on the page
    if (tableBody) loadevents();


    
});
