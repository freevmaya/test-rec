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
		    'dataProvider' => Recipes::dataProvider(null, ['recipes.author_id'=>\Yii::$app->user->id]),
		    'itemView' => '../../recipes/_item',
		]);
	?>
	</div>
</div>