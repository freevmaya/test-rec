<?
namespace common\models;

use yii\db\ActiveRecord;
use common\helpers\Utils;

class Units extends ActiveRecord
{

    public static function tableName()
    {
        return 'units';
    }

    public function rules()
    {
        return [
            [['id', 'short'], 'safe', 'on'=>'search'],
            [['name', 'short'], 'string']
        ];
    }

    public static function getAll() {

        return Units::find()->
            select(['id', 'name', 'short'])->where("lang_id='".Utils::getLang()."'")->
        asArray()->all();
    }
}

?>