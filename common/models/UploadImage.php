<?php

namespace common\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadImage extends Model {

	public function rules(){
		return[
			[['image'], 'file', 'extensions' => 'png, jpg'],
		]; 
	}

    public function attributeLabels() {
    	return [
            'image'=>\Yii::t('app', 'image')
    	];
	}

	public static function upload($model, $field){
	    if ($image = UploadedFile::getInstance($model, $field)) {
			if ($image) {
				$image->saveAs("uploads/{$image->baseName}.{$image->extension}");
		    	$model->$field = $image->name;
			} else {
				return false;
			}
		}
	}
}