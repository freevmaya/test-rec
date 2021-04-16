<?
use yii\helpers\Url;

?>
<h1>Привет <?=$executer->username;?>!</h1>
<p>У вас новый заказ.</p>
Перейдите по <a href="<?=Url::toRoute(['site/order', 'id' => $order->id, 'referer' => 'letter'], true);?>">ссылке</a> для просмотра заказа.