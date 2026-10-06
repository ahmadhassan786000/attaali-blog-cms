<?php
require __DIR__ . '/_base.php';
start_session();
unset($_SESSION['admin_id']);
session_destroy();
header('Location: /attaali/admin');

