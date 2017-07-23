<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property integer $role_id
 * @property string $role_name
 * @property string $create_time
 * @property string $update_time
 * @property integer $state
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_name'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['state'], 'integer'],
            [['role_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => 'Role ID',
            'role_name' => 'Role Name',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'state' => '默认0为普通员工',
        ];
    }
}
