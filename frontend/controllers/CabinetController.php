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
use common\models\Mainmenu;
use common\models\City;
use common\models\User_settings;
use common\models\UserRates;
use common\helpers\Utils;
use Goutte\Client;

/**
 * Site controller
 */
class CabinetController extends Controller {

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['findorder', 'index', 'mainmenu', 'mygeolocation', 'addinmenu', 'myrecipes', 'settings', 
                            'partner_settings', 'partner_orders', 'favorites', 'basket'],
                'rules' => [
                    [
                        'actions' => ['findorder', 'mainmenu', 'addinmenu', 'partner_settings', 'partner_orders'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role == 'partner';
                        }
                    ],[
                        'allow' => true,
                        'actions' => ['index', 'myrecipes', 'favorites', 'basket', 'settings', 'mygeolocation', 'order'],
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }

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
        	'current'=>'basket',
            'model'=>new Orders()
        ]);
    }

    /*
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
                            'pageSize' => \Yii::$app->params['countPerPage']
                        ]
                    ])
        ]);
    }*/

    public function actionOrder() {

        if ($id = Yii::$app->request->get('id')) {
            $model = Orders::find()->where(['id'=>$id])->one();

            return $this->render('index', [
                'current' =>'order',
                'model' => $model
            ]);
        }
    }

    public function actionMyorders() {
        
        if (\Yii::$app->request->isAjax) {

            $action = \Yii::$app->request->post('action');

            if ($action == 'setrate') {
                if ($user = User::find()->where(['id'=>\Yii::$app->request->post('user_id')])->one()) {
                    $user->setRates(\Yii::$app->request->post('order_id'), \Yii::$app->request->post('value'));
                    return 1;
                }
                return 0;
            }

            $state = \Yii::$app->request->post('state');
            $ids = \Yii::$app->request->post('ids');

            if ($action == 'changestate') 
                return json_encode(Orders::setStates($ids, $state));

            return 0;
        }

        $query = Orders::find()->where(['user_id'=>\Yii::$app->user->id])->orderBy('state DESC, date DESC, id DESC');

        return $this->render('index', [
            'current'=>'myorders',
            'model'=>new ActiveDataProvider([
                        'query' => $query,
                        'pagination' => [
                            'pageSize' => \Yii::$app->params['countPerPage'],
                        ]
                    ])
        ]);
    }

    public function actionSendbasket() {
        if (Yii::$app->request->isPost) {

            $order = new Orders();

            $post = Yii::$app->request->post('Orders');
            $order->attributes = $post;

            if (isset($post['execMethod']) && $post['execMethod'])
                $order->execMethod = implode(',', $post['execMethod']);

            $order->user_id = \Yii::$app->user->id;
            $order->date = date('Y-m-d');
            $order->time = date('H:i:s');
            $order->exec_id = $post['exec_id'];
            $order->state = Orders::STATE_USER_REQUEST;

            if ($order->validate())  {
                if ($order->save())
                    $order->assignBasket();

                $this->redirect(["cabinet/myorders"]);
                return;
            }
        } 

        return $this->render('index', [
            'current'=>'basket',
            'model'=> new Orders()
        ]);
    }

    public function actionMygeolocation() {
        if(\Yii::$app->request->isAjax) { 
            $coord = Yii::$app->request->post('coord');

            if ($user = \Yii::$app->user->identity) {

                $settings = User_settings::find()->where(['user_id'=>$user->id])->one();
                $settings->setLocation($coord);

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
        if (\Yii::$app->request->isAjax) {
            $action = \Yii::$app->request->post('action');

            if ($action == 'changestate') 
                return json_encode(Mainmenu::setStates(\Yii::$app->request->post('ids'), \Yii::$app->request->post('state')));
            else if ($action == 'setprice') 
                return json_encode(Mainmenu::setPrice(\Yii::$app->request->post('recipe_id'), \Yii::$app->request->post('price')));

            return 0;
        }

        return $this->render('index', [
            'current'=>'mainmenu'
        ]);
    }

    public function actionFavorites() {
        return $this->render('index', [
        	'current'=>'favorites'
        ]);
    }

    public function actionAddinmenu() {
        if (\Yii::$app->request->isAjax) {
            $order_id = Yii::$app->request->post('order_id');

            if ($order = Orders::find()->where(['id'=>$order_id])->one()) {
                $prices = Yii::$app->request->post('prices');
                foreach ($order->items as $item) {
                    $price = isset($prices[$item->recipe_id]) ? $prices[$item->recipe_id] : 0;
                    Mainmenu::add($item->recipe_id, $price);
                }
                return 1;
            }
            return 0;
        }
    }

    public function actionFindorder() {
        /*
        if (\Yii::$app->request->isAjax) {
            $order_id = Yii::$app->request->post('order_id');

            if ($order = Orders::find()->where(['id'=>$order_id])->one()) {
                $order->state = Orders::STATE_PROCESS;
                $order->exec_id = \Yii::$app->user->id;
                return $order->save();
            }
            return 0;
        }*/

        $model = \Yii::$app->user->identity->partner_settings;
        $items = [];

        if (Yii::$app->request->isPost) {
            $model->attributes = Yii::$app->request->post('Partner_settings');
            $model->save();
        }

        if (\Yii::$app->user->identity->settings->finddistance) {
            $items = new ActiveDataProvider([
                'query' => Orders::findOrdersQuery(\Yii::$app->user->identity),
                'pagination' => [
                    'pageSize' => 10,
                ]
            ]);
        }

        return $this->render('index', [
            'current'=>'findorder',
            'model'=>$model,
            'items'=>$items
        ]);
    }

    public function actionPartner_orders() {

        if (\Yii::$app->request->isAjax) {
            $state = \Yii::$app->request->post('state');
            $ids = \Yii::$app->request->post('ids');
            $action = \Yii::$app->request->post('action');

            if ($action == 'changestate') 
                return json_encode(Orders::setStates($ids, $state));

            return 0;
        }

        $model = \Yii::$app->user->identity->partner_settings;
        $items = new ActiveDataProvider([
            'query' => Orders::find()->where(['exec_id'=>\Yii::$app->user->id])->orderBy("state DESC, date DESC"),
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        return $this->render('index', [
            'current'=>'partner_orders',
            'model'=>$model,
            'items'=>$items
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

    public function actionPartner_settings() {

        if (User::isPartner()) {
            $model = \Yii::$app->user->identity->partner_settings;

            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post('Partner_settings');
                $post['execMethods'] = implode(',', $post['execMethodsArray']);
                $model->attributes = $post;
                if ($model->validate()) {

                    Utils::upload($model, 'image');
                    $model->save();

                    if ($model->address) {

                        $uset = \Yii::$app->user->identity->settings;

                        if ($uset->city_id) {
                            $city = City::byId($uset->city_id);
                            $address = $city->name.', '.$model->address;
                        } else $address = $model->address;

                        if ($geo = Utils::findGeolocation($address)) {
                            $uset->geoaddress = $geo;
                            $geo = json_decode($geo, true);

                            if (isset($geo['results'][0]['geometry']['location'])) {
                                $uset->setLocation($geo['results'][0]['geometry']['location']);
                                $uset->save();
                            }
                        }
                    }
                }
            }

            return $this->render('index', [
                'current'=>'partner_settings',
                'model'=>$model
            ]);
        }
    }

    public function actionSettings() {
    	$model = \Yii::$app->user->identity->settings;

    	if(Yii::$app->request->isPost){

	        $post = Yii::$app->request->post('User_settings');
	        if ($post['birthday']) $post['birthday'] = date('Y-m-d', strtotime($post['birthday']));

        	$model->attributes = $post;

	        if ( $model->validate() ) {

                //print_r($model->phone);

                if ($model->address) {

                    if ($model->city_id) {
                        $city = City::byId($model->city_id);
                        $address = $city->name.', '.$model->address;
                    } else $address = $model->address;

                    if ($geo = Utils::findGeolocation($address)) {
                        $model->geoaddress = $geo;
                        $geo = json_decode($geo, true);

                        if (isset($geo['results'][0]['geometry']['location'])) {
                            $model->setLocation($geo['results'][0]['geometry']['location']);
                        }
                    }
                }

	        	Utils::upload($model, 'image');
	        	$model->save();
	        }
	    } else {
            if (!$model->finddistance) $model->finddistance = 100;
        }

        return $this->render('index', [
        	'current'=>'settings',
        	'model'=>$model
        ]);
    }

    public function actionIndex() {
        return $this->render('index');
    }
}