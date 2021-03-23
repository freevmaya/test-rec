<?
use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;
use common\models\Recipes;

$addRecipeLink = Url::toRoute(['/recipes/edit']);
?>

<a href="<?=$addRecipeLink?>" class="btn btn-primary"><?=Utils::mb_ucfirst(Yii::t('app', 'add_recipe'))?></a>
<div class="recipes">
	<div class="recipes-list">
	<?
		echo ListView::widget([
		    'dataProvider' => Recipes::dataProvider(null, ['recipes.author_id'=>\Yii::$app->user->id]),
		    'itemView' => '../recipes/_item',
		    'summary' => '',
		]);
	?>
	</div>
</div>