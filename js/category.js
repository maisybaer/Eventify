document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#catTable tbody");
    const updateModal = new bootstrap.Modal(document.getElementById('updateCategoryModal'));

    // Add category
    $('#addCatForm').submit(function(e) {
        e.preventDefault();

        let cat_name = $('#cat_name').val().trim();
        let user_id = $('#user_id').val();

        if (cat_name === '') {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please enter a category name!',
            });
            return;
        }

        // Show loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Adding...');
        submitBtn.prop('disabled', true);

        let formData = new FormData();
        formData.append('cat_name', cat_name);
        formData.append('user_id', user_id);

        fetch("../actions/add_category_action.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            const icon = resp.status === 'success' ? 'success' : 'error';
            Swal.fire({
                icon: icon,
                title: resp.status === 'success' ? 'Success!' : 'Error',
                text: resp.message,
                showConfirmButton: false,
                timer: 2000
            });
            if (resp.status === 'success') {
                loadCategories();
                $('#addCatForm')[0].reset();
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire({ 
                icon: 'error', 
                title: 'Error', 
                text: 'Failed to add category. Please try again.' 
            });
        })
        .finally(() => {
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        });
    });

    // Update category modal
    $('#updateCategoryForm').submit(function(e) {
        e.preventDefault();

        let cat_name = $('#update_cat_name').val().trim();
        let cat_id = $('#update_cat_id').val();

        if (cat_name === '') {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please enter a category name!',
            });
            return;
        }

        // Show loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
        submitBtn.prop('disabled', true);

        let formData = new FormData();
        formData.append("cat_id", cat_id);
        formData.append("cat_name", cat_name);

        fetch("../actions/update_category_action.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            const icon = resp.status === 'success' ? 'success' : 'error';
            Swal.fire({
                icon: icon,
                title: resp.status === 'success' ? 'Updated!' : 'Error',
                text: resp.message,
                showConfirmButton: false,
                timer: 2000
            });
            if (resp.status === 'success') {
                updateModal.hide();
                loadCategories();
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire({ 
                icon: 'error', 
                title: 'Error', 
                text: 'Failed to update category. Please try again.' 
            });
        })
        .finally(() => {
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        });
    });

    // Open edit modal
    window.openEditModal = function(cat_id, cat_name) {
        $('#update_cat_id').val(cat_id);
        $('#update_cat_name').val(cat_name);
        updateModal.show();
    };

    // Fetch and display categories
    function loadCategories() {
        fetch("../actions/fetch_category_action.php")
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = "";
                if (!Array.isArray(data) || data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No categories found. Create your first category!
                            </td>
                        </tr>`;
                } else {
                    data.forEach((cat, index) => {
                        let row = `
                            <tr class="animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
                                <td><span class="badge bg-secondary">#${cat.cat_id}</span></td>
                                <td>
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    <strong>${escapeHtml(cat.cat_name)}</strong>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Category actions">
                                        <button onclick="openEditModal(${cat.cat_id}, '${escapeHtml(cat.cat_name).replace(/'/g, "\\'")}'" 
                                                class="btn btn-outline-primary btn-sm" 
                                                title="Edit Category">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteCategory(${cat.cat_id}, '${escapeHtml(cat.cat_name)}')" 
                                                class="btn btn-outline-danger btn-sm"
                                                title="Delete Category">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
            })
            .catch(err => {
                console.error('Error loading categories:', err);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                            Failed to load categories. Please refresh the page.
                        </td>
                    </tr>`;
            });
    }

    // Delete category
    window.deleteCategory = function(cat_id, cat_name) {
        Swal.fire({
            title: 'Delete Category?',
            html: `Are you sure you want to delete "<strong>${escapeHtml(cat_name)}</strong>"?<br><br>
                   <small class="text-muted">This action cannot be undone and may affect events using this category.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append("cat_id", cat_id);

                fetch("../actions/delete_category_action.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(resp => {
                    const icon = resp.status === 'success' ? 'success' : 'error';
                    Swal.fire({
                        icon: icon,
                        title: resp.status === 'success' ? 'Deleted!' : 'Error',
                        text: resp.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    if (resp.status === 'success') {
                        loadCategories();
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Error', 
                        text: 'Failed to delete category. Please try again.' 
                    });
                });
            }
        });
    };

    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Initialize
    loadCategories();
});
