function filterOrders() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let table = document.getElementById("ordersTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let orderId = tr[i].getElementsByTagName("td")[0];
        let productName = tr[i].getElementsByTagName("td")[1];
        
        if (orderId && productName) {
            let orderIdText = orderId.textContent || orderId.innerText;
            let productNameText = productName.textContent || productName.innerText;
            
            if (orderIdText.toLowerCase().includes(input) || productNameText.toLowerCase().includes(input)) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
   
function filterByDate(type) {
    let today = new Date();
    let yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);

    let rows = document.querySelectorAll("#ordersTable tbody tr");
    rows.forEach(row => {
        let orderDate = row.cells[4].innerText.trim(); // Read the formatted date
        let parsedDate = new Date(Date.parse(orderDate)); // Convert to date object

        if ((type === 'today' && parsedDate.toDateString() === today.toDateString()) ||(type === 'yesterday' && parsedDate.toDateString() === yesterday.toDateString())) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

function filterByStatus(status) {
    let rows = document.querySelectorAll("#ordersTable tbody tr");
    rows.forEach(row => {
        let orderStatus = row.cells[6].querySelector('select') ? row.cells[6].querySelector('select').value : row.cells[6].innerText.trim().toLowerCase();

        // If the status is 'all', show all rows
        if (status === 'all') {
            row.style.display = "";
        } else if (orderStatus === status) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
