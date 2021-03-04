<?
namespace common\models;

use yii\db\ActiveRecord;

class Rates extends ActiveRecord
{

    public static function tableName()
    {
        return 'recipes_rates';
    }

    public function rules()
    {
        return [
            [['recipe_id', 'user_id'], 'safe', 'on'=>'search'],
            [['value', 'recipe_id'], 'integer']
        ];
    }

    public static function avg($recipe_id) {
        $query = (new \yii\db\Query())
                ->select('SUM(value)/COUNT(recipe_id) AS rate')
                ->from(Rates::tableName())
                ->where("recipe_id=".$recipe_id);
        return $query->one()['rate'];
    }
}

?>