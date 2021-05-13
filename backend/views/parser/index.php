<?
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Parser';
?>
<div class="col-lg-5">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(['id' => 'parser-form']); ?>
		<div class="form-group">
		<?= $form->field($model, 'url')->textInput(['autofocus' => true]); ?>
		<?= $form->field($model, 'scheme')->textInput(); ?>
		<label from="refresh-required">Refresh</label>
		<input type="checkbox" name="refresh-required">
    	</div>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    <?if ($passed) {?>
    	<pre>
    		<?print_r($passed);?>
    	</pre>
    <?}?>
</div>