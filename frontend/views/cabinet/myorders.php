<?
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;
use common\models\Orders;
?>
<div class="orders">
	<div class="orders-list">		
	<?
		echo ListView::widget([
		    'dataProvider' => $model,
		    'itemView' => '_item_order',
		    'summary' => ''
		]);
	?>
	</div>
</div>