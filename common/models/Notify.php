<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use common\helpers\Utils;
use common\models\Units;
use yii\helpers\StringHelper;

class Notify extends ActiveRecord
{

    public static function tableName()
    {
        return 'notify';
    }

    public function rules()
    {
        return [
            [['id', 'user_id'], 'safe', 'on'=>'search'],
            [['time'], 'datetime'],
            [['subject', 'message', 'params'], 'string']
        ];
    }

    public function relations()
    {
       return array(
            'user'=>array(self::BELONGS_TO, 'User', 'user_id')
        );
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

?>