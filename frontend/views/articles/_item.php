<?
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use common\helpers\Utils;
use common\models\Articles;
use kartik\rating\StarRating;

$linkArray = ['/articles/item', 'id'=>$model['id'], 'cat_id'=>\Yii::$app->request->get('cat_id')];
$link = Url::toRoute($linkArray);

?>
<div class="card">
	<div class="article-item card-body">
		<h3 class="card-title"><a href="<?=$link?>"><?=$model['name']?></a></h3>
		<div class="article-content">
			<a href="<?=$link?>"><div class="image" style="background-image: url(<?=Articles::UrlImage($model)?>)"></div></a>
			<div class="article-detail">
				<div class="article-date"><?=Utils::dateToUserTimeZone($model['created'])?></div>
				<div class="description"><?=StringHelper::truncateWords($model['description'], 50, Html::a('... (читать дальше)', $linkArray));?></div>

				<div class="cook_detail">
					<div class="actions">
					<?=StarRating::widget(['name'=>'rate', 'value' => $model['rates'], 
					    'pluginOptions' => [
					    	'disabled'	=>false, 
					    	'showClear'	=>false, 
					    	'showClear' => false, 
					    	'showCaption' => false,					    
        					'step' => 1
					    ]
					]);
					?>
					<?if (!Yii::$app->user->isGuest) 
						echo $this->renderFile(dirname(__FILE__).'/actionBlock.php', ['article'=>$model]);?>
					</div>
				</div>
				<?
					if (Articles::editable($model)) {?>
						<div class="admin-block">
							<a type="button" class="btn btn-primary"href="<?=Url::toRoute(['/articles/edit', 'id'=>$model['id'], 'cat_id'=>\Yii::$app->request->get('cat_id')])?>"><?=Utils::mb_ucfirst(Yii::t('app', 'edit'))?></a>
							<a type="button" class="btn btn-warning"href="<?=Url::toRoute(['/articles/delete', 'id'=>$model['id']])?>"><?=Utils::mb_ucfirst(Yii::t('app', 'remove'))?></a>
						</div>
					<?}
				?>
			</div>
		</div>
	</div>
</div>