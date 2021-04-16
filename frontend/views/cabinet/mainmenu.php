<?
use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;
use common\models\Recipes;
use common\models\Mainmenu;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\data\ActiveDataProvider;

?>

<div class="mainmenu">
	<?=$this->render('../layouts/edit-toolbar', [
		'params'=>[
			'item-selector'=>'.recipe-item',
			'action-url'=>Url::toRoute(['cabinet/mainmenu']),
			'states'=>Utils::t('mmstatuslist'),
			'actions'=>[
				[
					'action'=>'Enabled',
					'include'=>[Mainmenu::STATE_INACTIVE],
					'state'=>Mainmenu::STATE_ACTIVE,
					'question'=>'Activate selected recipes?',
					'icon'=>'glyphicon-ok'
				],[
					'action'=>'Disable',
					'include'=>[Mainmenu::STATE_ACTIVE],
					'state'=>Mainmenu::STATE_INACTIVE,
					'question'=>'Disable selected recipes temporarily?',
					'icon'=>'glyphicon-ban-circle'
				],[
					'action'=>'Remove',
					'include'=>[Mainmenu::STATE_ACTIVE, Mainmenu::STATE_INACTIVE],
					'state'=>Mainmenu::STATE_REMOVE,
					'question'=>'Delete selected items?',
					'icon'=>'glyphicon-trash'
				]
			]
		]
	]);?>
	<div class="recipes-list">
	<?
		$query = Mainmenu::find()->where('user_id = '.Yii::$app->user->id.' AND state > 0')->with('recipe');
        $query->orderBy('state DESC, price');

		echo ListView::widget([
		    'dataProvider' => new ActiveDataProvider([
	            'query' => $query,
	            'pagination' => [
	                'pageSize' => 10,
	            ]
	        ]),
		    'itemView' => '_item_mymenu',
		    'summary' => ''
		]);
	?>
	</div>
</div>
<?

$this->registerJs('
	let btns = $(".recipe-item .btn");
	btns.each((i, btn)=>{
		btn = $(btn);
		let item = btn.closest(".recipe-item");

		let setProc = ()=>{
			btn.css("display", "inline-block");
		}
		item.find(".price input")
			.change(setProc)
			.keydown(setProc);
	});

	btns.click((e)=>{
		let btn = $(e.currentTarget);
		let item = btn.closest(".recipe-item");
		let recipe_id = item.data("id");
		if (recipe_id) {
			$.ajax({
	            url: "'.Url::toRoute(['cabinet/mainmenu']).'",
	            method: "POST",
	            data: {"recipe_id": recipe_id, "price": item.find(".price input").val(), "action": "setprice"},
	            success: function(data) {
					btn.css("display", "none");
	            }
	        });

		}
	})
', View::POS_READY, 'mainmenu');
?>