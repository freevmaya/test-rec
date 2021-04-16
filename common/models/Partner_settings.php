<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Partner_settings extends BaseModelWithImage
{
    public function getImagePath() {
        return 'uploads/partner_settings/';
    }

    public static function tableName()
    {
        return 'partner_settings';
    }

    public function attributeLabels() {
        return \Yii::t('app', 'partner_settingsLabels');
    }

    public function rules()
    {
        return [
            [['user_id'], 'safe', 'on'=>'search'],
            [['email'], 'email'],
            [['phone', 'name', 'address', 'image'], 'string'],
            [['find_union', 'in_method', 'disabled'], 'boolean'],
            [['execMethods'], 'safe'],
            [['email', 'phone', 'name', 'address'], 'required']
        ];
    }

    public function relations()
    {
       return array(
            'user'=>array(self::HAS_ONE, 'User', 'user_id'),
            'language'=>array(self::HAS_ONE, 'Language', 'lang'),
        );
    }

    public function getExecMethodsArray() {
        return explode(',', $this->execMethods);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }

    public function getLanguage() {
        return $this->hasOne(Language::className(), ['id'=>'lang']);
    }

    public static function Self() {
        return \Yii::$app->user->identity->partner_settings;
    }
}
