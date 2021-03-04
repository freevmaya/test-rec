<?
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;

$this->params['breadcrumbs'][] = $this->title = Utils::mb_ucfirst(Yii::t('app', 'delete'));

?>
<div>
	<?php $form = ActiveForm::begin(['id' => 'recipe-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
		<div class="alert alert-danger" role="alert">Вы действительно хотите удалить рецепт "<?=$model->name?>"?</div>
        <div class="form-group">
            <?=Html::submitButton('Да', ['class' => 'btn btn-primary', 'name'=>'recipe-delete', 'value'=>'on']) ?>
            <?=Html::Button('Отмена', ['class' => 'btn btn-warning', 'onclick' => "document.location.href='".Url::toRoute(['/recipes/index'])."'"]) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>