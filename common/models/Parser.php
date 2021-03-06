<?
namespace common\models;

use yii;
use yii\base\Model;
use common\helpers\Utils;
use common\models\RecipesCats;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use yii\db\ActiveRecord;

class Parser extends ActiveRecord
{
    private $crawler;
    private $_resultArray;

    private static $refreshPeriod = 3 * 24 * 60 * 60; // Трое суток
    private static $maxRefreshCount = 2;
    private static $resfreshIteration;
    private static $schemes = [];

    public static $States = ['active', 'processed', 'archived', 'removed', 'deferred'];

    public static function tableName()
    {
        return 'parser';
    }

    public function attributeLabels() {
        return [
            'url'=>'Url'
        ];
    }

    public function rules()
    {
        return [
            [['url', 'version', 'scheme', 'result'], 'required'],
            [['id', 'last', 'scheme', 'result'], 'string'],
            [['pid', 'version'], 'integer']
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->last = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    public function setUrl($value) {
        $this->id = md5($value);
        $this->_url = $value;
        $this->last = date('Y-m-d H:i:s');
    }

    public function getUrl() {
        return $this->_url;
    }

    public static function getScheme($name) {

        if (isset(Parser::$schemes[$name])) 
            return Parser::$schemes[$name];
        
        $fileName = dirname(__FILE__).'/schemes/'.$name.'.json';

        if (file_exists($fileName)) {
            $data = json_decode(file_get_contents($fileName));
            Parser::$schemes[$name] = $data;
            return $data;
        } else return null;
    }

    public static function parseBegin($url, $scheme, $refreshRequire = false) {
        Parser::$resfreshIteration = 0;
        return Parser::parseNext($url, $scheme, $refreshRequire);
    }

    private static function ParseCatTree($data) {
        $group = [];
        $cur_group = '';
        foreach ($data['value'] as $item) {
            $a = [];
            preg_match_all("/([\w-]{3,})\//", $item[0], $a);
            $r = $a[1];
            if (count($r) == 2) {
                $cur_group = $item[1];
            } else if (count($r) == 3) {
                if (!isset($group[$cur_group])) $group[$cur_group] = [];
                $group[$cur_group][] = $item[1];
            }
        }

        RecipesCats::refreshTree($group);
    }

    private static function ParseLinks($data, $refreshRequire = false) {
        $passed = [];
        foreach ($data['value'] as $relativeUrl) {
            if ($result = Parser::parseNext($data['parser']['baseUrl'].$relativeUrl, $data['parser']['scheme'], $refreshRequire)) 
                $passed[] = $result;
        }

        return $passed;
    }

    private static function checkParserData($item, $refreshRequire = false) {
        $passed = [];
        if (is_array($item)) {
            if (isset($item['parser'])) {
                if (isset($item['parser']['url'])) {
                    $comm = "\$item_url = {$item['parser']['url']};";
                    eval($comm);
                    if ($item_url) {
                        if (is_array($item_url)) {
                            foreach ($item_url as $url) {
                                if ($result = Parser::parseNext($url, $item['parser']['scheme'], $refreshRequire))
                                    $passed[] = $result;
                            }
                        } else {
                            if ($result = Parser::parseNext($item_url, $item['parser']['scheme'], $refreshRequire))
                                $passed[] = $result;
                        }
                    }
                    else {
                        //print_r($item);
                        \Yii::error("Invalid expression '{$comm}'");
                    }
                } else if (isset($item['parser']['method'])) {
                    $method = $item['parser']['method'];
                    if ($result = Parser::$method($item))
                        $passed = array_merge($passed, $result);
                }
            } else {
                foreach ($item as $data) {
                    $passed = array_merge($passed, Parser::checkParserData($data, $refreshRequire));
                    /*
                    if (is_array($data) && (isset($data['parser']))) {
                        $comm = "\$item_url = {$data['parser']['url']};";
                        eval($comm);
                        if ($item_url) {
                            if (is_array($item_url)) {
                                foreach ($item_url as $url) 
                                    Parser::parseNext($url, $data['parser']['scheme']);
                            } else Parser::parseNext($item_url, $data['parser']['scheme']);
                        }
                        else \Yii::error("Invalid expression '{$comm}'");
                    }
                    */
                }
            }
        }

        return $passed;
    }

    private static function parseChilds($result, $refreshRequire) {
        $childs = [];
        foreach ($result as $field=>$item) {
            if (is_array($item)) {
                if (isset($item[0])) {
                    for ($i=0; $i<count($item); $i++) {
                        if (is_array($item[$i]))
                            foreach ($item[$i] as $elem) {
                                $childs = array_merge($childs, Parser::checkParserData([$elem], $refreshRequire));
                            }
                    }
                } else $childs = array_merge($childs, Parser::checkParserData($item, $refreshRequire));
            }
        }

        if (count($childs) > 0) return $childs;
        return null;
    }

    public static function parseRefresh($scheme, $limit = 5) {
        $passed = [];

        Parser::$resfreshIteration = 0;
        Parser::$maxRefreshCount = $limit;

        if ($schemeData = Parser::getScheme($scheme)) {
            $list = Parser::find()->where("scheme = '{$scheme}' AND version != {$schemeData->version}")->all();

            foreach ($list as $parser) {
                if (Parser::$resfreshIteration <= Parser::$maxRefreshCount) {
                    if ($result = $parser->parse()) {
                        Parser::$resfreshIteration++;

                        $parser->state = 'active';
                        $parser->save();

                        $passed_item = ['refresh', $scheme, $parser->pid];                        
                        if ($childs = Parser::parseChilds($result, true))
                            $passed_item['childs'] = $childs;

                        $passed[] = $passed_item;
                    }
                } else break;
            }
        }

        return $passed;
    }

    private static function parseNext($url, $scheme, $refreshRequire = false) {
        if ($schemeData = Parser::getScheme($scheme)) {
            //echo "Parse $url, $scheme\n";
            $id = md5($url.$scheme.'1');
            $now = strtotime("now");
            $result = false;
            $passed = null;

            if (!($model = Parser::findOne(['id'=>$id]))) {
                if (Parser::$resfreshIteration <= Parser::$maxRefreshCount) {
                    $model = new Parser();
                    $model->_url = $url;
                    $model->scheme = $scheme;
                    $model->id = $id;
                    $model->version = $schemeData->version;
                    if ($result = $model->parse()) {

                        Yii::trace($result, 'user');
                        Parser::$resfreshIteration++;
                        $model->save();
                        $passed = ['new', $scheme, $model->pid];
                    } else return;
                } else return;
            } else {
                if ($refreshRequire) {
                    if (Parser::$resfreshIteration <= Parser::$maxRefreshCount) {
                        if ($result = $model->parse()) {
                            Parser::$resfreshIteration++;
                            if ($refreshRequire)
                                $model->state = 'active';

                            $model->save();
                            $passed= ['user refresh', $scheme, $model->pid];
                        } else return;
                    } else return;
                } else  {
                    if ($model->version != $schemeData->version) { // Обновляем если версия структуры отличается
                        if (Parser::$resfreshIteration <= Parser::$maxRefreshCount) {
                            if ($result = $model->parse()) {
                                Parser::$resfreshIteration++;
                                $model->scheme = $scheme;
                                $model->version = $schemeData->version;                        
                                $model->state = 'active';
                                $model->save();
                                $passed = ['change version', $scheme, $model->pid];
                            } else return;
                        } else return;
                    } else {
                        $result = json_decode($model->result, true);
                        $passed = ['pass', $scheme, $model->pid];
                    }

                    /* Пока не обновлять существующие записи
                    if ($model->state == 'active') {
                        if ($now <= strtotime($model->last) + Parser::$refreshPeriod)
                            $result = json_decode($model->result, true);
                        else if (Parser::$resfreshIteration < Parser::$maxRefreshCount) {
                            Parser::$resfreshIteration++;
                            if ($result = $model->parse()) {
                                $model->save();
                                Parser::$passed[] = $model->pid;
                            }
                        } else return $model;
                    }
                    */
                }
            }

            if ($result) {

                //Заменить на parseChilds!!!!!!!!!!!!
                $childs = [];
                foreach ($result as $field=>$item) {
                    if (is_array($item)) {
                        if (isset($item[0])) {
                            for ($i=0; $i<count($item); $i++) {
                                if (is_array($item[$i]))
                                    foreach ($item[$i] as $elem) {
                                        $childs = array_merge($childs, Parser::checkParserData([$elem], $refreshRequire));
                                    }
                            }
                        } else $childs = array_merge($childs, Parser::checkParserData($item, $refreshRequire));
                    }
                }

                if (count($childs) > 0) {
                    //print_r($childs);
                    $passed['childs'] = $childs;
                    return $passed;
                }

            } else \Yii::error("Empty result url: {$url}, scheme: {$scheme}, version: {$schemeData->version}");

        } else \Yii::error("schema {$scheme} not found");

        return $passed;
    }

    protected function getValue($data, $item) {
        if (is_object($item)) {
            if (isset($item->attr)) 
                return $data->attr($item->attr);
            else if (isset($item->items))
                return $this->parseNode($data, $item); 
            else if (isset($item->eval)) {
                $cmd = '$result = '.$item->eval.';';
                eval($cmd);
                return $result; 
            }
            else return [0=>$data->text()];
        } else return $data->text();
    }

    protected function parseNode($nodes, $node) {
        $result = [];

        if (isset($node->fields)) {
            foreach ($node->fields as $field=>$item) {

                if (is_string($item))
                    $data = $nodes->filter($item);
                else if ($item->selector)
                    $data = $nodes->filter($item->selector);
                else $data = $nodes;

                $lresult = null;

                if ($data->count() == 1) {
                    $lresult = $this->getValue($data, $item);
                } else if ($data->count() > 1) {
                    $list = [];
                    $data->each(function ($child) use (&$list, &$item) {
                        $list[] = $this->getValue($child, $item);
                    });
                    $lresult = $list;
                }


                if (isset($item->parser)) {
                    $result[$field] = [
                        'value'=>$lresult,
                        'parser'=>(array)$item->parser
                    ];
                } else $result[$field] = $lresult;
            }
        } else {
            if (isset($node->items)) {
                $This = $this;
                if (isset($node->items->selector)) 
                    $nodes = $nodes->filter($node->items->selector);

                $nodes->each(function($child) use (&$This, &$result, &$node) {
                    $result[] = $This->parseNode($child, $node->items);
                });
            } else {
                $lresult = $this->getValue($nodes, $node);
                if (isset($node->parser)) {
                    $result[] = [
                        'value'=>$lresult,
                        'parser'=>(array)$node->parser
                    ];
                } else $result[] = $lresult;
            }
        }

        return $result;
    }

    public function parse() {
        if ($this->scheme) {

            $scheme = Parser::getScheme($this->scheme);

            if ($scheme) {
                $this->getCrawler();
                $this->version = intval($scheme->version);
                $this->_resultArray = $this->parseNode($this->crawler, $scheme->body);
                $this->result = json_encode($this->_resultArray, JSON_UNESCAPED_UNICODE);
            }
        }

        return $this->_resultArray;
    }

    public function getResultArray() {
        return $this->_resultArray;
    }

    public function getCrawler() {
        if (!$this->crawler && $this->_url)
            $this->crawler = Parser::createCrawler($this->_url);

        return $this->crawler;
    }

    public static function createCrawler($url) {

        $cache = \Yii::$app->cache;
        $cachkey = md5($url.'2');

        if ($content = $cache->get($cachkey))
            $crawler = new Crawler($content, '', $url);
        else {
            $client = new Client();
            $crawler = $client->request('GET', $url);
            $cache->set($cachkey, $crawler->html());
        }

        return $crawler;
    }
}

?>