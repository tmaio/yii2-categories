<?php
/*
 * This file is part of the YiiModules.com
 *
 * (c) Yii2 modules open source project are hosted on <http://github.com/yiimodules/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace yiimodules\categories\models;
use Yii;
use yii\imagine\Image;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\helpers\Inflector;
use yii\helpers\Html;
/**
 * This is the model class for table "ymd_categories".
 *
 * @property string $id
 * @property string $parent_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $image
 * @property string $meta_title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $position
 * @property integer $is_active
 * @property integer $created_at
 * @property integer $updated_at
 */
class Categories extends \yii\db\ActiveRecord
{

    private $_fileName;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ymd_categories';
    }
	
	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					\yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
					\yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
				],
			],
		];
	}	

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'position', 'is_active', 'created_at', 'updated_at'], 'integer'],
            [['name', 'slug'], 'required'],
            [['description'], 'string'],
            [['name', 'slug'], 'string', 'max' => 45],
            [['meta_title'], 'string', 'max' => 80],
            [['meta_keywords'], 'string', 'max' => 150],
            [['meta_description'], 'string', 'max' => 255],
			[['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent category',
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'image' => 'Category Image',
            'meta_title' => 'Meta Title',
            'meta_keywords' => 'Meta Keywords',
            'meta_description' => 'Meta Description',
            'position' => 'Position',
            'is_active' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
	
	public function getParentCategory()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }	
	
	
	public function upload()
    {
        if($this->validate()){
			
			//Skip on empty
			if($this->image==null){
				return true;
			}
		
			$uploadDir = Yii::$app->getModule('categories')->uploadDir;
			$uploadThumbs = Yii::$app->getModule('categories')->uploadThumbs;
			
			//Create Dir if not exist
			$originalPath = Yii::getAlias($uploadDir) . DIRECTORY_SEPARATOR . 'original';
			if(!file_exists($originalPath)){    
				if (!FileHelper::createDirectory($originalPath, 0775,$recursive = true )) {
					throw new InvalidConfigException('category image upload directory does not exist and default path creation failed');
				}
			}
			
			//Save original file
			$this->image->saveAs($originalPath . DIRECTORY_SEPARATOR . $this->getFileName());
			
			//Save thumbs
			foreach($uploadThumbs as $thumbDirName=>$size){
				$thumbPath = Yii::getAlias($uploadDir) . DIRECTORY_SEPARATOR . $thumbDirName;
				if(!file_exists($thumbPath)){    
					if (!FileHelper::createDirectory($thumbPath, 0775,$recursive = true )) {
						throw new InvalidConfigException($thumbPath. 'category thumb path creation failed');
					}
				}
				Image::thumbnail(Yii::getAlias($originalPath . DIRECTORY_SEPARATOR . $this->getFileName()),$size[0],$size[1])
				->save(Yii::getAlias($thumbPath . DIRECTORY_SEPARATOR . $this->getFileName()), ['quality' => 90]);
			}
			$this->image = $this->getFileName();
			$this->save(false);
            return true;
        } else {
            return false;
        }
    }

    public function getFileName()
    {
        if (!$this->_fileName) {
            $fileName = substr(uniqid(md5(rand()), true), 0, 10);
            $fileName .= '-' . Inflector::slug($this->image->baseName);
            $fileName .= '.' . $this->image->extension;
            $this->_fileName = $fileName;
        }
        return $this->_fileName;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->image = UploadedFile::getInstance($this,'image');
            return true;
        }
        return false;
    }
	
	public static function createTreeList($parent_id,$current_category){
		$categories = self::find()->where(['parent_id'=>$parent_id])->all();
		$parent_id = Yii::$app->request->getQueryParam('parent_id');
		$html = "";
		$html .= "<ul>";
		foreach($categories as $category){
			$html .= "<li>";
			$class = (($category->id==$parent_id) || ($category->id==$current_category)) ? "jstree-clicked" : "";
			$url = Yii::$app->urlManager->createUrl(['categories','id'=>$category->id]);
			$html .= "<a class=\"".$class."\" href=\"".$url."\">";
			$html .= Html::getAttributeValue($category,'name');
			$html .= "</a>";
				$childCount = self::find()->where(['parent_id'=>$category->id])->count();
				if($childCount>0){
					$html .= self::createTreeList($category->id,$current_category);
				}
			$html .= "</li>";
		}
		$html .= "</ul>";
		return $html;
	}
	
	public static function printEditPath($categoryId,$html=""){
		$category= self::find()->where(['id'=>$categoryId])->one();
		if($category->parent_id!=NULL){
			$html .= self::printEditPath($category->parent_id,$html);
		}
		$html .= "<a href=\"".Yii::$app->urlManager->createUrl(['/categories','id'=>$category->id])."\">".$category->name."</a> &nbsp;/ ";
		return $html;
	}
	
	public static function getImage($categoryId,$type,$htmlOptions=[]){
	
		$uploadThumbs = Yii::$app->getModule('categories')->uploadThumbs;
		$uploadUrl = Yii::$app->getModule('categories')->uploadUrl;
		$model = self::find()->where(['id'=>$categoryId])->one();

		$htmlOptionsDefault = ['alt'=>$model->name,'title'=>$model->name];
		
		if(isset($uploadThumbs[$type][0])){
			$htmlOptionsDefault['width'] = $uploadThumbs[$type][0]."px";
		}
		if(isset($uploadThumbs[$type][1])){
			$htmlOptionsDefault['height'] = $uploadThumbs[$type][1]."px";
		}
		
		$htmlOptions = array_merge($htmlOptionsDefault,$htmlOptions);
		
		if (empty($model->image)) {
			$image = null;
			$image = Html::img(Yii::$app->getModule('categories')->assets->baseUrl.'/no-image-found.jpg',$htmlOptions);
		}
		else {
			$image = Html::img($uploadUrl.'/'.$type.'/'.$model->image,$htmlOptions);
		}
		return $image; 
	}
	
	public static function deleteImages($categoryId){
		$uploadDir = Yii::$app->getModule('categories')->uploadDir;
		$uploadThumbs = Yii::$app->getModule('categories')->uploadThumbs;
		$model = self::find()->where(['id'=>$categoryId])->one();
		
		foreach($uploadThumbs as $thumbDirName=>$size){
			$thumbPath = Yii::getAlias($uploadDir) . DIRECTORY_SEPARATOR . $thumbDirName;
			$deletePath = Yii::getAlias($thumbPath . DIRECTORY_SEPARATOR . $model->image);
			@unlink($deletePath);
		}
		$deletePath = Yii::getAlias($uploadDir . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR . $model->image);
		@unlink($deletePath);
	}
	
}
