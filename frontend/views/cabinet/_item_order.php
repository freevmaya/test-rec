<?
	use yii\helpers\Url;
	use common\helpers\Utils;
	use common\models\Orders;
	use common\models\OrderItems;
	use kartik\rating\StarRating;

	$states = Utils::t('orderstatuslist');
	$items = $model->items;

?>
<div class="card">
	<div class="order card-body" data-id="<?=$model->id?>" data-state="<?=$model->state?>">
		<div class="order-detail">
			<input type="checkbox">
			<span class="card-title"><?=$model->OrderDate?></span>
			<span class="varname"><?=\Yii::t('app', 'status')?></span><i class="state"><?=$states[$model->state]?></i>, 
			<span class="varname"><?=\Yii::t('app', 'totalItemCount')?></span><?=count($items)?>
		</div>
		<div class="order-items">
			<?
			for ($i=0; $i<count($items); $i++) {				
			?>
			<div class="order-item">
				<a href="<?=Url::toRoute(['/recipes/item', 'id'=>$items[$i]->recipe_id])?>">
					<div class="image" style="background-image: url(<?=$items[$i]->recipe->imageUrl()?>)">
						<div class="detail"><?=$items[$i]->recipe->name?></div>
						<div><?=Utils::val_desc($items[$i]->count, \Yii::t('app', 'portions'))?></div>
					</div>
				</a>
			</div>
			<?}?>
		</div>
		<?
		if (($model->state == Orders::STATE_FINISH) || ($model->state == Orders::STATE_PARTNER_FINISH)) {
		?>
		<div class="order-rate">
			<span class="varname"><?=Utils::t("Rate the execution of your order")?></span>
			<?=StarRating::widget(['name'=>'rate', 'value' => $model->executerRate, 
					    'pluginOptions' => [
					    	'disabled'	=>false, 
					    	'showClear'	=>false, 
					    	'showClear' => false, 
					    	'showCaption' => false,					    
        					'step' => 1
					    ],
        				"pluginEvents" => [
        					"change" => "function() {
						        $.ajax({
						            url: '".Url::toRoute(['cabinet/myorders'])."',
						            type: 'POST',
						            data: {action: 'setrate', value: $(this).val(), user_id: ".$model->exec_id.", order_id: ".$model->id."},
						            success: function(res){
						                console.log(res);
						            }
						        });
        					}"
        				]
					]);
					?>			
		</div>
		<?}?>
	</div>
</div>