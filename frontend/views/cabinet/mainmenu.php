<?
use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;
use common\models\Recipes;

?>

<div class="mainmenu">
	<div class="recipes-list">
	<?
		echo ListView::widget([
		    'dataProvider' => Recipes::dataProvider(null, ['mainmenu.user_id'=>\Yii::$app->user->id], ['INNER JOIN', 'mainmenu', 'mainmenu.recipe_id=recipes.id']),
		    'itemView' => '../recipes/_item',
		    'summary' => ''
		]);
	?>
	</div>
</div>