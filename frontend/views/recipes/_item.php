<?
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use common\helpers\Utils;
use common\models\Recipes;
use kartik\rating\StarRating;

$linkArray = ['/recipes/item', 'id'=>$model['id'], 'cat_id'=>\Yii::$app->request->get('cat_id')];
$link = Url::toRoute($linkArray);

?>
<div class="card">
	<div class="recipe-item card-body">
		<h3 class="card-title"><a href="<?=$link?>"><?=$model['name']?></a></h3>
		<div class="recipe-content">
			<a href="<?=$link?>"><div class="image" style="background-image: url(<?=Recipes::UrlImage($model)?>)"></div></a>
			<div class="recipe-detail">
				<div class="description"><?=StringHelper::truncateWords($model['description'], 50, Html::a('... (читать дальше)', $linkArray));?></div>

				<div class="cook_detail">
					<div class="cook_time"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'cooking time'))?>:</span> <?=Utils::cook_time($model['cook_time'])?></div>
					<div class="cook_level"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'cooking level'))?>:</span> <?=Recipes::$levels[$model['cook_level']]?></div>
					<div class="portion"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'portion'))?>:</span> <?=$model['portion']?></div>
					<div class="actions">
					<?=StarRating::widget(['name'=>'rate', 'value' => $model['rates'], 
					    'pluginOptions' => [
					    	'disabled'	=>false, 
					    	'showClear'	=>false, 
					    	'showClear' => false, 
					    	'showCaption' => false,					    
        					'step' => 1
					    ]
					]);
					?>
					<?=$this->renderFile(dirname(__FILE__).'/actionBlock.php', ['recipe'=>$model]);?>
					</div>
				</div>
				<?
					if (Recipes::editable($model)) {?>
						<div class="admin-block">
							<a type="button" class="btn btn-primary"href="<?=Url::toRoute(['/recipes/edit', 'id'=>$model['id'], 'cat_id'=>\Yii::$app->request->get('cat_id')])?>"><?=Utils::mb_ucfirst(Yii::t('app', 'edit'))?></a>
							<a type="button" class="btn btn-warning"href="<?=Url::toRoute(['/recipes/delete', 'id'=>$model['id']])?>"><?=Utils::mb_ucfirst(Yii::t('app', 'remove'))?></a>
						</div>
					<?}
				?>
			</div>
		</div>
	</div>
</div>