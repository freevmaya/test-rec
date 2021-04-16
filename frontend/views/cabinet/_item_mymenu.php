<?
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\StringHelper;
use common\helpers\Utils;
use common\models\Recipes;
use common\models\Mainmenu;
use kartik\rating\StarRating;

$linkArray = ['/recipes/item', 'id'=>$model->recipe_id, 'cat_id'=>\Yii::$app->request->get('cat_id')];
$link = Url::toRoute($linkArray);
$settings = Yii::$app->user->identity->settings;

?>
<div class="card">
	<div class="recipe-item card-body" data-id="<?=$model->recipe_id?>" data-state="<?=$model->state?>">
		<div class="header">
			<input type="checkbox">
			<h3 class="card-title"><a href="<?=$link?>"><?=$model->recipe->name?></a></h3>
		</div>
		<div class="recipe-content">
			<a href="<?=$link?>"><div class="image" style="background-image: url(<?=Recipes::UrlImage($model->recipe)?>)"></div></a>
			<div class="recipe-detail">
				<div class="description"><?=StringHelper::truncateWords($model->recipe->description, 30, Html::a('... (читать дальше)', $linkArray));?></div>

				<div class="cook_detail">
					<div class="cook_time"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'cooking time'))?>:</span> <?=Utils::cook_time($model->recipe->cook_time)?></div>
					<div class="cook_level"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'cooking level'))?>:</span> <?=Recipes::$levels[$model->recipe->cook_level]?></div>
					<div class="portion"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'portion'))?>:</span> <?=$model->recipe->portion?></div>
					<div class="price"><span><?=Utils::mb_ucfirst(Utils::t('price'))?>:</span>
						<input type="number" value="<?=$model->price?>"> <?=$settings->language->currency?> 
					</div>
				</div>
				<div class="actions">
					<button class="btn btn-primary"><?=Utils::t("changePrice")?></button>
				</div>
			</div>
		</div>
	</div>
</div>