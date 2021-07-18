<?php

//Php error messages (use if to toggle)

const DISPLAY_ERRORS = true;
const DONT_DISPLAY_ERRORS = false;

if (DONT_DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

//Default includes
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/User.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/user_controller.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/article_controller.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/search_controller.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/category_controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/controller/order_controller.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/article_review_controller.php";

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/includes/check_login.inc.php");




