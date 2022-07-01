<?php
session_start();

// Suppression des variables de session et de la session
$_SESSION = array();
session_destroy();

if (isset($_GET['deleted'])) {
  header( "refresh:0;url=login.php?deleted=true" );
} else {
  header( "refresh:0;url=login.php" );
}

?>
