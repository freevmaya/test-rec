<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use common\helpers\Utils;
use common\models\Units;
use yii\helpers\StringHelper;

class Distances extends ActiveRecord
{

    const DEFAULT_DISTANCE = 100;

    public static function tableName()
    {
        return 'distances';
    }

    public function rules()
    {
        return [
            [['partner_id', 'user_id'], 'safe', 'on'=>'search'],
            [['distance'], 'integer']
        ];
    }
}

?>