<?php
/*
 * This file is part of the YiiModules.com
 *
 * (c) Yii2 modules open source project are hosted on <http://github.com/yiimodules/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace yiimodules\categories\controllers;

use Yii;
use yiimodules\categories\models\Categories;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * Default controller for the `categories` module
 */
class DefaultController extends Controller
{
 
    /**
     * Lists all Categories models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Categories();
		
		$parentCategory = false;
	
		if(Yii::$app->request->getQueryParam('id')!=""){
			$model  = $this->findModel(Yii::$app->request->getQueryParam('id'));
		}
	
		if(Yii::$app->request->getQueryParam('parent_id')!=""){
			$model->parent_id = Yii::$app->request->getQueryParam('parent_id');
			$parentCategory = $this->findModel($model->parent_id);
		}

        if ($model->load(Yii::$app->request->post())) {
			$model->save();
			$model->upload();
			if($model->isNewRecord){
				Yii::$app->getSession()->setFlash('success', 'Category information has been stored.');
				return $this->redirect(['index']);
			} else{
				Yii::$app->getSession()->setFlash('success', 'Category information updated successfully.');
				$this->refresh();
			}
        } else {
            return $this->render('index', [
                'model' => $model,
				'parentCategory' => $parentCategory
            ]);
        }
	}

    /**
     * Displays a single Categories model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Categories model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Categories the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Categories::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
