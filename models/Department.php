<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "department".
 *
 * @property integer $department_id
 * @property string $department_name
 * @property string $create_time
 * @property string $update_time
 * @property integer $state
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_name', 'create_time', 'update_time'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['state'], 'integer'],
            [['department_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'department_id' => 'Department ID',
            'department_name' => 'Department Name',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'state' => 'State',
        ];
    }
}
