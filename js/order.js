document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#orderTable tbody");


    // Fetch orders that include events created by the logged-in user
    function loadOrder() {
        fetch("../actions/fetch_order_action.php?action=by_creator")
            .then(res => res.json())
            .then(data => {
                console.log('fetch_orders_by_creator response:', data);

                // Accept either an array or a single object result
                if (!Array.isArray(data) && data && typeof data === 'object') {
                    data = [data];
                }

                tableBody.innerHTML = "";
                if (!Array.isArray(data) || data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center">No orders found.</td></tr>`;
                    return;
                }

                data.forEach(order => {
                    const items = order.item_count || '0';
                    const total = order.total_price !== null ? parseFloat(order.total_price).toFixed(2) : '0.00';
                    const invoice = order.invoice_no || order.invoice_id || 'N/A';
                    const status = order.order_status || 'Pending';
                    const row = `
                        <tr>
                            <td>${order.order_id}</td>
                            <td>${invoice}</td>
                            <td>${order.customer_id}</td>
                            <td>${items}</td>
                            <td>GHS ${total}</td>
                            <td>${order.order_date}</td>
                            <td><span class="badge bg-${status === 'Paid' ? 'success' : 'warning'}">${status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewOrder(${order.order_id})">View</button>
                            </td>
                        </tr>`;
                    tableBody.innerHTML += row;
                });
            })
            .catch(err => {
                console.error('Failed to load orders', err);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Error loading orders.</td></tr>`;
            });
    }

    // Delete order
    window.deleteOrder = function (order_id) {
        let formData = new FormData();
        formData.append("order_id", order_id);

        fetch("../actions/delete_order_action.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            alert(resp.message);
            loadOrder();
        });
    };

    // View order items for a given order (shows modal)
    window.viewOrder = function(order_id) {
        const container = document.getElementById('orderItemsContainer');
        const loading = document.getElementById('orderItemsLoading');
        if (container) container.innerHTML = '<div class="text-center py-4" id="orderItemsLoading">Loading items...</div>';

        fetch(`../actions/fetch_order_action.php?action=order_products&order_id=${order_id}`)
            .then(res => res.json())
            .then(data => {
                // data should be an array of items or an error object
                if (!Array.isArray(data) || data.length === 0) {
                    container.innerHTML = '<div class="text-center py-4">No items found for this order.</div>';
                } else {
                    let html = `
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ticket</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                    let total = 0;
                    data.forEach(item => {
                        const title = item.product_title || item.event_desc || 'Ticket';
                        const qty = Number(item.qty || item.quantity || 0);
                        const price = Number(item.product_price || item.event_price || 0).toFixed(2);
                        const subtotal = (qty * Number(price)).toFixed(2);
                        total += Number(subtotal);

                        html += `
                            <tr>
                                <td>${title}</td>
                                <td>${qty}</td>
                                <td>GHS ${price}</td>
                                <td>GHS ${subtotal}</td>
                            </tr>`;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <h5>Total: GHS ${total.toFixed(2)}</h5>
                        </div>`;

                    container.innerHTML = html;
                }

                // show modal
                const modalEl = document.getElementById('orderItemsModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            })
            .catch(err => {
                console.error('Failed to fetch order items', err);
                if (container) container.innerHTML = '<div class="text-center text-danger py-4">Error loading items.</div>';
                const modalEl = document.getElementById('orderItemsModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            });
    };

    // Initial setup: load orders
    loadOrder();
});
    