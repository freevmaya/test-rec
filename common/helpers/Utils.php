<?
/**
 * @link https://vmaya.ru
 * @copyright Copyright (c) 2008 Vmaya Software
 * @license https://vmaya.ru
*/

namespace common\helpers;

/**
 * @author Vmaya <fwadim@mail.ru>
 * @since 1.0
 */
class Utils 
{
	public static $land;

	public static function getLang() {
		if (!Utils::$land) {
			$a = explode('-', \Yii::$app->language);
			Utils::$land = $a[0];
		};

		return Utils::$land;
	}

	public static function cook_time($str_value) {
		$a = explode(':', $str_value);

		$result = "";
		if (intval($a[0])) $result .= Utils::val_desc($a[0], \Yii::t('app', 'hour/hours/hours'));
		if ($a[1]) $result .= ($result?' ':'').Utils::val_desc($a[1], \Yii::t('app', 'minute/minutes/minutes'));
		if (intval($a[2])) $result .= ($result?' ':'').Utils::val_desc($a[2], \Yii::t('app', 'second/seconds/seconds'));

		return $result;
	}

	public static function val_desc($val, $descs) {
		if (!is_array($descs)) $descs = preg_split("/[|\/,]+/", $descs);

		$av = $val % 10;
		if ($av == 1) return $val.' '.$descs[0];
		else if ($av == 5) return $val.' '.$descs[1];

		return $val.' '.$descs[2];
	}

	public static function mb_ucfirst($string, $encoding = 'UTF-8'){
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);
		return mb_strtoupper($firstChar, $encoding) . $then;
	}


	public static function outCatItem($parent_id, $list, $level = 0) {
		$cat_id = \Yii::$app->request->get('cat_id');
		foreach ($list as $item) {
			$as_parent = false;
			if (!$parent_id && !$item->parent_id) {
				foreach ($list as $it)
					if ($it->parent_id == $item->id) $as_parent = true;
			}

			if ($as_parent || ($parent_id && ($item->parent_id == $parent_id))) {
				?>
				<div class="item">
					<div class="head">
						<a class="btn <?=($cat_id == $item->id)?'btn-primary':'btn-light'?>" type="button" href="<?=\yii\helpers\Url::toRoute(['/recipes', 'cat_id'=>$item->id])?>"><?=$item->name?></a>
					</div>
					<?
						if ($level < 1)
							Utils::outCatItem($item->id, $list, $level + 1);
					?>
				</div>
				<?
			}
		}
	}

	public static function upload($model, $field){
	    if ($image = \yii\web\UploadedFile::getInstance($model, $field)) {
			if ($image) {
				$image->saveAs($model->imagePath."/{$image->baseName}.{$image->extension}");
		    	return $model->$field = $image->name;
			}
		}
		return false;
	}
}
?>