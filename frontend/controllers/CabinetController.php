<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Basket;
use common\models\Recipes;
use common\models\Orders;
use common\models\OrderItems;
use common\models\User;
use common\models\User_settings;
use common\helpers\Utils;
use Goutte\Client;

/**
 * Site controller
 */
class CabinetController extends Controller {

    const COUNTPERPAGE = 10;

    protected function basketAjax() {
        $recipe_id = Yii::$app->request->get('recipe-id');
        if (Yii::$app->request->get('remove')) {
            return Basket::find()->where(['recipe_id'=>$recipe_id, 'user_id'=>\Yii::$app->user->id])->one()->delete();
        } else return Basket::setCount($recipe_id, Yii::$app->request->get('count'));
    }

    public function actionBasket() {
        if(\Yii::$app->request->isAjax) 
            return $this->basketAjax();

        return $this->render('index', [
        	'current'=>'basket'
        ]);
    }

    protected function selectPartner() {
        $query = User_settings::find()->where(
            [
                'city_id'=>\Yii::$app->user->identity->settings->city_id,
                '`user`.role'=>'partner'
            ])->innerJoin('`user` ON `user`.id=`user_settings`.user_id');

        return $this->render('index', [
            'current'=>'basketselectPartner',
            'model'=>new ActiveDataProvider([
                        'query' => $query,
                        'pagination' => [
                            'pageSize' => self::COUNTPERPAGE,
                        ]
                    ])
        ]);
    }

    public function actionMyorders() {

        $query = Orders::find()->where(['user_id'=>\Yii::$app->user->id]);

        return $this->render('index', [
            'current'=>'myorders',
            'model'=>new ActiveDataProvider([
                        'query' => $query,
                        'pagination' => [
                            'pageSize' => self::COUNTPERPAGE,
                        ]
                    ])
        ]);
    }

    public function actionSendbasket() {
        if (Yii::$app->request->isPost) {

            $order = new Orders();
            $order->user_id = \Yii::$app->user->id;
            $order->date = date('Y-m-d');
            $order->time = date('H:i:s');
            if ($order->save()) {
                $order->assignBasket();
            }

            $this->redirect(["cabinet/myorders"]);
        } else return $this->render('index', [
            'current'=>'basket'
        ]);
    }

    public function actionMygeolocation() {
        if(\Yii::$app->request->isAjax) { 
            $coord = Yii::$app->request->post('coord');

            if ($user = \Yii::$app->user->identity) {

                $settings = User_settings::find()->where(['user_id'=>$user->id])->one();
                $settings->geolocation = json_encode($coord);
                return $settings->save();
            }
        }
        return $this->render('index', [
            'current'=>'mygeolocation'
        ]);
    }

    public function actionMyrecipes() {
        return $this->render('index', [
        	'current'=>'myrecipes'
        ]);
    }

    public function actionMainmenu() {
        return $this->render('index', [
            'current'=>'mainmenu'
        ]);
    }

    public function actionFavorites() {
        return $this->render('index', [
        	'current'=>'favorites'
        ]);
    }

    public function actionChangetypeaccount() {

        if ($model = \Yii::$app->user->identity) {
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post('User');
                $model->role = $post['role'];
                if ($model->validate()) $model->save();
                else print_r($model->getErrors());
            }

            return $this->render('index', [
                'current'=>'changetypeaccount',
                'model'=>$model
            ]);
        }
    }

    protected function findGeolocation($settings) {
        if ($settings->address) {

            $url = "https://maps.google.com/maps/api/geocode/json?key=AIzaSyCoKk5jGpU844xvp1--OmnPaF7CvA2XlxY&address=".$settings->address;            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }
    } 

    public function actionSettings() {
    	$model = \Yii::$app->user->identity->settings;
        $geores = false;

    	if(Yii::$app->request->isPost){

	        $post = Yii::$app->request->post('User_settings');
	        if ($post['birthday']) $post['birthday'] = date('Y-m-d', strtotime($post['birthday']));

        	$model->attributes = $post;

	        if ( $model->validate() ) {

                //print_r($model->phone);
	        	Utils::upload($model, 'image');
	        	$model->save();

                if ($model->address) {
                    $geores = $this->findGeolocation($model);
                }
	        }
	    }

        return $this->render('index', [
        	'current'=>'settings',
        	'model'=>$model,
            'geores'=>$geores
        ]);
    }

    public function actionIndex() {
        return $this->render('index');
    }
}