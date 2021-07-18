<?php
//If cookie exists and user is not logged in
if (isset($_COOKIE["secure_code"]) && !isset($_SESSION["login"])) {
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/fingerprint.inc.php");
}

