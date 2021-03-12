<?
use yii\helpers\Html;
use common\helpers\Utils;
use yii\bootstrap\ActiveForm;
$this->title = 'Parser';
?>
<?=Utils::timeParseRUS('25 мин.');?>
<div class="col-lg-5">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(['id' => 'parser-form']); ?>
		<div class="form-group">
		<?= $form->field($model, 'scheme')->textInput(); ?>
		<?= $form->field($model, 'pid')->textInput(); ?>
    	</div>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
<?if ($list) {?>
<div class="col-lg-5">
	<pre>
	<?= print_r($list)?>
	</pre>
</div>
<?}?>