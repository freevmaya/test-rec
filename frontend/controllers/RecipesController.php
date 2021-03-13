<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Recipes;
use common\models\RecipesCats;
use common\models\UploadImage;
use common\models\Ingredients;
use common\models\Stages;
use common\models\Favorites;
use common\models\Basket;
use common\helpers\Utils;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Recipes controller
 */
class RecipesController extends Controller {
    public function actionIndex()
    {
        return $this->render('index', [
            "cats"=>RecipesCats::getAllTree(),
            "dataProvider"=>Recipes::dataProvider(Yii::$app->request->get('cat_id'))
        ]);
    }

    public function actionTogglefavorite($id) {
        if(\Yii::$app->request->isAjax){
            return Favorites::toggle($id);
        }
    }

    public function actionTogglebasket($id) {
        if(\Yii::$app->request->isAjax){
            return Basket::toggle($id);
        }
    }

    public function actionItem($id) {
    	$model = Recipes::findOne(['id'=>$id]);
    	if(\Yii::$app->request->isAjax){
    		$model->attributes = Yii::$app->request->post('recipes');
    		if ($model->validate()) {
    			$model->save();
    			return true;
    		}
	        return false;
	    }
    	return $this->render('Recipe', ['model'=>$model, 'cats'=>RecipesCats::getAllTree()]);
    }

    public function actionEditstage($id, $recipe_id) {
        $model = is_numeric($id) ? Stages::findOne(['id'=>$id]) : (new Stages());
        if(Yii::$app->request->isPost){
            $model->attributes = Yii::$app->request->post('Stages');
            if ($model->validate()) {
                Utils::upload($model, 'image');
                $model->save();
                $this->redirect(['recipes/edit', 'id'=>Yii::$app->request->get('recipe_id'), 'cat_id'=>Yii::$app->request->get('cat_id')]);
                return;
            }
        }
        return $this->render('Editstage', ['model'=>$model]);
    }

    public function actionDelete($id) {
    	$model = Recipes::findOne(['id'=>$id]);
    	if (isset($_POST['recipe-delete'])) {
    		$model->delete();
    		$this->redirect(['recipes/index']);
    		return;
    	}
    	return $this->render('deleteRecipe', ['model'=> $model]);
    }

    public function actionEdit($id = null) {
    	if ($id) $model = Recipes::findOne(['id'=>$id]); 
    	else $model = new Recipes();

	    if(Yii::$app->request->isPost){

	        $post = Yii::$app->request->post('recipes');

        	$model->attributes = $post;

	        if ( $model->validate() ) {

	        	if ($model->isNewRecord) {
	        		$model->created = date("Y-m-d H:i:s");
	        		$model->autor_id = Yii::$app->user->id;
	        	}

	        	Utils::upload($model, 'image');

	        	$model->save();

                if ($Ingr = Yii::$app->request->post('Ingr')) $model->saveIngredients($Ingr, Yii::$app->request->post('Unit'));
	    		//$this->redirect(['recipes/index', 'cat_id'=>\Yii::$app->request->get('cat_id')]); return;
	        }
	    }

    	return $this->render('addRecipe', ['model'=>$model]);
    } 
}
?>