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

    public function actionParser_json($url, $scheme) {
        Parser::parseBegin($url, $scheme);
        return json_encode(Parser::getPassed());
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

    public function actionAppendjson($scheme, $count_limit = 1) {
        $passList = [];

        if ($scheme) {

            $list = Parser::find()->where(['scheme'=>$scheme, 'state'=>'active'])->all();
            $count = 0;
            foreach ($list as $item) {
                $recipe = json_decode($item->result)[0];
                if (isset($recipe->image)) {
                    $file = pathinfo($recipe->image);
                    $cats = RecipesCats::check($recipe->subcats);
                    $ingredients = $recipe->ingredients ? Ingredients::check($recipe->ingredients) : false;


                    $imageBody = false;
                    $fileName = md5($file['filename']).'.'.$file['extension'];
                    $filePath = \Yii::$app->params['recipeImagesPath'].'/'.$fileName;
                    if (!file_exists($filePath)) {
                        if ($imageBody = file_get_contents($recipe->image))
                            file_put_contents($filePath, $imageBody);
                    } else $imageBody = true;

                    if ($imageBody) {
                        $new = new Recipes();
                        $new->created       = date('Y-m-d h:i:s');
                        $new->lang          = Utils::getLang();
                        if (Yii::$app->user->id)
                            $new->author_id     = Yii::$app->user->id;
                        
                        $new->name          = $recipe->name;
                        $new->description   = $recipe->description;
                        $new->image         = $fileName;
                        $new->cook_time     = Utils::timeParseRUS($recipe->cook_time);
                        $new->portion       = $recipe->portion;
                        $new->category_ids  = $cats;
                        $new->parser_id     = $item->pid;
//                        print_r($ingredients);
                        if ($new->save()) {
                            $item->state = 'processed';
                            $item->save();

                            if ($ingredients) $new->saveIngredients($ingredients['values'], $ingredients['units']);
                            if ($recipe->stages) $new->saveStages($recipe->stages);

                            $passList[] = $item->pid;
                        } else {
                            \Yii::error($new->getErrors());
                            //$item->state = 'deferred';
                            //$item->save();
                        }
                        $count++;
                        if ($count >= $count_limit) break;
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
            $list = $this->actionAppendjson($post['scheme']);
            $model->scheme = $post['scheme'];
        }

        return $this->render('append', [
            'model' => $model,
            'list' => $list
        ]);
    }
}
?>