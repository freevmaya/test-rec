<?php
 
namespace common\components;
 
use yii;
use yii\base\Component;
use common\helpers\Utils;
use common\models\Orders;
use yii\helpers\Html;
use yii\helpers\Url;

class Notify extends Component {


    public function initListeners() {
    	Yii::$app->on('Orders.afterSave', [$this, 'ordersAfterSave']);
    }

    protected static function renderTemplate($notify_type, $eventName, $params) {
    	return Yii::$app->view->renderFile('@common/messages/'.\Yii::$app->language.'/'.$notify_type.'/'.$eventName.'.php', $params);
    }

    protected function ordersAfterSave($event) {
    	$order = $event->sender;

    	if ($order->state == Orders::STATE_USER_REQUEST) {

    		$messageBody = Notify::renderTemplate('mail', 'Orders.toPartner', [
	    		'user'=>$order->user,
	    		'executer'=>$order->executer,
	    		'order'=>$order
	    	]);

	    	$message = Yii::$app->mailer->compose();
		    $message->setFrom(Yii::$app->params['adminEmail']);
			$message->setTo($order->executer->partner_settings->email)
			    ->setSubject(Utils::t('NewOrderNotification'))
			    ->setHtmlBody($messageBody)
			    ->send();

    	} else {
	    	$messageBody = Notify::renderTemplate('mail', $event->name, [
	    		'user'=>$order->user,
	    		'executer'=>$order->executer,
	    		'order'=>$order
	    	]);

	    	$message = Yii::$app->mailer->compose();
		    $message->setFrom(Yii::$app->params['adminEmail']);
			$message->setTo($order->user->email)
			    ->setSubject(Utils::t('OrderNotification'))
			    ->setHtmlBody($messageBody)
			    ->send();
		}
    }
}