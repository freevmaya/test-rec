<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class ConsistToRecipe extends ActiveRecord
{

    public static function tableName()
    {
        return 'consist_to_recipe';
    }
}

?>