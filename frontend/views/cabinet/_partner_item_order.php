<?
	use yii\helpers\Url;
	use yii\helpers\StringHelper;
	use common\helpers\Utils;
	use common\models\Orders;
	use common\models\OrderItems;
	use yii\web\View;
	use kartik\rating\StarRating;

	$states = Yii::t('app', 'orderstatuslist');
	$items 		= $model->items;
	$settings 	= $model->user->settings;
	$inProcess 	= $model->state == Orders::STATE_PROCESS;
	$currency   = $model->user->settings->language->currency;
?>
<div class="card partner">
	<div class="order card-body" data-id="<?=$model->id?>" data-state="<?=$model->state?>">	
		<div class="header">
			<input type="checkbox">
			<span class="card-title"> <?=$model->OrderDate?></span>
			<span class="varname"><?=\Yii::t('app', 'status')?></span><i class="state"><?=$states[$model->state]?></i>
		</div>
		<div class="order-content">
			<div class="avatar" style="background-image: url(<?=$settings->imageUrl()?>)"></div>
			<div>
				<table class="order-user">
					<tr>
						<td class="varname"><?=Utils::t('username')?></td><td><?=$model->user->username?></td>
					</tr>
					<tr>
						<td class="varname"><?=Utils::t('address')?></td><td><?=$settings->address?></td>
					</tr>
					<tr>
						<td class="varname"><?=Utils::t('distance')?></td><td>
							<?=round($settings->calcDistance(\Yii::$app->user->identity->settings), 2)?> <?=Utils::t('distanceunit')?>
						</td>
					</tr>
					<?if ($settings->phone) {?>
					<tr>
						<td class="varname"><?=Utils::t('phone')?></td><td><?=$settings->phone?></td>
					</tr>
					<?}?>
					<tr>
						<td class="varname"><?=Utils::t('totalItemCount')?></td><td><?=count($items)?> <a href="#" class="totalItemCount"><?=Utils::t("show")?></a></td>
					</tr>
				</table>
				<?
				if (($model->state == Orders::STATE_FINISH) || 
					($model->state == Orders::STATE_PARTNER_FINISH) ||
					($model->state == Orders::STATE_REMOVED)) {
					?><span class="varname"><?=Utils::t("Evaluation of order fulfillment by the client")?></span><?
					echo StarRating::widget(['name'=>'rate', 'value' => $model->getExecuterRate(), 
					    'pluginOptions' => [
					    	'displayOnly' => true
					    ]
					]);
				}
				?>
			</div>
		</div>
		<div class="order-items">
			<?
			$mmcount = 0;
			$totalPrice = 0;
			for ($i=0; $i<count($items); $i++) {				
				$item = $items[$i];
				$ixmm = array_search($item->recipe_id, $mm_recipes);
				if ($inmymenu = $ixmm > -1) $mmcount++;
				$totalPrice += $inmymenu ? ($mm_prices[$ixmm] * $item->count) : 0;
			?>
			<div class="order-item<?=$inmymenu ? '': ' nomymenu'?>">
				<table>
					<tr>
						<td class="image" style="background-image: url(<?=$items[$i]->recipe->imageUrl()?>)"></td>
						<td>
							<a href="<?=Url::toRoute(['/recipes/item', 'id'=>$item->recipe_id])?>">
								<div class="detail" title="<?=$item->recipe->name?>"><?=$item->recipe->name;?></div>
							</a>
							<div><?=Utils::val_desc($item->count, \Yii::t('app', 'portions'))?></div>
						</td>
					</tr>
					<tr>
						<td class="varname"><?=Utils::t('price')?></td>
						<td class="price">
							<?=$inmymenu ? $mm_prices[$ixmm] : '<input type="number" data-recipe_id="'.$item->recipe_id.'" value="0">'?> <?=$currency?>
						</td>
					</tr>
				</table>
			</div>
			<?}
				$mmfull = $mmcount == count($items);
			?>
		</div>
		<div class="order-footer<?=$mmfull ? '' : ' noinmm'?>">
			<div>
				<div class="order-info">
					<span class="id-order">ID:<?=$model->id?></span>
					<?if (!$mmfull) {?>
					<a class="btn" data-toggle="modal" data-target="#questionAddInMenu"><?=Utils::t('notfullmainmenu')?></a>
					<?} else {?>
						<span class="varname"><?=Utils::t('totalPrice')?></span><?=$totalPrice?> <?=$currency?>, 
					<?}?>
				</div>
				<div>
					<span class="varname"><?=Utils::t('requireExecMethods')?></span><?=implode(', ', $model->execMethodList)?></span>
				</div>
			</div>
		</div>
	</div>
</div>