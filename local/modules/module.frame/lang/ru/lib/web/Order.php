<?php
$MESS["ERR_UNKNOWN"] = "Неизвестная ошибка";
$MESS["ERR_EMPTY_REQUEST"] = "Пришел пустой запрос";
$MESS["ERR_NOT_FOUND_ITEM"] = "Передан #PARAM_NAME# = #PARAM_VALUE#, не существующий на сайте";
$MESS["ERR_EMPTY_FIELD"] = "В запросе не найден параметр #PARAM_NAME#";
$MESS["ERR_NOT_FOUND_USER_PROP"] = "Для корректного сохранения данных покупателя на сайте не найдено свойство «#PARAM_NAME#»";
$MESS["ERR_BAD_VALUE"] = "Недопустимое значение поля #PARAM_NAME# = #PARAM_VALUE#";
$MESS["ERR_PAYED_CANCEL_ORDER"] = "Заказ был отменён. Нельзя создать оплату для отменённого заказа.";

$MESS["ERR_NOT_FOUND_IBLOCK"] = 'На сайте не определены ID инфоблоков товаров и ТП, невозможно добавить/обновить товары в заказе';
$MESS["ERR_CREATE_PRODUCT"] = 'Передан не существующий на сайте товар #XML_ID#, попытка его создать в каталоге товаров приводит к ошибке «#ERROR#»';
$MESS["ERR_CREATE_SKU"] = 'Передан не существующий на сайте товар #XML_ID#, попытка создать ТП в каталоге товаров приводит к ошибке «#ERROR#»';
$MESS["ERR_UPDATE_QUANTITY"] = "Передано количество, превышающее остаток на сайте. Ошибка обновления остатка у товара с ID на сайте = #ID#. Текст ошибки: «#ERROR#»";
$MESS["ERR_ADD_PRICE"] = "Передан не существующий на сайте товар/ТП #XML_ID#. Товар был создан с ID=#ID#, но попытка присвоить ему цену приводит к ошибке «#ERROR#»";
$MESS["ERR_ADD_QUANTITY"] = "Передан не существующий на сайте товар/ТП #XML_ID#. Товар был создан с ID=#ID#, но попытка присвоить ему остатки приводит к ошибке «#ERROR#»";

$MESS["ERR_ADD_PRODUCT_BASKET"] = "Ошибка при добавлении товара #XML_ID# (ID=#ID#) в заказ. Текст ошибки: «#ERROR#»";
$MESS["ERR_UPDATE_PRODUCT_BASKET"] = "Ошибка в обновлении товара #XML_ID# (ID=#ID#) в заказе. Текст ошибки: «#ERROR#»";

$MESS["ERR_SAVE_ORDER"] = "Невозможно сохранить заказ. Текст ошибки: «#ERROR#»";
$MESS["ERR_CHANGE_PAID"] = "Невозможно изменить статус оплаты. Текст ошибки: «#ERROR#»";
$MESS["ERR_CHANGE_SHIPMENT"] = "Невозможно изменить статус отгрузки. Текст ошибки: «#ERROR#»";
$MESS["ERR_ORDER_SET_FIELD"] = "Не получилось изменись свойство заказа  #PARAM_NAME# = #PARAM_VALUE#. Текст ошибки: «#ERROR#»";

$MESS["ERR_UPDATE_ORDER_USER"] = "Не удалось изменить пользователя в заказе";
$MESS["ERR_CREATE_SECTION"] = "Не удалось создать раздел #NAME#";

$MESS["NOTIFY_UPDATED_PAYMENT_IN_QUEUE"] = "Сумма оплаты не была обновлена! Причина: в очереди на отправку в 1С находится оплата для этого заказа";
$MESS["NOTIFY_UPDATED_PAYMENT_WAITING"] = "Заказ уже был отправлен на доплату. Нельзя его менять, пока пользователь не оплатит недостающую сумму";


