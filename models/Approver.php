<?php

namespace app\models;

use Yii;
use app\models\User;

/**
 * This is the model class for table "approver".
 *
 * @property integer $approver_id
 * @property integer $evection_id
 * @property integer $step
 * @property integer $type
 * @property integer $approvers
 * @property string $create_time
 * @property string $update_time
 * @property integer $state
 */
class Approver extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'approver';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evection_id', 'step', 'type', 'approvers'], 'required'],
            [['evection_id', 'step', 'type', 'approvers', 'state'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'approver_id' => 'Approver ID',
            'evection_id' => 'Evection ID',
            'step' => '第几阶段的审批',
            'type' => '线性0，非线性1',
            'approvers' => '该阶段的审核人id',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'state' => '0(默认),1(审核中),2(转审),3(审核成功),4(审核失败)',
        ];
    }

}
