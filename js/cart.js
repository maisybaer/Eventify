$(document).ready(function() {

    // Update quantity when input changes
    $('.qty-input').on('change', function() {
        const productId = $(this).data('product-id');
        const qty = parseInt($(this).val());

        if (qty < 1) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Quantity',
                text: 'Quantity must be at least 1',
            });
            $(this).val(1);
            return;
        }

        updateQuantity(productId, qty);
    });

    // Remove item from cart
    $('.remove-btn').on('click', function() {
        const productId = $(this).data('product-id');
        removeFromCart(productId);
    });

    // Empty cart
    $('#emptyCartBtn').on('click', function() {
        Swal.fire({
            title: 'Empty Cart?',
            text: 'Are you sure you want to remove all items from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, empty it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                emptyCart();
            }
        });
    });

    // Proceed to checkout
    $('#checkoutBtn').on('click', function() {
        window.location.href = 'checkout.php';
    });

    // Function to update quantity
    function updateQuantity(productId, qty) {
        $.ajax({
            url: '../actions/update_quantity_action.php',
            method: 'POST',
            data: { product_id: productId, qty: qty },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update subtotal
                    updateCartDisplay();
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update quantity. Please try again.'
                });
            }
        });
    }

    // Function to remove item from cart
    function removeFromCart(productId) {
        Swal.fire({
            title: 'Remove Item?',
            text: 'Are you sure you want to remove this item from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/remove_from_cart_action.php',
                    method: 'POST',
                    data: { product_id: productId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Remove the row from table
                            $(`tr[data-product-id="${productId}"]`).fadeOut(300, function() {
                                $(this).remove();
                                updateCartDisplay();
                                checkEmptyCart();
                            });
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to remove item. Please try again.'
                        });
                    }
                });
            }
        });
    }

    // Function to empty cart
    function emptyCart() {
        $.ajax({
            url: '../actions/empty_cart_action.php',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cart Emptied',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to empty cart. Please try again.'
                });
            }
        });
    }

    // Function to update cart display (recalculate totals)
    function updateCartDisplay() {
        let total = 0;
        $('#cartItems tr').each(function() {
            const qty = parseInt($(this).find('.qty-input').val());
            const priceText = $(this).find('td:eq(2)').text().replace('$', '');
            const price = parseFloat(priceText);
            const subtotal = qty * price;
            
            $(this).find('.subtotal').text('$' + subtotal.toFixed(2));
            total += subtotal;
        });
        
        $('#cartTotal').text('$' + total.toFixed(2));
    }

    // Function to check if cart is empty
    function checkEmptyCart() {
        if ($('#cartItems tr').length === 0) {
            $('.cart-card').html('<p class="empty-msg">Your cart is empty. <a href="all_product.php">Continue shopping</a>.</p>');
        }
    }
});