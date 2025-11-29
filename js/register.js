$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        let name = $('#name').val();
        let email = $('#email').val();
        let password = $('#password').val();
        let country = $('#country').val();
        let city = $('#city').val();
        let phone_number = $('#phone_number').val();
        let user_image = $('#user_image')[0].files[0];
        let role = $('input[name="role"]:checked').val();

        if (name == '' || email == '' || password == '' ||country == ''|| city == ''|| phone_number == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });

            return;
        } else if (password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
            });

            return;
        } else if(password.length > 10 || password.length < 7 || !phone_number.match(/[0-9]/)){
             Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid phone number',
            });

            return;
        }

        //prepare data to accept image
        let formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('country', country);
        formData.append('city', city);
        formData.append('phone_number', phone_number);
        formData.append('role', role);

        if (user_image) {
            formData.append('user_image', user_image);
        }

        $.ajax({
            url: '../actions/register_action.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData:false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText); // For debugging
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred! Please try again later. Troubleshoot: See register.js_ajax ',
                });
            }
        });
    });
});