<?
namespace common\models;

use yii\db\ActiveRecord;
use yii\base\Event;

class UserRates extends ActiveRecord
{

    public static function tableName()
    {
        return 'user_rates';
    }

    public function rules()
    {
        return [
            [['user_id', 'author_id', 'order_id'], 'safe', 'on'=>'search'],
            [['value', 'user_id', 'author_id', 'order_id'], 'integer']
        ];
    }

    public static function avg($user_id) {
        $query = (new \yii\db\Query())
                ->select('SUM(value)/COUNT(user_id) AS rate')
                ->from(UserRates::tableName())
                ->where("user_id=".$user_id);
        return $query->one()['rate'];
    }

    public function afterSave ($insert, $changedAttributes) {
        $result = parent::afterSave($insert, $changedAttributes);
        \Yii::$app->trigger('UserRates.afterSave', new Event(['sender' => $this]));
        return $result;
    }
}

?>