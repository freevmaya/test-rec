<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class RecipesToCats extends ActiveRecord
{

    public static function tableName()
    {
        return 'recipes_to_cats';
    }

    public static function count($cat_id) {
    	return RecipesToCats::find()->select('COUNT(recipe_cat_id) AS `count`')->where(['recipe_cat_id'=>$cat_id])->one();
    }
}

?>