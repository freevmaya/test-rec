<?
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;
use common\models\Orders;

?>
<div class="orders">
	<div class="orders-list">		
	<?
		if ($model->getCount() > 0) {

			echo $this->render('_orders_js');

			echo $this->render('../layouts/edit-toolbar', [
				'params'=>[
					'item-selector'=>'.order', // Стиль-идентификатор элемента 
					'action-url'=>Url::toRoute(['cabinet/myorders']), // URL по которому будет формироваться Ajax запрос на изменения статуса
					'states'=>Utils::t('orderstatuslist'),	// Массив статусов где индекс номер - статуса, значение - название
					'actions'=>[	// Список возможных активностей-кнопок
						[
							'include'=>[Orders::STATE_PARTNER_FINISH], 	// Какие стутусы будут реагировать на эту кнопку
							'state'=>Orders::STATE_FINISH,				// Какой статус требуется установить
							'action'=>'Finish',							// Активность (задать в стилях)
							'question'=>'Complete selected orders?',	// Всплывающий вопрос при попытке
							'icon'=>'glyphicon-off'						// Иконка
						],[
							'include'=>[Orders::STATE_USER_REQUEST],
							'state'=>Orders::STATE_CANCEL,
							'action'=>'Cancel',
							'question'=>'Cancel selected orders?',
							'icon'=>'glyphicon-ban-circle'
						]
					]
				]
			]);
			echo ListView::widget([
			    'dataProvider' => $model,
			    'itemView' => '_item_order',
			    'summary' => ''
			]);
		}
	?>
	</div>
</div>