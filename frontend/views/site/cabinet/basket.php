<?
use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;
use common\models\Recipes;

?>
<div class="recipes">
	<div class="recipes-list">		
	<?
		echo ListView::widget([
		    'dataProvider' => Recipes::dataProvider(null, ['basket.user_id'=>\Yii::$app->user->id], ['INNER JOIN', 'basket', 'basket.recipe_id=recipes.id']),
		    'itemView' => '../../recipes/_item',
		]);
	?>
	</div>
</div>