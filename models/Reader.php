<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reader".
 *
 * @property integer $reader_id
 * @property integer $evection_id
 * @property integer $user_id
 * @property string $create_time
 * @property string $update_time
 * @property integer $state
 */
class Reader extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reader';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evection_id', 'user_id', 'create_time', 'update_time'], 'required'],
            [['evection_id', 'user_id', 'state'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reader_id' => 'Reader ID',
            'evection_id' => 'Evection ID',
            'user_id' => 'User ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'state' => 'State',
        ];
    }
}
