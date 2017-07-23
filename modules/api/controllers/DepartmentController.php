<?php
namespace app\modules\api\controllers;

use yii\web\Response;  
use Yii;  
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class DepartmentController extends ActiveController
{
	public $modelClass = 'app\models\Department';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function actionTest()   {    
           return [  
               'message' => 'API test Ok!',  
               'code' => 100,  
           ];  
    }
     public function behaviors()  
    {  
        $behaviors = parent::behaviors();  
        #定义返回格式是：JSON  
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;  
        return  $behaviors ; 
    }
     public function actions()
     {
         $actions = parent::actions();
         // 注销系统自带的实现方法
         unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
         return $actions;
     
}
public function search(){
	$modelClass = $this->modelClass;
         $query = $modelClass::find();
         return new ActiveDataProvider([
             'query' => $query
         ]);
}
     public function actionIndex()
     {
     	$cache= \yii::$app->cache;
     	$dependency=new \yii\caching\DbDependency(['sql'=>'select count(*) from department']);
     	$department=$this->search();
        $cache->add('department',$department,3000,$dependency);
        $data=$cache->get('department');
        return $data;
     }
         public function actionCreate()
     {
         $model = new $this->modelClass();
         // $model->load(Yii::$app->getRequest()
         // ->getBodyParams(), '');
         $model->attributes = Yii::$app->request->post();
         $model->create_time= date('Y-m-d H:i:s');
         $model->update_time= date('Y-m-d H:i:s');
         if (! $model->save()) {
             return array_values($model->getFirstErrors())[0];
         }
         return 1;
     }
      public function actionDelete($id)
     {
         return $this->findModel($id)->delete();
     }

}