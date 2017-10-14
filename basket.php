<?php
// подключение библиотек
require "inc/lib.inc.php";
//require "inc/config.inc.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Корзина пользователя</title>
</head>
<body>
<h1>Ваша корзина</h1>
<?php
if (!myBasket()) {
    echo '<p>' . 'Корзина  пуста! ' . '<br>' . '<a href="catalog.php">Вернитесь в каталог</a></p>';
    die;
} else {
    echo '<p><a href="catalog.php">Вернуться в каталог товаров</a></p>';
}
?>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th>N п/п</th>
        <th>Название</th>
        <th>Автор</th>
        <th>Год издания</th>
        <th>Цена, руб.</th>
        <th>Количество</th>
        <th>Удалить</th>
    </tr>
    <?php
    $i = 1; // Порядковый номер товара
    $sum = 0; // Сумма цены
    foreach (myBasket() as $item) { ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $item['title'] ?></td>

            <td><?php echo $item['author'] ?></td>
            <td><?php echo $item['pubyear'] ?></td>
            <td><?php echo $item['price'] ?></td>
            <td><?php echo $item['quantity'] ?></td>
            <td><a href='delete_from_basket.php?id=<?php echo $item['id']; ?>'>Удалить товар</a></td>
        </tr>
        <?php
        $i++;
        $sum += $item['price'] * $item['quantity'];
    }
    ?>
</table>

<p>Всего товаров в корзине на сумму: <b><?php echo $sum; ?> </b> руб. </p>

<div align="center">
    <input type="button" value="Оформить заказ!"
           onClick="location.href='orderform.php'"/>
</div>

</body>
</html>







