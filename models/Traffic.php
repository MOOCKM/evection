<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "traffic".
 *
 * @property integer $traffic_id
 * @property string $traffic_name
 * @property string $create_time
 * @property string $update_time
 * @property integer $state
 */
class Traffic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'traffic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['traffic_name', 'create_time', 'update_time'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['state'], 'integer'],
            [['traffic_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'traffic_id' => 'Traffic ID',
            'traffic_name' => 'Traffic Name',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'state' => 'State',
        ];
    }
   public function fields()
   {
    $fields = parent::fields();

    // 删除一些包含敏感信息的字段
    unset($fields['create_time'], $fields['update_time']);

    return $fields;
  }
}
