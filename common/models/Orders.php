<?
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use common\helpers\Utils;

class Orders extends ActiveRecord
{

    const STATE_NEW = 0;
    const STATE_PROCESS = 1;
    const STATE_USER_REQUEST = 2;
    const STATE_PARTNER_FINISH = 3;
    const STATE_FINISH = 4;
    const STATE_ACCEPTED = 10;
    const STATE_REJECTED = 11;
    const STATE_REMOVED = 12;
    const STATE_CANCEL = 13;

    const EVENT_ORDER_SAVE = "ORDER_SAVE";


    const METHODS = [ 'Pickup', 'Courier', 'Delivery', 'PersonalVisit' ];

    public static function tableName()
    {
        return 'orders';
    }

    public function attributeLabels() {
        return \Yii::t('app', 'orderLabels');
    }

    public function rules()
    {
        return [
            [['id', 'user_id', 'exec_id'], 'safe', 'on'=>'search'],
            [['date', 'time'], 'string'],
            [['state'], 'integer']
        ];
    }

    public function relations()
    {
       return array(
            'items'=>array(self::HAS_MANY, 'OrderItems', 'order_id'),
            'user'=>array(self::HAS_ONE, 'User', 'user_id'),
            'executer'=>array(self::HAS_ONE, 'User', 'exec_id'),
            'settings'=>array(self::HAS_ONE, 'User_settings', 'user_id'),
            'mainmenu'=>array(self::HAS_ONE, 'Mainmenu', 'user_id'),
        );
    }

    public function getItems()
    {
        return $this->hasMany(OrderItems::className(), ['order_id'=>'id']);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }

    public function getExecuter() {
        return $this->hasOne(User::className(), ['id'=>'exec_id']);
    }

    public function getSettings() {
        return $this->hasOne(User_settings::className(), ['user_id'=>'user_id']);
    }

    public function getMainmenu() {
        return $this->hasOne(Mainmenu::className(), ['user_id'=>'user_id']);
    }

    public function getExecMethodList() {
        $list = explode(',', $this->execMethod);
        $langList = Utils::t('execMethods');
        $result = [];
        foreach ($list as $key) {
            $result[] = $langList[$key];
        }
        return $result;
    }

    public function assignBasket() {
        if ($this->id) {
            $basket = Basket::getAll();
            $items = '';
            foreach ($basket as $item) {
                $items .= ($items ? ', ': '')."($this->id, $item->recipe_id, $item->count)";
            }

            Yii::$app->db->createCommand('INSERT INTO '.OrderItems::tableName().' VALUES '.$items)->execute();
            Basket::clearAll();
        }
    }

    public function getOrderDate() {
        $date = date('d.m.Y', strtotime($this->date)).' '.date('H:i', strtotime($this->time));

        $now = new \DateTime();
        $interval = $now->diff(\DateTime::createFromFormat('d.m.Y H:i', $date));

        if ($interval->days < 1) 
            if ($interval->h < 1) $date = Utils::val_desc($interval->i, \Yii::t('app', 'minute/minutes/minutes')).' '.Utils::t('ago');
            else $date = Utils::val_desc($interval->h, \Yii::t('app', 'hour/hours/hours')).' '.Utils::t('ago');
        else if ($interval->days < 3) 
            $date = Utils::val_desc($interval->days, \Yii::t('app', 'day/days/days')).' '.Utils::t('ago');
        else $date = Utils::dateToUserTimeZone($date);

        return $date;
    }

    public function afterSave ($insert, $changedAttributes) {
        $result = parent::afterSave($insert, $changedAttributes);
        Yii::$app->trigger('Orders.afterSave', new Event(['sender' => $this]));
        return $result;
    }

    public function getExecuterRate() {
        return Yii::$app->db->createCommand(
            'SELECT sum(value)/count(order_id) AS value FROM '.UserRates::tableName().' WHERE order_id='.$this->id
        )->queryScalar();
    }

    public static function countAll() {
        return Yii::$app->db->createCommand(
            'SELECT count(id) FROM '.self::tableName().' WHERE user_id='.\Yii::$app->user->id.
            ' AND state < '.self::STATE_ACCEPTED
        )->queryScalar();
    }

    public static function findOrdersQuery($user) {

        $set = $user->settings;
        $pset = $user->partner_settings;

        $limitsLat = [$set->lat - $set->finddistance * User_settings::KMTOLAT, $set->lat + $set->finddistance * User_settings::KMTOLAT];
        $limitsLon = [$set->lon - $set->finddistance * User_settings::KMTOLON, $set->lon + $set->finddistance * User_settings::KMTOLON];

        $where_union = ' AND exec_id IS NULL AND state = '.Orders::STATE_NEW;//.' OR (state ='.Order::STATE_PROCESS.' AND )';
        if ($pset->find_union) {

            $ids = Mainmenu::OrderIds($user->id);

            if (count($ids) > 0)
                $where_union .= ' AND id IN ('.implode(',', $ids).')';
        }

        if ($pset->in_method) {
            $mwhere = '';
            foreach ($pset->execMethodsArray as $method) {
                $mwhere .= ($mwhere ? ' OR ' : '')."execMethod LIKE '%{$method}%'";
            }

            if ($mwhere) $where_union .= " AND ($mwhere)";
        }

        $query = Orders::find()->innerjoinWith(['settings'])->where(['state'=>Orders::STATE_NEW]);


        $query->where("(lat BETWEEN {$limitsLat[0]} AND {$limitsLat[1]}) AND ".
                    "(lon BETWEEN {$limitsLon[0]} AND {$limitsLon[1]}) $where_union");

        $query->orderBy("date DESC");

         return $query;
    }


    public static function setStates($ids, $state) {
        $result = [];
        $user_id = \Yii::$app->user->id;
        if (User::isPartner()) 
            $where_user = "((exec_id IS NULL) OR (exec_id={$user_id}))";
        else $where_user = "(user_id={$user_id})";

        foreach ($ids as $id) {
            if ($order = Orders::find()->where("id={$id} AND {$where_user}")->one()) {
                $order->state = $state;

                if (User::isPartner()) 
                    $order->exec_id = ($state == Orders::STATE_NEW) ? null : \Yii::$app->user->id;

                if ($order->save())
                    $result[] = $id;
            }
        }
        return $result;
    }
}

?>