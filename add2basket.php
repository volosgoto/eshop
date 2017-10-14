<?php

// Добавление товара в корзину
require "inc/lib.inc.php";
//require "inc/config.inc.php";

$id = (int)($_GET['id']);
if ($id) {
    add2Basket($id);
}
header("Location: catalog.php");
die;
