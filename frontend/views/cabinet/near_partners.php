<?
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\web\View;

use common\helpers\Utils;
use common\models\Recipes;
use common\models\Orders;
use common\models\User;
use common\models\User_settings;
use common\models\Distances;


$sets = \Yii::$app->user->identity->settings;

$query = User_settings::find()->innerJoin('distances', 'distances.partner_id=user_settings.user_id')->
					innerJoin('user', 'user.id=user_settings.user_id')->
					where("user.role='partner' AND distances.user_id = {$sets->user_id} AND distances.distance <= {$sets->finddistance}");
?>
<div class="near-partners">
	<label class="control-label"><?=Utils::t('near_partners')?></label>
	<?
	echo ListView::widget([
	    'dataProvider' => new ActiveDataProvider([
                        'query' => $query,
                        'pagination' => [
                            'pageSize' => \Yii::$app->params['countPerPage'],
                        ]
                    ]),
	    'itemView' => '_item_partner',
	    'summary' => ''
	]);
	?>
	<input type="hidden" name="Orders[exec_id]" value="0" id="selected_partner_id">
</div>

<?
$this->registerJs('
	var select = 0;

	$(".partner-item .parther-title").click((e)=>{
		let item = $(e.currentTarget).closest(".partner-item");
		let item_id = item.data("id");
		if (item_id != select) {
			if (select != 0) $(".partner-item[data-id=\"" + select + "\"]").removeClass("pshow");
			select = item_id;
			$("#selected_partner_id").val(item_id);

			$(window).trigger("SELECT_PARTNER", item.find(".execMethods").data("list"));
		}
		item.toggleClass("pshow");
	});
', View::POS_READY, 'item_partner');
?>