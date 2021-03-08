<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Parser;
use common\models\Recipes;
use common\models\RecipesCats;
use common\models\Ingredients;
use common\helpers\Utils;

/**
 * Cron controller
 */
class CronController extends Controller
{
	public function beforeAction($action)
    {
		if (!parent::beforeAction($action)) {
			return false;
		}

		if (( $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] ) ||
		(!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin()))
		{
			return true;
		}
		return false;
    }

    public function actionParser_json($url, $scheme) {
        Parser::parseBegin($url, $scheme);
        return json_encode(Parser::getPassed());
    }

    public function actionAppendjson($scheme, $count_limit = 1) {
        return Parser::actionAppendjson($scheme, $count_limit);
    }
}