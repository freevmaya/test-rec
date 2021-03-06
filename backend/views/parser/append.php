<?
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Parser';
?>
<div class="col-lg-5">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(['id' => 'parser-form']); ?>
		<div class="form-group">
		<?= $form->field($model, 'scheme')->textInput(); ?>
    	</div>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
<?if ($recipe) {?>
<div class="col-lg-5">
	<pre>
	<?= print_r($recipe)?>
	</pre>
</div>
<?}?>