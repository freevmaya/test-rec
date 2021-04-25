<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Sitemap generator';
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Создание файла Sitemap.xml:</p>

    <?php $form = ActiveForm::begin(['id' => 'sitemap-form']); ?>

        <div class="form-group">
        	<input type="hidden" name="Sitemap[action]" value="1">
            <?= Html::submitButton('Sitemap generate', ['class' => 'btn btn-primary btn-block', 'name' => 'sitemap-button']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>