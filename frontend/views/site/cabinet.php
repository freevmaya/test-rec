<?

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\base\Model;
use common\models\Recipes;
use common\models\Units;
use common\helpers\Utils;

$current = Yii::$app->request->get('page');
$backLink = $backLink = Url::toRoute(['/site/cabinet']);
$this->params['breadcrumbs'][] = ['label'=>Utils::mb_ucfirst(Yii::t('app', 'Cabinet')), 'url' => $backLink];

if ($current)
	$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', $current));

$menu = [
	['favorites', 'heart-empty'],
	//['bookmarks', 'paperclip'],
	['basket', 'shopping-cart'],
	['myrecipes', 'briefcase'],
	['deliveryaddress', 'plane'],
	['settings', 'wrench']
];


?>
<div class="cabinet">
	<div class="column-one">
		<h1><?=Yii::t('app', $current ? $current : 'Cabinet')?></h1>
		<?
			if ($current) echo $this->render('cabinet/'.$current);
		?>
	</div>
	<div class="column-two">
		<div class="menu">
		<?foreach ($menu as $item) {?>
		<a class="menu-item<?=$current==$item[0]?' current':''?>" href="<?=Url::toRoute(['/site/cabinet', 'page'=>$item[0]])?>">
			<span class="glyphicon glyphicon-<?=$item[1]?>" aria-hidden="true"></span>
			<span><?=Yii::t('app', $item[0])?></span>
		</a>
		<?}?>
		</div>
	</div>	
</div>