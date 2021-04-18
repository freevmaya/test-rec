<?
use yii\helpers\Url;

?>
<h1>Привет <?=$user->username;?>!</h1>
<?if ($executer) {?>
<p>У вас есть изменения по заказу. Выбран исполнитель: <?=$executer->partner_settings->name?></p>
Перейдите по <a href="<?=Url::toRoute(['site/order', 'id' => $order->id, 'referer' => 'letter'], true);?>">ссылке</a> для просмотра заказа.
<?} else {?>
<p>У вас есть изменения по заказу</p>
Перейдите по <a href="<?=Url::toRoute(['site/order', 'id' => $order->id, 'referer' => 'letter'], true);?>">ссылке</a> для просмотра заказа.
<?}?>