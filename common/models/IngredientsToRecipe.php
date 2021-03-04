<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class IngredientsToRecipe extends ActiveRecord
{

    public static function tableName()
    {
        return 'ingredients_to_recipe';
    }
}

?>