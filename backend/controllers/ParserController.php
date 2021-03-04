<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Parser;

/**
 * Site controller
 */
class ParserController extends Controller
{

    private function getScheme($name) {
        $fileName = dirname(__FILE__).'/schemes/'.$name.'.json';
        if (file_exists($fileName)) {
            $data = json_decode(file_get_contents($fileName));
            return $data;
        } else return null;
    }

    private function parseBegin($url, $scheme) {
        Parser::$resfreshIteration = 0;
        return Parser::parseNext($url, $scheme);
    }

    public function actionIndex()
    {
    	$model = new Parser();

        if ($post = Yii::$app->request->post('Parser')) {
            $model = Parser::parseBegin($post['url'], $post['scheme']);
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }
}
?>