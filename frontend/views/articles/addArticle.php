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
use dosamigos\tinymce\TinyMce;

$this->title = Utils::t("Article");
$this->params['breadcrumbs'][] = $this->title;

$selectedCats = [];
foreach ($model->categories as $cat) {
	$selectedCats[$cat->id] = ['Selected'=>true];
}

if ($cat_id = \Yii::$app->request->get('cat_id')) $selectedCats[$cat_id] = ['Selected'=>true];

?>
<div class="articles">
	<div class="column-one">		
        <?php $form = ActiveForm::begin(['id' => 'article-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
        <div>
            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'description')->widget(TinyMce::className(), [
            	'options' => ['rows' => 24],
			    'language' => 'ru',
			    'clientOptions' => [
			        'plugins' => [
			            "advlist autolink lists link charmap print preview anchor",
			            "searchreplace visualblocks code fullscreen",
			            "insertdatetime media table contextmenu paste"
			        ],
			        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
			    ]
			]);?>
		</div>				
		<div>
	        <div class="col-lg-5">
            <?= $form->field($model, 'category_ids')->dropDownList(ArticlesCats::groupTree(), ['multiple' => true]) ?>
            <?= $form->field($model, 'block_id')->textInput() ?>
            <?= ImageControl::widget(['form'=>$form, 'model'=>$model, 'field'=>'image']);?>
    		<?if ($model->id) {?>
    		<input type="hidden" name="Articles[id]" value="<?=$model->id?>">
    		<?}?>
        	<?=Html::submitButton(Yii::t('app', 'submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
        	</div>
        </div>
        <?php ActiveForm::end(); ?>
	</div>
</div>