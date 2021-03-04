<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class RecipesCats extends ActiveRecord
{

    public static function tableName()
    {
        return 'recipes_cats';
    }

    public static function getAllTree() {
    	return RecipesCats::find()->where(["active"=>1])->orderBy('sort')->all();
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
}

?>