<?php
require_once 'config.inc.php';

// Добавление товара в каталог
function addItemToCatalog($title, $author, $pubyear, $price) {
    global $link;
    $sql = "INSERT INTO catalog (title, author, pubyear, price)
            VALUES (?, ?, ?, ?)";
    if (!$stmt = mysqli_prepare($link, $sql)) {
        return false;
    } else {
        mysqli_stmt_bind_param($stmt, "ssii", $title, $author, $pubyear, $price);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return true;
    }
}

// Выбрка всех товаров
function selectAllItems(){
    global $link;
    $sql = "SELECT id, title, author, pubyear, price FROM catalog WHERE 1";
    $result = mysqli_query($link, $sql);
        if( !$result ){
            return false;
        } else {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        return $items;
        }
}

// Сохранение корзины
function saveBasket(){
    global $basket;
    $basket = base64_encode(serialize($basket));
    setcookie('basket', $basket, 0x7FFFFFFF);
}

// Создаем корзину
function basketInit(){
    global $basket;
    global $count;

    // Смотрим есть ли есть корзина. Читаем куку basket
    if (!isset($_COOKIE['basket'])) {
        $basket = ['orderid' => uniqid()];
        saveBasket();
    } else {
        $basket = unserialize(base64_decode($_COOKIE['basket']));
        $count = count($basket) - 1; // Превый элемент всегда orderid (к товару не имеет отношения)
    }
}

//Добавление в корзину
function add2Basket($id){
    global $basket;
    $basket[$id] = 1;
    saveBasket();
}

//Выборка и показ товаров из корзины пользователя
function myBasket() {
    global $link;
    global $basket;
    $goods = array_keys($basket);
    array_shift($goods); // 0 orderid, исключаем, т.к. нам нужны тольк товары
        if (!$goods) {  // Проверяем что пользователь просто зашел в корзину не добавив ни одной книги
            return false;
        }
    $ids = implode(",", $goods);
    $sql = "SELECT id, author, title, pubyear, price FROM catalog WHERE id IN ($ids)";
        if ( !$result = mysqli_query($link, $sql) ) {
            return false;
        }
    $items = result2Array($result);
    mysqli_free_result($result);
    return $items;
}

function result2Array($data){
    global $basket;
    $arr = [];
    while($row = mysqli_fetch_assoc($data)){
        $row['quantity'] = $basket[$row['id']];
        $arr[] = $row;
    }
    return $arr;
}

// Удаление товара из корзины
function deleteItemFromBasket($id) {
    global $basket;
    unset($basket[$id]);
    saveBasket();
}

function safeOrder($datetime) {
    global $link;
    global $basket;
    $goods = myBasket();
    $stmt = mysqli_stmt_init($link);
    $sql = 'INSERT INTO orders (
                                title,
                                author,
                                pubyear,
                                price,
                                quantity,
                                orderid,
                                datetime
                                )
              VALUES (?, ?, ?, ?, ?, ?, ?)';
    if (!mysqli_stmt_prepare($stmt, $sql))
        return false;
    foreach($goods as $item){
        mysqli_stmt_bind_param($stmt, "ssiiisi",
            $item['title'], $item['author'],
            $item['pubyear'], $item['price'],
            $item['quantity'],
            $basket['orderid'],
            $datetime);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
    setcookie("basket", "", 1);
    return true;
}

function getOrders(){
    global $link;
    if(!is_file(ORDERS_LOG))
        return false;
    /* Получаем в виде массива персональные данные пользователей из файла */
    $orders = file(ORDERS_LOG);
    /* Массив, который будет возвращен функцией */
    $allorders = [];
    foreach ($orders as $order) {
        list($name, $phone, $email, $address, $date, $orderid) = explode("|", trim($order));

        /* Промежуточный массив для хранения информации о конкретном заказе */
        $orderinfo = [];
        /* Сохранение информацию о конкретном пользователе */
        $orderinfo["name"] = $name;
        $orderinfo["phone"] = $phone;
        $orderinfo["email"] = $email;
        $orderinfo["address"] = $address;
        $orderinfo["date"] = $date;
        $orderinfo["orderid"] = $orderid;

        /* SQL-запрос на выборку из таблицы orders всех товаров для конкретного
        покупателя */
        $sql = "SELECT title, author, pubyear, price, quantity
                FROM orders
                WHERE orderid = '$orderid' AND datetime = $date";
        /* Получение результата выборки */
        if(!$result = mysqli_query($link, $sql))
            return false;
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        /* Сохранение результата в промежуточном массиве */
        $orderinfo["goods"] = $items;
        /* Добавление промежуточного массива в возвращаемый массив */
        $allorders[] = $orderinfo;

    }
    //print_r($allorders); die;
    return $allorders;
}