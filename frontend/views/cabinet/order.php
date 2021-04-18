<?
	use yii\helpers\Url;
	use yii\helpers\Html;
	use yii\helpers\ArrayHelper;
	use common\helpers\Utils;
	use common\models\Orders;
	use common\models\Mainmenu;
	use common\models\OrderItems;

	$states = Utils::t('orderstatuslist');
	$items = $model->items;
	$settings = $model->user->settings;
    $is_executer = $model->exec_id == \Yii::$app->user->id;

	if ($model->exec_id) {
		$mymenu = \Yii::$app->db->createCommand(
        	'SELECT recipe_id, price FROM '.Mainmenu::tableName().' WHERE user_id='.$model->exec_id
    	)->queryAll();
	    $partner_settings = $model->executer->partner_settings;
	    $currency = $model->executer->settings->language->currency;
	} else {
		$mymenu = [];
		$partner_settings = null;
	    $currency = $model->user->settings->language->currency;
	}

    $mm_recipes = ArrayHelper::getColumn($mymenu, 'recipe_id');
    $mm_prices 	= ArrayHelper::getColumn($mymenu, 'price');

    echo $this->render('_orders_js');
?>
<div class="card partner">
	<div class="order card-body" data-id="<?=$model->id?>" data-state="<?=$model->state?>">
		<h1><?=Utils::t('Stateorder');?></h1>
		<div class="order-detail">
			<span class="card-title"><?=date(\Yii::t('app', 'dateformat'), strtotime($model->date))?></span>
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
					<?if ($settings->phone) {?>
					<tr>
						<td class="varname"><?=Utils::t('phone')?></td><td><a href="tel:<?=$settings->phone?>"><?=$settings->phone?></a></td>
					</tr>
					<?}?>
					<tr>
						<td class="varname"><?=\Yii::t('app', 'status')?></td><td>
							<?=$states[$model->state]?>
						</td>
					</tr>
					<?if ($partner_settings) {?>
					<tr>
						<td class="varname"><?=Utils::t('Executer')?></td><td><a href="<?=Url::toRoute('site/executer')?>"><?=$partner_settings->name?></a> <a href="tel:<?=$partner_settings->phone?>"><?=$partner_settings->phone?></a></td>
					</tr>
					<?}?>
					<tr>
						<td class="varname"><?=Utils::t('totalItemCount')?></td><td><?=count($items)?> <a href="#" class="totalItemCount"><?=Utils::t("show")?></a></td>
					</tr>
				</table>
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
				<?if ($model->exec_id) {?>
				<div class="order-info">
					<span class="id-order">ID:<?=$model->id?></span>
					<?if (!$mmfull && $is_executer) {?>
					<a class="btn" data-toggle="modal" data-target="#questionAddInMenu"><?=Utils::t('notfullmainmenu')?></a>
					<?} else if ($is_executer && ($totalPrice == 0)) {?>
						<a class="btn" href="<?=Url::toRoute('cabinet/mainmenu')?>"><?=Utils::t('nopriceset')?></a>
						<?} else {?>
						<span class="varname"><?=Utils::t('totalPrice')?></span><?=$totalPrice?> <?=$currency?>, 
					<?}?>
				</div>
				<?}?>
				<div>
					<span class="varname"><?=Utils::t('requireExecMethods')?></span><?=implode(', ', $model->execMethodList)?></span>
				</div>
			</div>
		</div>
	</div>
</div>