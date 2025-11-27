document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#orderTable tbody");


    // Fetch orders that include events created by the logged-in user
    function loadOrder() {
        fetch("../actions/fetch_orders_by_creator_action.php")
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = "";
                if (!Array.isArray(data) || data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center">No orders found.</td></tr>`;
                    document.getElementById('totalOrders').textContent = '0';
                    return;
                }

                document.getElementById('totalOrders').textContent = String(data.length);

                data.forEach(order => {
                    const items = order.item_count || '';
                    const total = order.total_price !== null ? order.total_price : '';
                    const invoice = order.invoice_no || order.invoice_id || '';
                    const row = `
                        <tr>
                            <td>${order.order_id}</td>
                            <td>${invoice}</td>
                            <td>${order.customer_id}</td>
                            <td>${items}</td>
                            <td>${total}</td>
                            <td>${order.order_date}</td>
                            <td>
                                <a href="../view/fetch_order_action.php?order_id=${order.order_id}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>`;
                    tableBody.innerHTML += row;
                });
            })
            .catch(err => {
                console.error('Failed to load orders', err);
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error loading orders.</td></tr>`;
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

    // Expose openForm globally for inline button
    window.openForm = openForm;

    // Initial setup 
    closeForm();
    loadOrder();
});
    