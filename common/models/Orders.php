<?
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Orders extends ActiveRecord
{

    const STATE_NEW = 0;
    const STATE_PROCESS = 1;
    const STATE_ACCEPTED = 10;
    const STATE_REJECTED = 11;
    const STATE_REMOVED = 12;

    public static function tableName()
    {
        return 'orders';
    }

    public function rules()
    {
        return [
            [['id', 'user_id'], 'safe', 'on'=>'search'],
            [['date', 'time'], 'string'],
            [['state'], 'integer']
        ];
    }

    public function relations()
    {
       return array(
            'items'=>array(self::HAS_MANY, 'OrderItems', 'order_id'),
            'user'=>array(self::HAS_ONE, 'User', 'user_id')
        );
    }

    public function getItems()
    {
        return $this->hasMany(OrderItems::className(), ['order_id'=>'id']);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }

    public function assignBasket() {
        if ($this->id) {
            $basket = Basket::getAll();
            $items = '';
            foreach ($basket as $item) {
                $items .= ($items ? ', ': '')."($this->id, $item->recipe_id, $item->count)";
            }

            Yii::$app->db->createCommand('INSERT INTO '.OrderItems::tableName().' VALUES '.$items)->execute();
            Basket::clearAll();
        }
    }

    public static function countAll() {
        return Yii::$app->db->createCommand(
            'SELECT count(id) FROM '.self::tableName().' WHERE user_id='.\Yii::$app->user->id.
            ' AND state < '.self::STATE_ACCEPTED
        )->queryScalar();
    }
}

?>