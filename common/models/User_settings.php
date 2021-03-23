<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User_settings extends BaseModelWithImage
{
    public function getImagePath() {
        return 'uploads/settings/';
    }

    public static function tableName()
    {
        return 'user_settings';
    }

    public function attributeLabels() {
        return \Yii::t('app', 'settingsLabels');
    }

    public function rules()
    {
        return [
            [['user_id'], 'safe', 'on'=>'search'],
            [['image', 'address', 'lang', 'birthday', 'phone', 'geolocation'], 'string'],
            [['city_id'], 'integer']
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
        //return User::find()->where(['id'=>$this->user_id])->one();
    }

    public function relations()
    {
       return array(
            'user'=>array(self::HAS_ONE, 'User', 'user_id')
        );
    }
}
