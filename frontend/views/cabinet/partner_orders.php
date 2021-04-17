<?
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use common\helpers\Utils;
use yii\widgets\ListView;
use yii\bootstrap\Modal;
use common\models\Mainmenu;
use common\models\Orders;

if ($items) {
	echo $this->render('_orders_js');	
	?>
<div class="orders">
	<?=$this->render('../layouts/edit-toolbar', [
		'params'=>[
			'item-selector'=>'.order', // Стиль-идентификатор элемента 
			'action-url'=>Url::toRoute(['cabinet/partner_orders']), // URL по которому будет формироваться Ajax запрос на изменения статуса
			'states'=>Utils::t('orderstatuslist'),	// Массив статусов где индекс номер - статуса, значение - название
			'actions'=>[	// Список возможных активностей-кнопок
				[
					'include'=>[Orders::STATE_USER_REQUEST], 	// Какие стутусы будут реагировать на эту кнопку
					'state'=>Orders::STATE_PROCESS,				// Какой статус требуется установить
					'action'=>'Accept',							// Активность (задать в стилях)
					'question'=>'Accept of selected orders?',	// Всплывающий вопрос при попытке
					'icon'=>'glyphicon-ok'						// Иконка
				],[
					'include'=>[Orders::STATE_USER_REQUEST],
					'state'=>Orders::STATE_REJECTED,
					'action'=>'Deny',
					'question'=>'Deny selected orders?',
					'icon'=>'glyphicon-ban-circle'
				],[
					'include'=>[Orders::STATE_PROCESS],
					'state'=>Orders::STATE_NEW,
					'action'=>'Cancel',					
					'question'=>'Cancel selected orders?',
					'icon'=>'glyphicon-remove-circle'
				],[
					'include'=>[Orders::STATE_REJECTED, Orders::STATE_ACCEPTED, Orders::STATE_FINISH],
					'state'=>Orders::STATE_REMOVED,
					'action'=>'Remove',					
					'question'=>'Remove selected orders?',
					'icon'=>'glyphicon-remove-sign'
				],[
					'include'=>[Orders::STATE_PROCESS],
					'state'=>Orders::STATE_PARTNER_FINISH,
					'action'=>'Finish',					
					'question'=>'Complete selected orders?',
					'icon'=>'glyphicon-off'
				]
			]
		]
	]);?>
	<div class="orders-list">		
	<?
		$mymenu = \Yii::$app->db->createCommand(
            'SELECT recipe_id, price FROM '.Mainmenu::tableName().' WHERE user_id='.$model->user->id
        )->queryAll();

		echo ListView::widget([
		    'dataProvider' => $items,
		    'itemView' => '_partner_item_order',
		    'viewParams' => ['mm_recipes'=>ArrayHelper::getColumn($mymenu, 'recipe_id'), 'mm_prices'=>ArrayHelper::getColumn($mymenu, 'price')],
		    'summary' => ''
		]);
	?>
	</div>
</div>
<?}?>