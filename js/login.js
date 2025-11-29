$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();

        let email = $('#email').val();
        let password = $('#password').val();

        //checks if fields are empty
        if (email == '' || password == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });

            return;
        } 

        $.ajax({
            url: '../actions/login_action.php',
            type: 'POST',
            dataType:"json",
            data: {
                email: email,
                password: password,
            },

            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../index.php';
                        }
                    });
                
                }else if(response.status === 'redirect') {
                    window.location.href = response.redirect_url;
                
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Login AJAX Error:', xhr.responseText);
                let errorMsg = 'An error occurred! Please try again later.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch(e) {
                    if (xhr.responseText) {
                        errorMsg = xhr.responseText.substring(0, 200);
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMsg,
                });
            }
        });
    });
});