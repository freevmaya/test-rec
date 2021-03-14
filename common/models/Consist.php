<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use common\helpers\Utils;
use common\models\Units;
use yii\helpers\StringHelper;

class Consist extends ActiveRecord
{

    public static function tableName()
    {
        return 'consist';
    }

    public function rules()
    {
        return [
            [['recipe_id', 'consist_id'], 'safe', 'on'=>'search']
        ];
    }

    public static function check($consist) {
        $result = [];
        foreach ($consist as $conName) {
            if ($con = Consist::find()->where(['name'=>$conName])->one())
                $result[] = $con->id;
            else {
                $con = new Consist();
                $con->name = $conName;
                $con->save();
                $result[] = $con->id;
            }
        }

        return $result;
    }

/*
    public function relations()
    {
       return array(
            'unit'=>array(self::BELONGS_TO, 'Units', 'unit_id')
        );
    }*/
}

?>