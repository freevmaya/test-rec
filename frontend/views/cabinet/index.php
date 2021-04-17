<?

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\base\Model;
use common\models\Recipes;
use common\models\Units;
use common\models\User;
use common\models\Orders;
use common\models\Basket;
use common\helpers\Utils;

$backLink = $backLink = Url::toRoute(['/cabinet']);
$this->params['breadcrumbs'][] = ['label'=>Utils::mb_ucfirst(Yii::t('app', 'Cabinet')), 'url' => $backLink];

$current = isset($current) ? $current : false;

if ($current)
	$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', $current));

$menu = [];

if (User::isPartner()) {
	$menu[] = ['mygeolocation', 'screenshot'];
	$menu[] = ['mainmenu', 'list'];
	$menu[] = ['findorder', 'search'];
	$menu[] = ['partner_orders', 'inbox'];
	$menu[] = ['partner_settings', 'wrench'];
	$menu[] = ['separate'];
} else {
//	if ($basketCount = Basket::totalCount()) 
	$menu[] = ['basket', 'shopping-cart'];
	$menu[] = ['myorders', 'list-alt'];
}

$menu = array_merge($menu, [
	['favorites', 'heart-empty'],
	['myrecipes', 'briefcase'],
	//['deliveryaddress', 'plane'],
	['settings', 'wrench']
]);

?>
<div class="cabinet">
	<?if ($current) {?>
	<div class="column-one">
		<h1><?=Yii::t('app', $current)?></h1>
		<?
			echo $this->render($current, ['model'=>isset($model) ? $model : null, 'items'=>isset($items) ? $items : null]);
		?>
	</div>
	<div class="column-two">
		<div class="menu">
	<?} else {?>
	<div class="general-menu">
		<h1><?=Yii::t('app', 'Cabinet')?></h1>
		<div>
	<?}?>
		<?foreach ($menu as $item)
			if ($item[0] == 'separate') {
				echo "<hr>";
			} else {
		?>
		<a class="menu-item" href="<?=Url::toRoute(['/cabinet/'.$item[0]])?>">
			<span class="glyphicon glyphicon-<?=$item[1]?>" aria-hidden="true"></span>
			<span><?=Yii::t('app', $item[0])?></span>
		</a>
		<?}?>
		</div>
	</div>	
</div>