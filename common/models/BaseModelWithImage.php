<?
namespace common\models;

use Yii;
use yii\helpers\Url;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

class BaseModelWithImage extends ActiveRecord
{
    public function getImagePath() {
        return 'uploads/';
    }

    public function imageUrl() {
    	if ($this->image)
        	return Url::base(true).'/'.$this->imagePath.$this->image;
        else return false;
    }
}