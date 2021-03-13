<?
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\RecipesToCats;
use yii\db\ActiveRecord;

class RecipesCats extends ActiveRecord
{

    public static function tableName()
    {
        return 'recipes_cats';
    }

    public function relations()
    {
       return array(
            'recipes'=>array(self::STAT, 'RecipesToCats', 'count(recipe_cat_id)')
        );
    }

    public function getRecipes() {
        return $this->hasMany(RecipesToCats::className(), ['recipe_cat_id'=>'id']);
    }

    public function getRecipeCount() {
        return RecipesToCats::count($this->id);
    }

    public static function refreshTree($tree) {
        $parent = false;
        foreach ($tree as $parentName=>$childs) {
            if (!$parent || ($parent->name != $parentName)) {
                if (!($parent = RecipesCats::find()->where("name LIKE ('".$parentName."')")->one())) {
                    $parent = new RecipesCats();
                    $parent->name = $parentName;
                    $parent->save();
                }
            }

            $child = false;
            foreach ($childs as $childName) {

                if (!$child || ($child->name != $childName)) {
                    if (!($child = RecipesCats::find()->where("name LIKE ('".$childName."')")->one())) {
                        $child = new RecipesCats();
                        $child->name = $childName;
                        $child->parent_id = $parent->id;
                        $child->save();
                    } else if ($child->parent_id != $parent->id) {
                        $child->parent_id = $parent->id;
                        $child->save();
                    }
                }
            }
        }
    }

    public static function getAllTree() {

        $key = "getAllTree";
        $cache = Yii::$app->cache;
        if ($data = $cache->get($key)) {
            return $data;
        }

        /*
        $data = RecipesCats::find()
            //->innerJoinWith('recipes')
            ->where(["active"=>1])
            ->orderBy('sort DESC')->all();
        */


        $command = Yii::$app->db->createCommand('
            SELECT cats.*, (SELECT COUNT(recipe_id) FROM recipes_to_cats WHERE recipe_cat_id=cats.id) AS count_recipe
            FROM recipes_cats cats 
            WHERE cats.active = 1');
        $data = $command->queryAll();

        $cache->set($key, $data, 60 * 60);

    	return $data;
    }

    public static function groupTree() {
 		$list = RecipesCats::find()->where(["active"=>1])->orderBy('sort')->all();   	

 		$result = [];

 		foreach ($list as $item)
 			if (!$item->parent_id) {
 				$childs = [];
		 		foreach ($list as $child) {
		 			if ($child->parent_id == $item->id)
		 				$childs[$child->id] = $child->name;
 				}
 				if (count($childs))
					$result[$item->name] = $childs;
 			}

 		return $result;
    }

    public static function check($cats) {
        $result = [];
        foreach ($cats as $catName) {
            if ($cat = RecipesCats::find()->where(['name'=>$catName])->one())
                $result[] = $cat->id;
            else {
                $cat = new RecipesCats();
                $cat->name = $catName;
                $cat->save();
                $result[] = $cat->id;
            }
        }

        return $result;
    }
}

?>