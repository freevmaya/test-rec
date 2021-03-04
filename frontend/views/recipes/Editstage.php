<?
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use common\widgets\ImageControl;
use common\helpers\Utils;
use yii\bootstrap\ActiveForm;

$backLink = Url::toRoute(['/recipes/edit', 
		'cat_id'=>\Yii::$app->request->get('cat_id'),
		'id'=>\Yii::$app->request->get('recipe_id')
	]);

$this->params['breadcrumbs'][] = ['label'=>Utils::mb_ucfirst(Yii::t('app', 'recipe')), 'url' => $backLink];
$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', 'stage'));

?>

<div class="recipes">
	<div class="column-one">
		<?php $form = ActiveForm::begin(['id' => 'stage-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
		<div class="col-lg-5">
			<?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>
            <?= ImageControl::widget(['form'=>$form, 'model'=>$model, 'field'=>'image']);?>
			<div>
				<input type="hidden" name="Stages[recipe_id]" value="<?=\Yii::$app->request->get('recipe_id')?>">
			<?= Html::submitButton(Yii::t('app', 'submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
			<?= Html::button(Yii::t('app', 'cancel'), ['class' => 'btn', 'name' => 'contact-button']) ?>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
	<div class="column-two">
	</div>
</div>