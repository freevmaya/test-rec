<?
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;

$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', 'delete'));

?>
<div>
	<?php $form = ActiveForm::begin(['id' => 'article-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
		<div class="alert alert-danger" role="alert"><?=Utils::t('removearticleask', ['article'=>$model->name])?></div>
        <div class="form-group">
            <?=Html::submitButton('Да', ['class' => 'btn btn-primary', 'name'=>'article-delete', 'value'=>'on']) ?>
            <?=Html::Button('Отмена', ['class' => 'btn btn-warning', 'onclick' => "document.location.href='".Url::toRoute(['/articles/index'])."'"]) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>