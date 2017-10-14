<?php

session_start();
if(!isset($_SESSION['admin'])){
    header('Location: /admin/secure/login.php?ref='. $_SERVER['REQUEST_URI']); //поменять путь к админке
    exit;
}
