<?
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;
use common\models\Recipes;

$provider = Recipes::dataProvider(null, ['basket.user_id'=>\Yii::$app->user->id], ['INNER JOIN', 'basket', 'basket.recipe_id=recipes.id'], 'basket.count AS `basketCount`');
?>
<div class="recipes">
	<div class="recipes-list">		
	<?
		echo ListView::widget([
		    'dataProvider' => $provider,
		    'itemView' => '../recipes/_item_basket',
		    'summary' => ''
		]);
	?>
	</div>
</div>
<?if ($provider->getTotalCount()) {?>
<div class="basket-footer">
	<div class="alert alert-warning"><?=\Yii::t('app', 'basket-bottom')?></div>
	<?$form = ActiveForm::begin(['id' => 'sendbasket-form', 'action' => Url::toRoute('cabinet/sendbasket')]);?>
	<div class="form-group">
	    <?= Html::submitButton(Yii::t('app', 'send-basket'), ['class' => 'btn btn-primary', 'name' => 'basket-button']) ?>
	</div>

    <?ActiveForm::end();?>
</div>
<?}?>