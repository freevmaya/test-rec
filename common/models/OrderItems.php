<?
namespace common\models;

use yii\db\ActiveRecord;

class OrderItems extends ActiveRecord
{

    public static function tableName()
    {
        return 'order_items';
    }

    public function rules()
    {
        return [
            [['order_id', 'recipe_id'], 'safe', 'on'=>'search'],
            [['count'], 'integer']
        ];
    }

    public function relations()
    {
       return array(
            'order'=>array(self::HAS_ONE, 'Orders', 'order_id'),
            'recipe'=>array(self::HAS_ONE, 'Recipes', 'recipe_id'),
        );
    }

    public function getOrder() {
        return $this->hasOne(Orders::className(), ['id'=>'order_id']);
    }

    public function getRecipe() {
        return $this->hasOne(Recipes::className(), ['id'=>'recipe_id']);
    }
}

?>