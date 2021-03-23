<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Language extends ActiveRecord
{
    public function getImagePath() {
        return 'uploads/settings/';
    }

    public static function tableName()
    {
        return 'language';
    }

    public function attributeLabels() {
        return \Yii::t('app', 'languageLabels');
    }

    public function rules()
    {
        return [
            [['id'], 'safe', 'on'=>'search'],
            [['id', 'name', 'currency'], 'string']
        ];
    }

    public static function getAll() {
        return Language::find()->all();
    }
}
