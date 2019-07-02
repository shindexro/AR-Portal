<?php
setcookie("userid", "", time() - (86400 * 30), "/");
setcookie("sesID", "", time() - (86400 * 30), "/");
header('Location: index.php');
?>
