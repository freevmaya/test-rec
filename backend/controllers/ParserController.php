<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\StringHelper;
use common\models\LoginForm;
use common\models\Parser;
use common\models\Recipes;
use common\models\RecipesCats;
use common\models\Ingredients;
use common\models\Consist;
use common\helpers\Utils;

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

    public function actionParser_json($url, $scheme, $refreshRequired = false) {
        return json_encode(Parser::parseBegin($url, $scheme, $refreshRequired));
    }

    public function actionRefresh_json($scheme) {
        return json_encode(Parser::parseRefresh($scheme));
    }

    public function actionIndex()
    {
    	$model = new Parser();
        $passed = null;

        if ($post = Yii::$app->request->post('Parser')) {
            $passed = Parser::parseBegin($post['url'], $post['scheme'], Yii::$app->request->post('refresh-required'));
            $model->attributes = $post;
        }

        return $this->render('index', [
            'model' => $model,
            'passed' => $passed
        ]);
    }

    public function actionAppendjson($scheme, $count_limit = 1, $author_id = 2, $pid=0) {
        $passList = [];

        if ($scheme) {

            if ($pid > 0) $list = Parser::find()->where(['pid'=>$pid])->all();
            else $list = Parser::find()->where(['scheme'=>$scheme, 'state'=>'active'])->limit($count_limit)->all();
            
            foreach ($list as $item) {
                $recipe = json_decode($item->result)[0];
                if (isset($recipe->image)) {
                    $file = pathinfo($recipe->image);
                    $cats = RecipesCats::check($recipe->subcats);
                    $consist = isset($recipe->ingridients_full) ? Consist::check($recipe->ingridients_full) : [];
                    //$ingredients = $recipe->ingredients ? Ingredients::check($recipe->ingredients) : false;

                    $imageBody = false;
                    $fileName = md5($file['filename']).'.'.$file['extension'];
                    $filePath = \Yii::$app->params['recipeImagesPath'].'/'.$fileName;
                    if (!file_exists($filePath)) {
                        if ($imageBody = file_get_contents($recipe->image))
                            file_put_contents($filePath, $imageBody);
                    } else $imageBody = true;

                    if ($imageBody) {

                        if (!$rec = Recipes::find()->where(['parser_id'=>$item->pid])->one()) {
                            $rec = new Recipes();
                            $rec->created       = date('Y-m-d h:i:s');
                        }
                        $rec->lang          = Utils::getLang();
                        $rec->author_id     = $author_id;
                        $rec->name          = $recipe->name;
                        $rec->description   = $recipe->description;
                        $rec->image         = $fileName;
                        $rec->cook_time     = Utils::timeParseRUS($recipe->cook_time);
                        $rec->portion       = StringHelper::truncate(is_array($recipe->portion) ? implode(',', $recipe->portion) : $recipe->portion, 29, '...');
                        $rec->category_ids  = $cats;
                        $rec->consist_ids   = $consist;
                        $rec->parser_id     = $item->pid;
//                        print_r($ingredients);
                        if ($rec->save()) {
                            $item->state = 'processed';
                            $item->save();

                            if ($recipe->ingredients) $rec->saveIngredients($recipe->ingredients);
                            if ($recipe->stages) $rec->saveStages($recipe->stages);

                            $passList[] = $item->pid;
                        } else {
                            \Yii::error($passList[] = $rec->getErrors());
                            //$item->state = 'deferred';
                            //$item->save();
                        }
                    } else {
                       $item->state = 'deferred';
                       $item->save();
                    }
                }
            }
        }

        return json_encode($passList);
    }

    public function actionAppend()
    {
        $list = null;
        $model = new Parser();
        $countLimit = 1;

        if ($post = Yii::$app->request->post('Parser')) {
            if ($post['id']) {
                $recipe = Recipes::find()->where(['id'=>$post['id']])->one();
                $post['pid'] = $recipe->parser_id;
            }

            $list = $this->actionAppendjson($post['scheme'], 
                    isset($post['count_limit']) ? $post['count_limit'] : 1,
                    isset($post['author_id']) ? $post['author_id'] : \Yii::$app->user->id,
                    $post['pid']);
            $model->attributes = $post;
        } else {
            if (Yii::$app->request->get('pid')) 
                $model->pid = Yii::$app->request->get('pid');
        }

        return $this->render('append', [
            'model' => $model,
            'list' => $list
        ]);
    }
}
?>