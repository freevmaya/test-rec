<?
use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;
use common\models\Recipes;

?>
<div class="partners">
	<?
		echo ListView::widget([
		    'dataProvider' => $model,
		    'itemView' => '_item_partner',
		    'summary' => ''
		]);
	?>
</div>