<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "job".
 *
 * @property integer $job_id
 * @property integer $department_id
 * @property string $job_name
 * @property string $create_time
 * @property string $update_time
 * @property integer $state
 */
class Job extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_id', 'job_name', 'create_time', 'update_time'], 'required'],
            [['department_id', 'state'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['job_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'job_id' => 'Job ID',
            'department_id' => 'Department ID',
            'job_name' => 'Job Name',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'state' => 'State',
        ];
    }
}
