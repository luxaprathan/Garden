<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.5); /* Dark background */
            padding-top: 100px;
            transition: opacity 0.3s ease; /* Smooth fade-in effect */
        }
        
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
            text-align: center;
            position: relative;
        }
        
        .close {
            color: #000;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
        }
        
        .close:hover,
        .close:focus {
            color: red;
            text-decoration: none;
        }
        
        .modal-content p {
            margin-bottom: 10px;
        }
        
        .modal-content button {
            padding: 12px 25px;
            font-size: 16px;
            margin: 10px 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .confirm-cancel {
            background-color: #e74c3c; /* Red color for cancel */
            color: white;
        }
        
        .confirm-cancel:hover {
            background-color: #c0392b; /* Darker red on hover */
        }
        
        .cancel {
            background-color: #2ecc71; /* Green color for cancel */
            color: white;
        }
        
        .cancel:hover {
            background-color: #27ae60; /* Darker green on hover */
        }
        
        /* Transition effects for the modal */
        .modal.show {
            display: block;
            opacity: 1;
        }

        /* Button to open the modal */
        #openModalBtn {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #openModalBtn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<!-- Modal HTML -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Are you sure you want to cancel this order?</p>
        <button id="confirmCancel" class="confirm-cancel">Yes, Cancel Order</button>
        <button id="closeModal" class="cancel">No, Keep Order</button>
    </div>
</div>

<script>
    // Get modal elements
    const modal = document.getElementById("cancelModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModalBtn = document.getElementById("closeModal");
    const confirmCancelBtn = document.getElementById("confirmCancel");
    const closeSpan = document.querySelector(".close");

    // Show the modal
    openModalBtn.addEventListener("click", function() {
        modal.classList.add("show");
    });

    // Hide the modal when clicking the close (×) button
    closeSpan.addEventListener("click", function() {
        modal.classList.remove("show");
    });

    // Hide the modal when clicking "No, Keep Order" button
    closeModalBtn.addEventListener("click", function() {
        modal.classList.remove("show");
    });

    // Handle the confirmation of canceling the order
    confirmCancelBtn.addEventListener("click", function() {
        alert("Order has been canceled!");
        modal.classList.remove("show");

        // Here, you can add AJAX to send the cancellation request to the backend
    });

    // Hide modal if user clicks outside the modal content
    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.classList.remove("show");
        }
    });
</script>

</body>
</html>
