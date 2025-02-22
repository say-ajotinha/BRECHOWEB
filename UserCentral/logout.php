<?php
session_start();
session_destroy();
header("Location: ../UserCentral/login.php");
exit();
?>
