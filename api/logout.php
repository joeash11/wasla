<?php
// ============================================
// Logout Handler
// Destroys session and redirects to landing
// ============================================
session_start();
session_unset();
session_destroy();
header('Location: /wasla/landing.html');
exit;
?>
