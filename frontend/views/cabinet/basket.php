<?
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use common\helpers\Utils;
use common\models\Recipes;
use common\models\Orders;
use common\models\User;
use common\models\Distances;

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
	<?$form = ActiveForm::begin(['id' => 'sendbasket-form', 'action' => Url::toRoute('cabinet/sendbasket')]);?>
	<?=$form->field($model, 'execMethod')->dropDownList(Utils::t('execMethods'), [
          	'multiple'=>'multiple',
          	'class'=>'form-control'
         ]);

	echo $this->render('near_partners');

	?>
	<div class="alert alert-warning"><?=\Yii::t('app', 'basket-bottom')?></div>

	<div class="form-group">
	    <?= Html::submitButton(Yii::t('app', 'send-basket'), ['class' => 'btn btn-primary', 'name' => 'basket-button']) ?>
	</div>

    <?ActiveForm::end();?>
</div>
<?}
$this->registerJs('
	$(window).on("SELECT_PARTNER", (e, execMethos)=>{
		let list = execMethos.split(",");
		console.log(list);
		$("#orders-execmethod").children("option").each((i, opt)=>{
			opt = $(opt);
			if (list.indexOf(opt.val()) > -1) {
				opt.removeClass("unpossible");
				opt.addClass("possible");
			}
			else {
				opt.addClass("unpossible");
				opt.removeClass("possible");
			}
		});
	});
', View::POS_READY, 'basket');
?>