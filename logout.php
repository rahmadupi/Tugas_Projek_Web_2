<?php
session_start();
session_destroy();
$username='Guest';
header('Location: index.php');
?>