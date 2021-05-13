<?
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\StringHelper;
use yii\base\Model;
use common\models\Articles;
use common\models\Units;
use common\helpers\Utils;

use kartik\rating\StarRating;

$this->registerCssFile("css/articles.css");

$backLink = $backLink = Url::toRoute(['/articles/index', 'cat_id'=>\Yii::$app->request->get('cat_id')]);
$this->params['breadcrumbs'][] = ['label'=>Utils::mb_ucfirst(Yii::t('app', 'articles')), 'url' => $backLink];
$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', 'article'));
$rate = $model->rates;

if (Yii::$app->user->isGuest) $addArticleLink = Url::toRoute(['/site/login']);
else $addArticleLink = Url::toRoute(['/articles/edit', 'cat_id'=>\Yii::$app->request->get('cat_id')]);

?>
<div class="articles" itemscope itemtype="http://schema.org/Article">
	<div class="column-one">
		
		<a href="<?=$addArticleLink?>"><?=Utils::mb_ucfirst(Yii::t('app', 'add_article'))?></a>

		<div class="card">
			<div class="article-item card-body full">
				<h3 class="card-title" itemprop="name"><?=$model['name']?></h3>
				<div class="article-content">
					<div class="header">
						<div class="article-date"><?=Utils::dateToUserTimeZone($model['created'])?></div>
					<?					
					$form_id = 'article-rates-form-'.$model->id;
					$form = ActiveForm::begin(['id' => $form_id]); ?>
					<?=$form->field($model, 'rates')->widget(StarRating::classname(), [
						    'value'=> $rate,
						    'pluginOptions' => [
						    	'disabled'=>false, 
						    	'showClear'=>false, 
						    	'showClear' => false, 
						    	'showCaption' => false,					    
	        					'step' => 1
	        				],
	        				"pluginEvents" => [
	        					"change" => "function() {
	        						let form = $('#{$form_id}');
							        $.ajax({
							            url: form.attr('action'),
							            type: 'POST',
							            data: form.serialize(),
							            success: function(res){
							                console.log(res);
							            }
							        });
	        					}"
	        				]
						])->label(false);?>
					<?php ActiveForm::end(); ?>
					</div>
					<?if ($model['image']) {?>
					<div class="image" style="background-image: url(<?=Articles::UrlImage($model)?>)" itemprop="resultPhoto"></div>
					<?}?>
					<div class="article-detail">

						<div class="description"><?=$model['description'];?></div>

						<div class="cook_detail">
							<div class="author"><span><?=Utils::mb_ucfirst(\Yii::t('app', 'author'))?>:</span> <?=$model->author->username?></div>
						</div>
						<?
							if (Articles::editable($model)) {?>
								<div class="admin-block">
									<a type="button" class="btn btn-primary"href="<?=Url::toRoute(['/articles/edit', 'id'=>$model['id']])?>"><?=Utils::mb_ucfirst(Yii::t('app', 'edit'))?></a>
									<a type="button" class="btn btn-warning"href="<?=Url::toRoute(['/articles/delete', 'id'=>$model['id']])?>"><?=Utils::mb_ucfirst(Yii::t('app', 'remove'))?></a>
								</div>
							<?}
						?>
					</div>
				</div>
			</div>			
		</div>
	</div>
	<div class="column-two">
		<?Utils::outCatItem(null, $cats);?>
	</div>
</div>