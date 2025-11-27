$(document).ready(function() {
    // Toggle between view and edit mode
    $('#editProfileBtn').on('click', function() {
        $('#viewMode').hide();
        $('#editMode').show();
        $(this).hide();
    });

    $('#cancelEditBtn').on('click', function() {
        $('#editMode').hide();
        $('#viewMode').show();
        $('#editProfileBtn').show();
    });

    // Handle form submission
    $('#vendorProfileForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('name', $('#editName').val());
        formData.append('contact', $('#editContact').val());
        formData.append('country', $('#editCountry').val());
        formData.append('city', $('#editCity').val());
        formData.append('vendor_type', $('#editVendorType').val());
        formData.append('description', $('#editDescription').val());

        // Add image if selected
        const imageFile = $('#editImage')[0].files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        // Show loading indicator
        Swal.fire({
            title: 'Updating Profile...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '../actions/update_vendor_action.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function(response) {
            if (response && response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated!',
                    text: response.message || 'Your profile has been updated successfully.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload the page to show updated data
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: response.message || 'Failed to update profile. Please try again.'
                });
            }
        }).fail(function(xhr, status, error) {
            console.error('Update error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update profile. Please try again.'
            });
        });
    });
});
