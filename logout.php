<?php
// clear session and go home
session_start();
session_destroy();
header('Location: index.php');
exit;
