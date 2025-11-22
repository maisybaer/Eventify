document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#brandTable tbody");

    // Add Brand
    $('#addBrandForm').submit(function(e) {
        e.preventDefault();

        let brand_name = $('#brand_name').val();
        let user_id = $('#user_id').val();

        if (brand_name == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill the name of your new brand!',
            });
            return;
        }

        let formData = new FormData();
        formData.append('brand_name', brand_name);
        formData.append('user_id', user_id);

        fetch("../actions/add_brand_action.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            Swal.fire({
                icon: 'success',
                title: 'Added!',
                text: resp.message,
            });
            loadBrand();
            $('#addBrandForm')[0].reset();
        });
    });

    // Update Brand (Pop up form)
    let currentEditBrandId = null;

    function createEditPopup() {
        if (document.getElementById("updateBrandForm")) return;
        let popup = document.createElement("div");
        popup.className = "form-popup";
        popup.id = "updateBrandForm";
        popup.style.display = "none";
        popup.innerHTML = `
            <div class="card" style="max-width:400px;margin:auto;">
                <div class="card-body">
                    <form id="editBrandForm">
                        <div class="mb-3">
                            <label for="editBrandName" class="form-label">Edit Brand Name</label>
                            <input type="text" class="form-control" id="editBrandName" name="editBrandName" required>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Update</button>
                        <button type="button" class="btn btn-secondary w-100 mt-2" id="closeEditPopup">Cancel</button>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(popup);

        document.getElementById("closeEditPopup").onclick = closeForm;

        document.getElementById("editBrandForm").onsubmit = function(e) {
            e.preventDefault();
            let brand_name = document.getElementById("editBrandName").value;
            let formData = new FormData();
            formData.append("brand_id", currentEditBrandId);
            formData.append("brand_name", brand_name);

            fetch("../actions/update_brand_action.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(resp => {
                alert(resp.message);
                closeForm();
                loadBrand();
            });
        };
    }

    function openForm(brand_id, brand_name) {
        currentEditBrandId = brand_id;
        let popup = document.getElementById("updateBrandForm");
        document.getElementById("editBrandName").value = brand_name;
        popup.style.display = "block";
    }

    function closeForm() {
        let popup = document.getElementById("updateBrandForm");
        popup.style.display = "none";
    }

    // Fetch brand
    function loadBrand() {
        fetch("../actions/fetch_brand_action.php")
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = "";
                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="3">No Brands available</td></tr>`;
                } else {
                    data.forEach(brand => {
                        let row = `
                            <tr>
                                <td>${brand.brand_id}</td>
                                <td>${brand.brand_name}</td>
                                <td>
                                    <button onclick="openForm(${brand.brand_id}, '${brand.brand_name.replace(/'/g, "\\'")}')" class="btn btn-custom btn-sm">Edit</button>
                                    <button onclick="deleteBrand(${brand.brand_id})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
            });
    }

    // Delete Brand
    window.deleteBrand = function (brand_id) {
        let formData = new FormData();
        formData.append("brand_id", brand_id);

        fetch("../actions/delete_brand_action.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            alert(resp.message);
            loadBrand();
        });
    };

    // Expose openForm globally for inline button
    window.openForm = openForm;

    // Initial setup 
    createEditPopup();
    closeForm();
    loadBrand();
});
