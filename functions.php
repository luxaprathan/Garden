<?php 
    function isLoggedIn() {
        return isset($_SESSION['user_id']) ;
    }
    // Check if the user 
    function isUser() {
        return isset($_SESSION['user_id']) && $_SESSION["user_id"] !== 1;
    }
    // Check if the user is an Admin
    function isAdmin() {
        return isset($_SESSION['user_id']) && $_SESSION["user_id"] === 1;
    }
?>