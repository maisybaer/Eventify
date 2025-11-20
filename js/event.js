document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#eventTable tbody");

    // Add Event
    $('#addEventForm').submit(function(e) {
        e.preventDefault();

        let event_name = $('#event_name').val();
        let event_location = $('#event_location').val();
        let event_date = $('#event_date').val();
        let event_start = $('#event_start').val();
        let event_end = $('#event_end').val();
        let event_desc = $('#event_desc').val();
        let flyer = $('#flyer')[0].files[0];
        let event_cat = $('#event_cat').val();
        let user_id = $('#user_id').val();

        if (event_name === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter the event name.',
            });
            return;
        }

        let formData = new FormData();
        formData.append('event_name', event_name);
        formData.append('event_location', event_location);
        formData.append('event_date', event_date);
        formData.append('event_start', event_start);
        formData.append('event_end', event_end);
        formData.append('event_desc', event_desc);
        formData.append('event_cat', event_cat);
        formData.append('flyer', flyer);
        formData.append('user_id', user_id);

        fetch("../actions/add_event_action.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            const icon = resp.status === 'success' ? 'success' : 'error';
            Swal.fire({ icon: icon, title: resp.status === 'success' ? 'Event Added!' : 'Error', text: resp.message });
            if (resp.status === 'success') {
                loadEvent();
                $('#addEventForm')[0].reset();
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network or server error.' }));
    });

    // Update Event (Popup)
    let currentEditEventId = null;

    function createEditPopup() {
        if (document.getElementById("updateEventForm")) return;

        let popup = document.createElement("div");
        popup.className = "form-popup";
        popup.id = "updateEventForm";
        popup.style.display = "none";

        popup.innerHTML = `
            <div class="card" style="max-width:450px;margin:auto;">
                <div class="card-body">
                    <form id="editEventForm">
                        <div class="mb-3">
                            <label class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="edit_event_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" id="edit_event_location" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="edit_event_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="edit_event_start" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-control" id="edit_event_end" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="edit_event_desc"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-control" id="edit_event_cat" required>
                                <option value="">Loading categories...</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-custom w-100">Update Event</button>
                        <button type="button" class="btn btn-secondary w-100 mt-2" id="closeEditPopup">Cancel</button>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(popup);

        document.getElementById("closeEditPopup").onclick = closeForm;

        document.getElementById("editEventForm").onsubmit = function(e) {
            e.preventDefault();

            let formData = new FormData();
            formData.append("event_id", currentEditEventId);
            formData.append("event_name", document.getElementById("edit_event_name").value);
            formData.append("event_location", document.getElementById("edit_event_location").value);
            formData.append("event_date", document.getElementById("edit_event_date").value);
            formData.append("event_start", document.getElementById("edit_event_start").value);
            formData.append("event_end", document.getElementById("edit_event_end").value);
            formData.append("event_desc", document.getElementById("edit_event_desc").value);
            formData.append("event_cat", document.getElementById("edit_event_cat").value);

            fetch("../actions/update_event_action.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(resp => {
                const icon = resp.status === 'success' ? 'success' : 'error';
                Swal.fire({ icon: icon, title: resp.status === 'success' ? 'Updated!' : 'Error', text: resp.message });
                if (resp.status === 'success') { closeForm(); loadEvent(); }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network or server error.' }));
        };
    }

    function openForm(event) {
        currentEditEventId = event.event_id;

        document.getElementById("edit_event_name").value = event.event_name;
        document.getElementById("edit_event_location").value = event.event_location;
        document.getElementById("edit_event_date").value = event.event_date;
        document.getElementById("edit_event_start").value = event.event_start;
        document.getElementById("edit_event_end").value = event.event_end;
        document.getElementById("edit_event_desc").value = event.event_desc;

        // Set category select (value should be category id)
        const editCatSelect = document.getElementById("edit_event_cat");
        if (editCatSelect) {
            // ensure options are loaded then set value
            const trySet = () => {
                const opts = editCatSelect.querySelectorAll('option');
                if (opts.length > 0) {
                    // try set by value first
                    editCatSelect.value = event.event_cat;
                    if (editCatSelect.value === '' && event.event_cat) {
                        // fallback: try to match by text
                        for (let o of opts) {
                            if ((o.textContent || '').trim() === (event.event_cat || '').trim()) {
                                editCatSelect.value = o.value;
                                break;
                            }
                        }
                    }
                } else {
                    setTimeout(trySet, 100);
                }
            };
            trySet();
        }

        document.getElementById("updateEventForm").style.display = "block";
    }

    function closeForm() {
        document.getElementById("updateEventForm").style.display = "none";
    }

    // Fetch Events
    function loadEvent() {
        fetch("../actions/fetch_event_action.php")
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = "";
                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="6">No Events Available</td></tr>`;
                } else {
                    data.forEach(event => {
                        let row = `
                            <tr>
                                <td>${event.event_name}</td>
                                <td>${event.event_location}</td>
                                <td>${event.event_date}</td>
                                <td>${event.event_start} - ${event.event_end}</td>
                                <td>${event.event_cat}</td>
                                <td>
                                    <button class="btn btn-custom btn-sm" 
                                        onclick='openForm(${JSON.stringify(event)})'>
                                        Edit
                                    </button>

                                    <button class="btn btn-danger btn-sm" 
                                        onclick="deleteEvent(${event.event_id})">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
            });
    }

    // Load categories into the event form and edit popup
    function loadCategoryOptions() {
        fetch('../actions/fetch_category_action.php')
            .then(res => res.json())
            .then(data => {
                const createSelect = document.getElementById('event_cat');
                const editSelect = document.getElementById('edit_event_cat');

                const buildOptions = (select) => {
                    if (!select) return;
                    select.innerHTML = '';
                    if (!data || data.length === 0) {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'No categories found';
                        select.appendChild(opt);
                        return;
                    }
                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = 'Select Category';
                    select.appendChild(placeholder);
                    data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.cat_id;
                        opt.textContent = c.cat_name;
                        select.appendChild(opt);
                    });
                };

                buildOptions(createSelect);
                buildOptions(editSelect);
            })
            .catch(() => {
                const createSelect = document.getElementById('event_cat');
                if (createSelect) createSelect.innerHTML = '<option value="">Error loading categories</option>';
            });
    }

    // Delete Event
    window.deleteEvent = function(event_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to delete this event. This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append("event_id", event_id);

                fetch("../actions/delete_event_action.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(resp => {
                    const icon = resp.status === 'success' ? 'success' : 'error';
                    Swal.fire({ icon: icon, title: resp.status === 'success' ? 'Deleted!' : 'Error', text: resp.message });
                    if (resp.status === 'success') loadEvent();
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network or server error.' }));
            }
        });
    };

    // Expose for inline use
    window.openForm = openForm;

    // Initial setup
    createEditPopup();
    closeForm();
    loadCategoryOptions();
    loadEvent();
});
