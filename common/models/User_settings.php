<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User_settings extends BaseModelWithImage
{

    const KMTOLAT = 1 / 111.134861111;
    const KMTOLON = 1 / 111.321377778;

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
            [['image', 'address', 'lang', 'birthday', 'phone', 'geoaddress'], 'string'],
            [['lon', 'lat'], 'number'],
            [['city_id', 'int_lon', 'int_lat', 'timezone', 'finddistance'], 'integer']
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }

    public function setLocation($location) {
        if (is_string($location))
            $location = json_decode($location);

        if (isset($location['lat'])) {
            $this->lat = doubleval($location['lat']);
            $this->lon = doubleval($location['lng']);
        } else if (isset($location['latitude'])) {
            $this->lat = doubleval($location['latitude']);
            $this->lon = doubleval($location['longitude']);
        } else if (isset($location->lat)) {
            $this->lat = doubleval($location->lat);
            $this->lon = doubleval($location->lng);
        } else if (isset($location->latitude)) {
            $this->lat = doubleval($location->latitude);
            $this->lon = doubleval($location->longitude);
        }

        $this->int_lat = round($this->lat);
        $this->int_lon = round($this->lon);

        $this->refreshDistances();
    }

    public function refreshDistances() {

        $isPartner = $this->user->role == 'partner';

        $users = User::find()->where("id != {$this->user->id} AND status=".User::STATUS_ACTIVE." AND role ".($isPartner ? '!=' : '=')." 'partner'")->all();

        $values = '';
        $count = 0;
        foreach ($users as $user) 
            if ($user->settings && $user->settings->lat) {
                $distance = $this->calcDistance($user->settings);
                $value = "({$this->user_id}, {$user->id}, {$distance})";
                $values .= ($values ? ', ' : '').$value;
                $count++;
            }

        if ($count > 0) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($isPartner) {
                    $field = 'partner_id';
                    $fields = '`partner_id`, `user_id`, `distance`';
                } else {
                    $field = 'user_id';
                    $fields = '`user_id`, `partner_id`, `distance`';
                }
                Yii::$app->db->createCommand('DELETE FROM '.Distances::tableName()." WHERE {$field}={$this->user_id}")->execute();
                Yii::$app->db->createCommand('INSERT INTO '.Distances::tableName()." ({$fields}) VALUES {$values}")->execute();
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }

    public function relations()
    {
       return array(
            'user'=>array(self::HAS_ONE, 'User', 'user_id'),
            'city'=>array(self::HAS_ONE, 'City', 'city_id'),
            'language'=>array(self::HAS_ONE, 'Language', 'lang')
        );
    }

    public function getLanguage() {
        return $this->hasOne(Language::className(), ['id'=>'lang']);
        //return User::find()->where(['id'=>$this->user_id])->one();
    }

    public static function Self() {
        return \Yii::$app->user->identity->settings;
    }

    public function calcDistance($otherSettings) {
        if ($this->lat && $this->lon && $otherSettings->lat && $otherSettings->lon) {
            $dlat = ($this->lat - $otherSettings->lat) / self::KMTOLAT;
            $dlon = ($this->lon - $otherSettings->lon) / self::KMTOLAT;

            return sqrt($dlat * $dlat + $dlon * $dlon);
        }

        return 0;
    }
}
