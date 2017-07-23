<?php
namespace app\modules\api\controllers;

use yii\web\Response;  
use Yii;  
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\models\Evection;
date_default_timezone_set('PRC');
class ApproverController extends ActiveController
{
	public $modelClass = 'app\models\Approver';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    public function nowStep($id){
        $model = new Evection;
        $query = $model::find()->where(['evection_id'=>$id])->one(); 
        return $query->step;       
    }
    public function actionApprover()
    {
        $modelClass = $this->modelClass;
        $id=Yii::$app->request->post('evection_id');
        $step = $this->nowStep($id);
        $query = $modelClass::find()->select(['approver.step','approver.type','user.user_id','user.user_name','approver.state'])
            ->join('LEFT JOIN','user','user.user_id=approver.approvers')
            ->where(['evection_id'=>$id])
            ->andWhere(['<=','step',$step])
            ->asArray()->all();
        $data = array('spInfo' => $query);
        return $data;
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
    //审核人审批，0(默认),1(未审核但该阶段已成功),2(转审),3(审核成功),4(审核失败)
    //put  post(evection_id)
    public function actionUpdata()
    {
        $model = $this->modelClass;
        $data = Yii::$app->request->post();
        $query = $model::find()->where(['evection_id'=>$data['evection_id'],'approvers'=>5])->one();
        $step= $query->step;
        $query->state = $data['state'];
        $query->update_time= date('Y-m-d H:i:s');
        if (! $query->save()) {
            return array_values($query->getFirstErrors())[0];
        }
        if($data['state']==3){//同意
            if($query->type==1){//判断是否为线性
            //将该步骤的审核人状态转为1//审核进入下一阶段
            if($this->toNext($data['evection_id'],$step) && $this->alterStep($data['evection_id'],$step))
               { return 1;// '线性审核人同意，进入下一阶段',  
                }
            }else{ //为非线性
               if($this->isAllPass($data['evection_id'],$step)){//判断是否该阶段一全部同意
                 return 1;// '该阶段审核人全部同意，进入下一阶段',  
             }else{
                    return 1;// '该阶段审核人未全部同意',  
                                    }
            }
         }else{//不同意
            if($this->endEvection($data['evection_id'])){
                return 1;// '审核人拒绝，审核中断',  
            }
         }
         return $query;
     }
     //将该步骤的审核人状态转为1
     public function toNext($evection_id,$step){
        $model =  $this->modelClass;
        $query = $model::find()->where(['evection_id'=>$evection_id,'step'=>$step])
                ->andWhere(['not', ['approvers' =>5]])->all();
        foreach($query as $key=>$val){
            $val->state = 1;
            $val->update_time= date('Y-m-d H:i:s');
            if (! $val->save()){
             return array_values($val->getFirstErrors())[0];
         }
        }
        return 1;
     }
     //成功或审核进入下一阶段
     public function alterStep($id,$step){
         $model = new Evection;
         $query = $model::find()->where(['evection_id'=>$id])->one();
         $query->update_time= date('Y-m-d H:i:s');
        if($step == $this->isFulfill($id)){
           $query->state = 3;
        }else{
            $query->step = $query->step+1;
        }
        if (! $query->save()) {
             return array_values($query->getFirstErrors())[0];
         }
         return 1;
     }
    //判断是否该阶段一全部同意
     public function isAllPass($id,$step){
        $model= $this->modelClass;
        $query =  $model::find()->where(['evection_id'=>$id,'step'=>$step])->all();
        foreach($query as $key=>$val){
            if($val['state'] != 3){
                return 0;//该阶段还未完成
            }
        }
        return $this->alterStep($data['evection_id'],$step);
     }
     //是否已经是最后一步
     public function isFulfill($id){
         $model= $this->modelClass;
        $query = $model::find()->where(['evection_id'=>$id])->max('step');
        return intval($query);
     }
     //审核结束，将state=4
     public function endEvection($id){
        $model = new Evection;
        $query = $model::find()->where(['evection_id'=>$id])->one();
        $query->state=4;
        $query->update_time= date('Y-m-d H:i:s');
        if (! $query->save()) {
             return array_values($query->getFirstErrors())[0];
         }
         return 1;
     }
    public function actionIndex()
     {
         $modelClass = $this->modelClass;
         $query = $modelClass::find();
         return new ActiveDataProvider([
             'query' => $query
         ]);
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

}