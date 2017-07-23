<?php
namespace app\modules\api\controllers;

use yii\web\Response;  
use Yii;  
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\Approver;
use app\models\Reader;
use yii\web\NotFoundHttpException;
date_default_timezone_set('PRC');
class EvectionController extends ActiveController{
	public $modelClass = 'app\models\Evection';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    const EVENT_APPROVER = 'approver'; //插入审批人数据事件  
    const EVENT_READER = 'read';//插入抄送人数据时间 

	public function actions()
     {
         $actions = parent::actions();
         // 注销系统自带的实现方法
         unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
         return $actions;
     }

   public function behaviors()  
    {  
        $behaviors = parent::behaviors();  
        #定义返回格式是：JSON  
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;  
     return $behaviors;        
                
    }
    //我发起的
    public function myEvection(){
        $modelClass = $this->modelClass;
        $query = $modelClass::find()->where(['user_id'=>1])->all();
        return $query;

    }
    public function actionEvection(){
        $myEvection=$this->myEvection();
        $data = array('err_ok'=>0,'travelInfo' => $myEvection);
        return $data;
    }
     //返回给审核人申请表信息
     public function actionApprovers(){
     	$modelClass = $this->modelClass;
        $data = $this->findApprover();
        if($data == null){
            return array('err_ok'=>0,'travelInfo' => false);
        }
        foreach ($data as $key => $val) {
           $query[$key] = $modelClass::find()
                    ->select(['evection.*','user.user_name'])
                    ->join('LEFT JOIN','user','user.user_id=evection.user_id')
                    ->where(['evection_id'=>$val['evection_id'],'step'=>$val['step']])
                    ->andWhere(['between','evection.state',0,2])
                    ->asArray()->all();
        }
        $i=0;
        foreach ($query as $key => $value) {
            if($value != null){
                $data1[$i]=$value;
                $i++;
            }
        }
        if($i==0){
            $data1 = array('err_ok'=>0,'travelInfo' => false);
        }
        $data1 = array('err_ok'=>0,'travelInfo' => $data1);
        return $data1;
     }
     //返回给抄送人审核表信息
     public function myReader(){
        $modelClass = $this->modelClass;
        $data = $this->findReader();
        $query = $modelClass::find()->select(['evection.*','user.user_name'])
                ->join('LEFT JOIN','user','user.user_id=evection.user_id')
                ->where(['in','evection_id',$data])
                ->andWhere(['evection.state'=>4])
                ->asArray()->all();
         return $query;
     }
     public function actionReaders(){
        $myReader=$this->myReader();
        $data = array('err_ok'=>0,'travelInfo' => $myReader);
        return $data;
     }
     //返回给申请人/抄送人某一申请具体信息
     public function actionIndex(){
        $modelClass = $this->modelClass;
        $query = $modelClass::find()->all();
         return $query;
     }
     //将申请信息插入到申请表中
      public function actionCreate()
     {
         $model = new $this->modelClass();
         $model->attributes = Yii::$app->request->post();
         $data = Yii::$app->request->post();
         $model->create_time= date('Y-m-d H:i:s');
         $model->update_time= date('Y-m-d H:i:s');
         $model->reader= $data['reader'][1];
         $model->accompany=$data['accompany'][1];
         if (! $model->save()) {
             return array_values($model->getFirstErrors())[0];
         }
        //$approver = new ApproverController(); 
        //  $reader = new ReaderController(); 
        // $this->on(self::EVENT_APPROVER,[$approver,'approver'],$model->evection_id);  
        //  $this->on(self::EVENT_READER,[$reader,'reader'],$model->evection_id);  
  
        // //触发  
        //$this->trigger(self::EVENT_APPROVER,$data);  
        // $this->trigger(self::EVENT_READER);
        if($this->approver($data['approver_id'],$model->evection_id) && $this->reader($data['reader'],$model->evection_id)){
            return 1;
        }else{
            return 0;
        }
         return 1;
     }

     public function approver($data,$id){
        foreach ($data as $key=>$val){
            $people= explode(',',$val[1]);
            foreach ($people as $k => $v) {
            $model = new Approver();
            $model->evection_id=$id;
            $model->step=$key+1; 
            $model->type=$val[0];
            $model->create_time= date('Y-m-d H:i:s');
            $model->update_time= date('Y-m-d H:i:s');
            $model->approvers=intval($v);
             if (! $model->save()) {
              return array_values($model->getFirstErrors())[0];
             }
           }
         }
         return 1;
     }
    public function reader($data,$id)
     {
            $people = explode(',',$data[0]);
           foreach ($people as $key => $value) {
            $model = new Reader();
            $model->evection_id=$id;
            $model->create_time= date('Y-m-d H:i:s');
            $model->update_time= date('Y-m-d H:i:s');
            $model->user_id=intval($value);
             if (! $model->save()) {
              return array_values($model->getFirstErrors())[0];
             }
           }
       return 1;
     }
     public function findApprover(){
        $model = new approver();
         $query = $model::find()->select(['evection_id','step'])->where(['approvers'=>5])
         ->andWhere(['state'=>0])->all();
         return $query;
     }
     public function findReader(){
        $model = new reader();
         $query = $model::find()->select(['evection_id'])->where(['user_id'=>5])->all();
         return $query;
     }

    public function actionDelete($id)
    {
         return $this->findModel($id)->delete();
    }

    public function actionView($id)
     {
         return $this->findModel($id);
     }
 
     protected function findModel($id)
     {
         $modelClass = $this->modelClass;
         if (($model = $modelClass::findOne($id)) !== null) {
             return $model;
         } else {
             throw new NotFoundHttpException('The requested page does not exist.');
         }
     }
     //撤销
     public function actionUpdata()
     {
        $id = Yii::$app->request->post();
        $model = $this->findModel($id);
        $model->state = 5;//撤销
        $model->update_time=date('Y-m-d H:i:s');
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
         //将审批人表的状态设为5（用户注销）
        return 1;
     }
}
?>