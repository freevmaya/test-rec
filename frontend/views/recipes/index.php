<?

use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;
use common\Models\RecipesCats;
use common\widgets\ArticleBlock;

$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', 'recipes'));
if ($cat_id = \Yii::$app->request->get('cat_id')) {
	$cat = RecipesCats::find()->where(['id'=>$cat_id])->one();
	$this->params['breadcrumbs'][] = $cat->name;
}
if (Yii::$app->user->isGuest) $addRecipeLink = Url::toRoute(['/site/login']);
else $addRecipeLink = Url::toRoute(['/recipes/edit', 'cat_id'=>$cat_id]);

?>

<?=ArticleBlock::widget(['block_id'=>2]);?>
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
<?=ArticleBlock::widget(['block_id'=>3]);?>