<?php
// logout.php
session_start();

// Destrui sessão hotu
$_SESSION = array();

// Se hala'o session cookie, delete
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrui sessão
session_destroy();

// Direksiona ba login
header('Location: login.php');
exit();
?>