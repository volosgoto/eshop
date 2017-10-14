<?php

//DB connect
const DB_HOST = 'localhost';
const DB_LOGIN = 'root';
const DB_PASSWORD = '';
const DB_NAME = 'eshopphpspecialist';
const ORDERS_LOG = 'orders.log';
$link = mysqli_connect(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME);
mysqli_set_charset($link, "utf8");

//Инициализация корзины
$basket = [];
$count = 0;
basketInit();
