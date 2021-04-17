<?
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;
use yii\widgets\ListView;
use yii\bootstrap\Modal;
use common\models\Orders;
use common\models\Mainmenu;

echo $this->render('_orders_js');
?>
<div class="findorder">
<?

$user_settings = $model->user->settings;

if ($user_settings->lat && $user_settings->lon) {
?>
	<div>
        <?$form = ActiveForm::begin(['id' => 'findorder-form']);?>

		<?=$form->field($model, 'find_union')->checkbox();?>
		<?=$form->field($model, 'in_method')->checkbox();?>

        <?ActiveForm::end();?>
    </div>
<?} else {?>
	<div class="alert alert-warning"><?=\Yii::t('app', 'requiregeolocation')?></div>	
<?}?>
</div>

<?if ($items && $items->getCount()) {?>
<div class="orders">
	<?=$this->render('../layouts/edit-toolbar', [
		'params'=>[
			'item-selector'=>'.order',
			'action-url'=>Url::toRoute(['cabinet/partner_orders']),
			'states'=>Utils::t('orderstatuslist'),
			'actions'=>[
				[
					'include'=>[Orders::STATE_NEW],
					'state'=>Orders::STATE_PROCESS,
					'action'=>'Accept',
					'question'=>'Accept of selected orders?',
					'icon'=>'glyphicon-ok'
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