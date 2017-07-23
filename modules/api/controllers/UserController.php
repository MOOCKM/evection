<?php
namespace app\modules\api\controllers;

use yii\web\Response;  
use Yii;  
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\LoginForm;
date_default_timezone_set('PRC');
class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
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
     //查看所有用户--》管理员权限
     public function actionIndex()
     {
         $modelClass = $this->modelClass;
         $query = $modelClass::find();
         return new ActiveDataProvider([
             'query' => $query
         ]);
     }

     public function actionMyself(){
        $modelClass = $this->modelClass;
         $query = $modelClass::find()->select(['user.user_name','department.department_name','department.department_id','job.job_name','job.job_id','user.tel','user.age'])
         ->join('LEFT JOIN','department','department.department_id=user.department_id')
         ->join('LEFT JOIN','job','job.job_id=user.job_id')
         ->where(['user.user_id'=>1])->asArray()->all();
        return array('err_ok'=>0,'user'=>$query);
     }

      //根据部门id返回管理职员的id，name，job
     protected function findUser(){
        $modelClass = $this->modelClass;
         $query = $modelClass::find()->select(['user.department_id','department.department_name'])->join('LEFT JOIN','department','department.department_id=user.department_id')->groupBy('user.department_id')->asArray()->all();
         foreach($query as $key=>$value){
            $data[$key]['department']=$value['department_name'];
            $data[$key]['peopleInfo'] = $modelClass::find()->select(['user.user_id','user.user_name','job.job_name'])->join('LEFT JOIN','job','job.job_id=user.job_id')->where(['user.department_id'=> $value['department_id']])->asArray()->all();
         }
         return $data;
     }
     public function actionUser(){
        $cache= \yii::$app->cache;
        if($cache->get('user')){
            $data=$cache->get('user');
            $data = array('peoples' => $data);
            return $data;
        }
        $dependency=new \yii\caching\DbDependency(['sql'=>'select max(update_time) from user']);
        $user=$this->findUser();
        $cache->add('user',$user,3000,$dependency);
        $data=$cache->get('user');
        $data = array('peoples' => $data);
        return $data;
     }


     public function actionCreate()
     {
         $model = new $this->modelClass();
         // $model->load(Yii::$app->getRequest()
         // ->getBodyParams(), '');
         $model->attributes = Yii::$app->request->post();
         $model->create_time=date('Y-m-d H:i:s');
         $model->update_time=date('Y-m-d H:i:s');
         if (! $model->save()) {
             return array_values($model->getFirstErrors())[0];
         }
         return 1;
     }
    //修改用户信息（角色，部门，职位）--》管理员权限
     public function actionUpdate($id)
     {
         $model = $this->findModel($id);
         $model->attributes = Yii::$app->request->post();
         $model->update_time=date('Y-m-d H:i:s');
         if (! $model->save()) {
             return array_values($model->getFirstErrors())[0];
         }
         return $model;
     }
    //删除用户--》管理员权限
     public function actionDelete($id)
     {
         return $this->findModel($id)->delete();
     }
    //查看用户--》管理员权限
    public function actionSearch($name){  
        $one = User::findOne(['user_name'=>$name]);  
        return $one;  
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
     //登录，并获取个人信息
     public function actionLogin(){
        $model = new LoginForm();
         $model->attributes = Yii::$app->request->post();
        if (! $model->login()) {
             return array_values($model->getFirstErrors())[0];
         }else{
            return $model->login();
         }
     }
 }    
