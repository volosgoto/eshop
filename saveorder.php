<?php
require "inc/lib.inc.php";
//require "inc/config.inc.php";

$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$address = $_POST['address'];
$dt = time();
$oid = $basket['orderid'];

$order = "$name|$phone|$email|$address|$dt|$oid \n";

file_put_contents('admin/' . 'orders.log', $order, FILE_APPEND);
safeOrder($dt);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Сохранение данных заказа</title>
</head>
<body>
<p>Ваш заказ принят.</p>
<p><a href="catalog.php">Вернуться в каталог товаров</a></p>
</body>
</html>