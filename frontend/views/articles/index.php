<?

use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;
use common\models\ArticlesCats;

$this->registerCssFile("css/articles.css");

$this->params['breadcrumbs'][] = $this->title = Utils::t('Articles');

if ($cat_id = \Yii::$app->request->get('cat_id')) {
	$cat = ArticlesCats::find()->where(['id'=>$cat_id])->one();
	$this->params['breadcrumbs'][] = $cat->name;
}
if (Yii::$app->user->isGuest) $addArticleLink = Url::toRoute(['/site/login']);
else $addArticleLink = Url::toRoute(['/articles/edit', 'cat_id'=>$cat_id]);
?>
<div class="articles">
	<div class="column-one">
		<?if ($consist) {?>
			<h2><?=$consist->name?></h2>
		<?}?>
		<a href="<?=$addArticleLink?>"><?=Utils::t('add_article')?></a>
		<div class="articles-list">		
		<?
			echo ListView::widget([
			    'dataProvider' => $dataProvider,
			    'itemView' => '_item'
			]);
		?>
		</div>
	</div>
	<div class="column-two">
			<div class="admin-block">
				<a href="<?=Url::toRoute(['/articles/editcat', 'id'=>$model['id']])?>"><?=Utils::t('add_cat')?></a>
			</div>
		<?Utils::outCatItem(null, $cats, 0, ['/articles']);
		if (Utils::isAdmin()) {?>
		<?}?>
	</div>
</div>