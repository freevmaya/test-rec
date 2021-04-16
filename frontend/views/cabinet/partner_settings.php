<?
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;
use common\models\Recipes;
use common\models\City;
use common\models\Language;
use common\models\User;
use common\widgets\ImageControl;

$user = \Yii::$app->user->identity;

?>
<div class="settings">
    <div class="alert alert-warning"><?=Utils::t('partnerPublicDesc')?></div>
    <?$form = ActiveForm::begin(['id' => 'partner-settings-form', 'options' => ['enctype' => 'multipart/form-data']]);?>
    <div class="col-lg-5">       
        <div class="form-group">
            <?= ImageControl::widget(['form'=>$form, 'model'=>$model, 'field'=>'image']);?>
        </div> 
        <div class="form-group">            
            <?=$form->field($model, 'name')->textInput();?>
            <?=$form->field($model, 'address')->textInput();?>
            <?=$form->field($model, 'email')->textInput();?>
            <?=$form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '+7 (999) 999-99-99',
            ]);?>
            <?=$form->field($model, 'execMethodsArray')->dropDownList(Utils::t('execMethods'), [
                'multiple'=>'multiple',
                'class'=>'form-control'
             ]);?>
    		
            <?=$form->field($model, 'disabled')->checkbox();?>
    	</div>
	    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    	</div>
    </div>
    <?ActiveForm::end();?>
</div>