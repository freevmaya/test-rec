<?
	use common\helpers\Utils;
	use common\helpers\Html;
	use common\models\User;
	use common\models\User_settings;
	use yii\web\View;
	use kartik\rating\StarRating;

	$pset = $model->user->partner_settings;

	$methodLocal = [];
	$aliases = Utils::t('execMethods');
	foreach ($pset->execMethodsArray as $method) $methodLocal[] = $aliases[$method];
?>
<div class="partner-item" data-id="<?=$model->user_id?>">
	<div class="image" style="background-image: url(<?=$pset->imageUrl()?>)"></div>
	<div class="partner-detail">
		<h4 class="parther-title"><?=$pset->name?></h4>
		<div>
			<?=StarRating::widget(['name'=>'rate', 'value' => $model->user->rates, 
					    'pluginOptions' => [
					    	'displayOnly' => true
					    ]
			]);
			?>
			<div><?=Html::field($pset, 'email');?></div>
			<div><?=Html::field($pset, 'phone');?></div>
			<div class="execMethods" data-list="<?=implode(",", $pset->execMethodsArray);?>"><span class="varname"><?=$pset->attributeLabels()['execMethods'];?></span><?=implode(', ', $methodLocal)?>
			</div>				
		</div>
	</div>
</div>