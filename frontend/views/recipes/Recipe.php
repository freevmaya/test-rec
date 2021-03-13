<?
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\StringHelper;
use yii\base\Model;
use common\models\Recipes;
use common\models\Units;
use common\helpers\Utils;

use kartik\rating\StarRating;

$backLink = $backLink = Url::toRoute(['/recipes/index', 'cat_id'=>\Yii::$app->request->get('cat_id')]);
$this->params['breadcrumbs'][] = ['label'=>Utils::mb_ucfirst(Yii::t('app', 'recipes')), 'url' => $backLink];
$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', 'recipe'));
$rate = $model->rates;

if (Yii::$app->user->isGuest) $addRecipeLink = Url::toRoute(['/site/login']);
else $addRecipeLink = Url::toRoute(['/recipes/edit', 'cat_id'=>\Yii::$app->request->get('cat_id')]);

$ajaxList = [
	'favorite'=>['glyphicon-heart in', 'glyphicon-heart-empty'],
	'basket'=>['glyphicon-shopping-cart in', 'glyphicon-shopping-cart']
];

$this->registerJs('
	var ajaxList = '.json_encode($ajaxList).';

	$(".ajax-request").click(function (e) {
		let a = $(e.currentTarget);
   		let span = a.find("span");
   		let key = a.data("type");
        $.ajax({
            url: a.attr("href"),
            success: function(data){
            	if (data) {
            		if (span.hasClass(ajaxList[key][0])) {
            			span.removeClass(ajaxList[key][0]);
            			span.addClass(ajaxList[key][1]);
            		} else {
            			span.removeClass(ajaxList[key][1]);
            			span.addClass(ajaxList[key][0]);
            		}
            	}
            }
        });
        e.stopPropagation();
        return false;
    });
');

?>
<div class="recipes">
	<div class="column-one">
		
		<a href="<?=$addRecipeLink?>"><?=Utils::mb_ucfirst(Yii::t('app', 'add_recipe'))?></a>

		<div class="card">
			<div class="recipe-item card-body full">
				<h3 class="card-title"><?=$model['name']?></h3>
				<div class="recipe-content">
					<div class="header">
						<?foreach ($ajaxList as $key=>$item) {
							$field = 'is'.$key;
							?>
						<a class="ajax-request" data-type="<?=$key?>" title="<?=Yii::t('app', 'title-'.$key)?>" href="<?=Url::toRoute(['/recipes/toggle'.$key, 'id'=>$model->id]);?>">
							<span class="glyphicon <?=$model->$field?$item[0]:$item[1]?>"></span>
						</a>
						<?}?>
					<?
					$form_id = 'recipe-rates-form-'.$model->id;
					$form = ActiveForm::begin(['id' => $form_id]); ?>
					<?=$form->field($model, 'rates')->widget(StarRating::classname(), [
						    'value'=> $rate,
						    'pluginOptions' => [
						    	'disabled'=>false, 
						    	'showClear'=>false, 
						    	'showClear' => false, 
						    	'showCaption' => false,					    
	        					'step' => 1
	        				],
	        				"pluginEvents" => [
	        					"change" => "function() {
	        						let form = $('#{$form_id}');
							        $.ajax({
							            url: form.attr('action'),
							            type: 'POST',
							            data: form.serialize(),
							            success: function(res){
							                console.log(res);
							            }
							        });
	        					}"
	        				]
						])->label(false);?>
					<?php ActiveForm::end(); ?>
					</div>
					<div class="image" style="background-image: url(<?=Recipes::UrlImage($model)?>)"></div>
					<div class="recipe-detail">
						<div class="description"><?=$model['description'];?></div>

						<div class="cook_detail">
							<div class="cook_time"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'cooking time'))?>:</span> <?=Utils::cook_time($model['cook_time'])?></div>
							<div class="cook_level"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'cooking level'))?>:</span> <?=$model->cookLevel?></div>
							<div class="portion"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'portion'))?>:</span> <?=$model['portion']?></div>
							<div class="author"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'author'))?>:</span> <?=$model->author->username?></div>
							<div>
								<div><span><?=Utils::mb_ucfirst(\Yii::t('app', 'ingredients'))?></span></div>
								<div class="ingredients">
									<ul>
								<?
									foreach ($model->getIngredientValues() as $item) {?>
										<li><?=$item['name']?>: <?=Units::unitValue($item['value'], $item['type'])?> <?=$item['short']?></li>
									<?}?>
									</ul>
									<div class="alert alert-warning units-help">
										<?=\Yii::t('app', 'units-help');?>
									</div>
								</div>
							</div>
						</div>
						<div class="stages">
		                	<h3><?=Yii::t('app', 'stages')?></h3>
		                	<div>
		                		<?foreach ($model->stages as $ix=>$stage) {?>
		                			<div class="stage">
		                				<h4><?=$stage->name?></h4>
		                				<?if ($stage->image) {?>
		                					<div class="image" style="background-image: url(<?=$stage->imageUrl()?>)"></div>
		                				<?}?>
		                				<p><?=$stage->text?></p>
		                			</div>
		                			<?if ($ix < count($model->stages) - 1) {?>
		                			<hr>
		                			<?}?>
		                		<?}?>
		                	</div>
	                	</div>
						<?if ($model->parser_id) {?>
						<div class="source">
							<a href="<?=$model->parser->url?>" target="_blank"><?=Yii::t('app', 'source')?></a>
						</div>
						<?}
							if (Recipes::editable($model)) {?>
								<div class="admin-block">
									<a type="button" class="btn btn-primary"href="<?=Url::toRoute(['/recipes/edit', 'id'=>$model['id']])?>"><?=Utils::mb_ucfirst(Yii::t('app', 'edit'))?></a>
									<a type="button" class="btn btn-warning"href="<?=Url::toRoute(['/recipes/delete', 'id'=>$model['id']])?>"><?=Utils::mb_ucfirst(Yii::t('app', 'remove'))?></a>
								</div>
							<?}
						?>
					</div>
				</div>
			</div>			
		</div>
	</div>
	<div class="column-two">
		<?Utils::outCatItem(null, $cats);?>
	</div>
</div>