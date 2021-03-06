<?
namespace common\models;

use Yii;
use yii\helpers\Url;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

class Stages extends BaseModelWithImage
{

    public static function tableName()
    {
        return 'stages';
    }

    public function rules()
    {
        return [
            [['id', 'recipe_id'], 'safe', 'on'=>'search'],
            [['name', 'image', 'text'], 'string'],
            [['id', 'recipe_id'], 'integer']
        ];
    } 

    public function attributeLabels() {
        return [
            'name'=>\Yii::t('app', 'name'),
            'text'=>\Yii::t('app', 'description'),
            'image'=>\Yii::t('app', 'image')
        ];
    }

    public function getImagePath() {
        return 'uploads/stages/';
    }

    public static function parseName(&$text) {
        $name = null;
        $pos = mb_strpos($text, ':');
        if (($pos !== false) && ($pos < 128)) {
            $name = mb_substr($text, 0, $pos - 1);
            $text = mb_substr($text, $pos + 1);
        }
        return $name;
    }
}

?>