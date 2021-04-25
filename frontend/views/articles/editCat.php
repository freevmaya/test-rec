<?
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\widgets\MaskedInput;
use \yii\web\JsExpression;
use common\models\Articles;
use common\models\ArticlesCats;
use common\models\Ingredients;
use common\widgets\IngredientList;
use common\widgets\ImageControl;
use common\helpers\Utils;
use yii\web\View;
use yii\jui\AutoComplete;

$this->title =  Utils::t($model->isNewRecord ? "new_cat" : 'cat');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="articles">
	<div class="column-one">		
		<div class="row">
	        <?php $form = ActiveForm::begin(['id' => 'article-cat-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
	        <div class="col-lg-5">
                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'parent_id')->dropDownList(
                    array_merge(['0' => '---'], ArrayHelper::map(ArticlesCats::find()->orderBy('sort')->all(), 'id', 'name'))
                ) ?>
                <?= ImageControl::widget(['form'=>$form, 'model'=>$model, 'field'=>'image']);?>
        		<?if ($model->id) {?>
        		<input type="hidden" name="ArticlesCats[id]" value="<?=$model->id?>">
        		<?}?>
            	<?=Html::submitButton(Utils::t('submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
	        </div>
            <?php ActiveForm::end(); ?>
	    </div>		
	</div>
</div>