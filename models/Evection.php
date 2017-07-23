<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "evection".
 *
 * @property integer $evection_id
 * @property integer $user_id
 * @property string $leave_add
 * @property string $reach_add
 * @property string $start
 * @property string $end
 * @property string $time
 * @property string $accompany
 * @property string $reason
 * @property string $traffic
 * @property string $money
 * @property string $reader
 * @property string $approver_id
 * @property string $create_time
 * @property string $update_time
 * @property integer $state
 */
class Evection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'evection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'state'], 'integer'],
            [['leave_add', 'reach_add', 'start', 'end', 'reason', 'money'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['leave_add', 'reach_add'], 'string', 'max' => 30],
            [['start', 'end'], 'string', 'max' => 20],
            [['time'], 'string', 'max' => 3],
            [['accompany', 'reader'], 'string', 'max' => 60],
            [['reason'], 'string', 'max' => 310],
            [['traffic'], 'string', 'max' => 8],
            [['money'], 'string', 'max' => 7],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'evection_id' => 'Evection ID',
            'user_id' => 'User ID',
            'leave_add' => '出发地点',
            'reach_add' => '目的地点',
            'start' => '开始时间',
            'end' => '结束时间',
            'time' => '天数',
            'accompany' => '陪同人员',
            'reason' => '原因',
            'traffic' => 'Traffic',
            'money' => '预算',
            'reader' => '抄送人',
            'step' => '0(默认)1(第一阶段)2(第二阶段)...',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'state' => '0(默认),1(审核中),2(转审),3(审核成功),4(审核失败)',
        ];
    }
}
