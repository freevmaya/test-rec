<?
namespace common\models;

use yii\db\ActiveRecord;

class City extends ActiveRecord
{

    public static function tableName()
    {
        return 'city';
    }

    public function rules()
    {
        return [
            [['id'], 'safe', 'on'=>'search'],
            [['region_id', 'area_id', 'time_zone','publish'], 'integer'],
            [['name', 'name_english', 'name_rod'], 'string']
        ];
    }

    public static function byId($id) {
        return City::find()->where(['id'=>$id])->one();
    }

    public static function getAll() {
        return City::find()->where(['publish'=>1])->all();
    }
}

?>