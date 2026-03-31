<?php
session_start();
session_unset();
session_destroy();
header('Location: /canteen_project/admin/login.php');
exit;
