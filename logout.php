<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] == true) {
    
    session_unset();
    session_destroy();
    header("Location: index.php");  
    exit();
}


if (isset($_SESSION['user_id'])) {
    
    session_unset();
    session_destroy();
    header("Location: index.php");  
    exit();
} else {
    
    header("Location: index.php"); 
    exit();
}
?>
