<?
	use yii\helpers\Url;
	use common\helpers\Utils;
	use common\models\Orders;
	use common\models\OrderItems;

	$states = Yii::t('app', 'orderstatuslist');
	$items = $model->items;
?>
<div class="card">
	<div class="order card-body">	
		<div class="order-detail">
			<span class="card-title"><?=date(\Yii::t('app', 'dateformat'), strtotime($model->date))?></span>
			<span><?=\Yii::t('app', 'status')?>: </span><?=$states[$model->state]?>, 
			<span><?=\Yii::t('app', 'totalItemCount')?>: </span><?=count($items)?>
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
	</div>
</div>