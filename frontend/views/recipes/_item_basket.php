<?
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\View;
use common\helpers\Utils;
use common\models\Recipes;
use kartik\rating\StarRating;

$linkArray = ['/recipes/item', 'id'=>$model['id'], 'cat_id'=>\Yii::$app->request->get('cat_id')];
$link = Url::toRoute($linkArray);
$field_name = "basket[{$model['id']}]";

$this->registerJs('
	$("input[name=\"'.$field_name.'\"]").change(function (e) {
		let input = $(e.currentTarget);
		let value = input.val();
		if (value < 1) input.val(1);
		else {
			$.ajax({
            url: "'.Url::toRoute(['cabinet/basket']).'",
            data: {"recipe-id": input.data("recipe-id"), "count": value},
            success: function(data) {
            }
        });
		}
    });

    $(".recipe-item .remove").click((e)=>{
    	let elem = $(e.target);
    	let recipe_id = $(e.currentTarget).data("recipe-id");
    	if (confirm("'.\Yii::t('app', 'remove-question').'")) {
    		elem.closest(".card").remove();
    		$.ajax({
	            url: "'.Url::toRoute(['cabinet/basket']).'",
	            data: {"recipe-id": recipe_id, "remove": 1},
	            success: function(data) {
	            	if (psrseInt(data) == 1)
	            		elem.closest(".card");
	            }
	        });
    	}
    });
', View::POS_READY, 'item_basket');

?>
<div class="card">
	<div class="recipe-item card-body basket">
		<div class="recipe-content">
			<a href="<?=$link?>"><div class="image" style="background-image: url(<?=Recipes::UrlImage($model)?>)"></div></a>
			<div class="recipe-detail">
				<h4 class="card-title"><a href="<?=$link?>"><?=$model['name']?></a></h4>
				<div class="description"><?=StringHelper::truncateWords($model['description'], 20, Html::a('... (читать дальше)', $linkArray));?></div>

				<div class="actions">
					<label for="<?=$field_name?>"><?=\Yii::t('app', 'basket-count')?>:</label> <input type="number" data-recipe-id="<?=$model['id']?>" value="<?=$model->basketcount?>" name="<?=$field_name?>">
					<button class="btn btn-light remove" data-recipe-id="<?=$model['id']?>"><?=\Yii::t('app', 'remove')?></button>
				</div>
			</div>
		</div>
	</div>
</div>