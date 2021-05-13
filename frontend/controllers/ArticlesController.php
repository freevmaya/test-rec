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
use common\models\Articles;
use common\models\ArticlesCats;
use common\models\UploadImage;
use common\models\Ingredients;
use common\models\Stages;
use common\models\Favorites;
use common\models\Basket;
use common\models\Mainmenu;
use common\models\Consist;
use common\helpers\Utils;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Articles controller
 */
class ArticlesController extends Controller {

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete', 'edit', 'editcat'],
                'rules' => [
                    [
                        'actions' => ['item', 'delete', 'edit', 'editcat'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role == 'admin';
                        }
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $cat_id = Yii::$app->request->get('cat_id');
        $consist_id = Yii::$app->request->get('consist-id');
        $consist = null;
        $where = null;

        if (!Utils::IsAdmin()) $where = 'block_id = 0';

        if ($consist_id) {

            $consist = Consist::findOne(['id'=>$consist_id]);
            $join = [['INNER JOIN', 'consist_to_article', 'consist_to_article.article_id=articles.id']];
            $where = ($where ? ($where.' AND ') : '').'consist_to_article.consist_id=:consist_id';
            $provider = Articles::dataProvider($cat_id, $where, $join);
            $provider->query->addParams([':consist_id'=>$consist_id]);

        } else $provider = Articles::dataProvider($cat_id, $where);

        return $this->render('index', [
            "cats"=>ArticlesCats::getAllTree(),
            "cat_id"=>$cat_id,
            "consist"=>$consist,
            "dataProvider"=>$provider
        ]);
    }

    public function actionItem($id) {
    	$model = Articles::findOne(['id'=>$id]);
    	if(\Yii::$app->request->isAjax){
    		$model->attributes = Yii::$app->request->post('Articles');
    		if ($model->validate()) {
    			$model->save();
    			return true;
    		}
	        return false;
	    }
    	return $this->render('Article', ['model'=>$model, 'cats'=>ArticlesCats::getAllTree()]);
    }

    public function actionDelete($id) {
    	$model = Articles::findOne(['id'=>$id]);
    	if (isset($_POST['article-delete'])) {
    		$model->delete();
    		$this->redirect(['articles/index']);
    		return;
    	}
    	return $this->render('deleteArticle', ['model'=> $model]);
    }

    public function actionEditcat($id = null) {
        if ($id) $model = ArticlesCats::findOne(['id'=>$id]); 
        else $model = new ArticlesCats();

        if (Yii::$app->request->isPost) {
            $model->attributes =Yii::$app->request->post('ArticlesCats');
            if ( $model->validate()) {
                Utils::upload($model, 'image');
                $model->save();
            }
        }

        return $this->render('editCat', ['model'=>$model]);
    }

    public function actionEdit($id = null) {
    	if ($id) $model = Articles::findOne(['id'=>$id]); 
    	else $model = new Articles();

	    if (Yii::$app->request->isPost) {

	        $post = Yii::$app->request->post('Articles');

            if (isset($post['id']) && $model->isNewRecord) 
                $model = Articles::findOne(['id'=>$post['id']]); 

        	$model->attributes = $post;

	        if ( $model->validate() ) {

	        	if ($model->isNewRecord) {
	        		$model->created = date("Y-m-d H:i:s");
	        		$model->author_id = Yii::$app->user->id;
	        	}

	        	Utils::upload($model, 'image');

	        	$model->save();
	        }
	    }

    	return $this->render('addArticle', ['model'=>$model]);
    } 
}
?>