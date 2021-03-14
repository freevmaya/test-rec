<?

use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;

$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', 'recipes'));
if (Yii::$app->user->isGuest) $addRecipeLink = Url::toRoute(['/site/login']);
else $addRecipeLink = Url::toRoute(['/recipes/edit', 'cat_id'=>$cat_id]);
?>
<div class="recipes">
	<div class="column-one">
		<?if ($consist) {?>
			<h2><?=$consist->name?></h2>
		<?}?>
		<a href="<?=$addRecipeLink?>"><?=Utils::mb_ucfirst(Yii::t('app', 'add_recipe'))?></a>
		<div class="recipes-list">		
		<?
			echo ListView::widget([
			    'dataProvider' => $dataProvider,
			    'itemView' => '_item',
			]);
		?>
		</div>
	</div>
	<div class="column-two">
		<?Utils::outCatItem(null, $cats);?>
	</div>
</div>