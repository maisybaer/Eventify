document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#orderTable tbody");


    // Fetch order
    function loadOrder() {
        fetch("../actions/fetch_order_action.php")
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = "";
                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="3">No orders available</td></tr>`;
                } else {
                    data.forEach(order => {
                        let row = `
                            <tr>
                                <td>${order.order_id}</td>
                                <td>${order.invoice_id}</td>
                                <td>${order.customer_id}</td>
                                <td>${order.event_id}</td>
                                <td>${order.quantity}</td>
                                <td>${order.total_price}</td>
                                <td>${order.order_date}</td>
                                <td>
                                    <button onclick="openForm(${order.order_id}, '${order.order_name.replace(/'/g, "\\'")}')" class="btn btn-custom btn-sm">Edit</button>
                                    <button onclick="deleteorder(${order.order_id})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
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
    