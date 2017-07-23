<?php
namespace app\models;
use Yii;
use yii\db\ActiveRecord;
class Msg extends ActiveRecord {  
    // 触发  
    public function approver($event) {  
        print_r($event->data);//输出传递过来的‘您有一个新的申请待审批’  
        //做逻辑处理  
  
    }  
  
    public function reader($event) {  
        print_r($event->data);//输出传递过来的‘您有一个新的抄送’  
        //做逻辑处理  
    }  
}
?>