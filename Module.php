<?php
/*
 * This file is part of the YiiModules.com
 *
 * (c) Yii2 modules open source project are hosted on <http://github.com/yiimodules/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace yiimodules\categories;
use yii\helpers\Url;
/**
 * categories module definition class
 */
class Module extends \yii\base\Module
{
	//Path where category images will be uploaded
	public $uploadDir = '@webroot/uploads/category-images';
	public $uploadUrl = '@web/uploads/category-images';
	/*
	Array of Image thumb sizes will create different image version after resizing
	Thumb directory will be "{uploadPath}/{thumb name}
	*/
	public $uploadThumbs = ["small" => [80,80],"medium" => [120,120]];
	
	public $assets = "";

     /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setAliases([
            '@categories-assets' => __DIR__ . '/assets'
        ]);
        // custom initialization code goes here
    }
	
	public function getAll($parent_id=NULL){
		$data = models\Categories::find()->where(['parent_id'=>$parent_id])->all();
		$arr = array();
		foreach($data as $data){
			$catData = $data->attributes;
			$uploadThumbs = $this->uploadThumbs;
			foreach($uploadThumbs as $thumbType=>$sizes){
				if(empty($catData['image'])){
					$catData['image_'.$thumbType] =  false;
				} else{
					$catData['image_'.$thumbType] =  Url::to($this->uploadUrl.'/'.$thumbType.'/'.$catData['image']);
				}
			}
			$countChilds = models\Categories::find()->where(['parent_id'=>$data->id])->count();
			if($countChilds>0){
				$catData['sub_categories'] = $this->getAll($parent_id=$data->id);
			}
			$arr[] = $catData;
		}
		return $arr;
	}
	
	public function getOne($category_id=Null){
		$data = models\Categories::find()->where(['id'=>$category_id])->one();
		if($data===null){ return false; }
		$catData = $data->attributes;
		$uploadThumbs = $this->uploadThumbs;
		foreach($uploadThumbs as $thumbType=>$sizes){
			if(empty($catData['image'])){
				$catData['image_'.$thumbType] =  false;
			} else{
				$catData['image_'.$thumbType] =  Url::to($this->uploadUrl.'/'.$thumbType.'/'.$catData['image']);
			}
		}
		$countChilds = models\Categories::find()->where(['parent_id'=>$data->id])->count();
		if($countChilds>0){
			$catData['sub_categories'] = $this->getAll($parent_id=$data->id);
		}
		$arr[] = $catData;
		return $arr;
	}
	
}
