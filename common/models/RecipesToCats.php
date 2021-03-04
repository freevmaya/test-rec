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
}

?>