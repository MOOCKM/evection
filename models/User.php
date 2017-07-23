<?php
namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use app\models\RegisterForm;
use app\models\Job;
use app\models\Approver;
class User  extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
         
     public static function tableName()
    {
        return 'user';
    }

     public function rules()
    {
        return [
            // 对username的值进行两边去空格过滤
            ['user_name', 'filter', 'filter' => 'trim'],
            
            // required表示必须的，也就是说表单提交过来的值必须要有, message 是username不满足required规则时给的提示消息
            ['user_name', 'required', 'message' => '用户名不可以为空'],
            
            // unique表示唯一性，targetClass表示的数据模型 这里就是说UserBackend模型对应的数据表字段username必须唯一
            ['user_name', 'unique', 'message' => '用户名已存在.'],
            ['user_name', 'string', 'min' => 2, 'max' => 14],

            ['tel', 'filter', 'filter' => 'trim'],
            ['tel', 'required', 'message' => '电话不能为空'],
            ['tel', 'string', 'max' => 20],
             ['tel', 'unique',  'message' => '电话已经被设置了.'],
            ['password', 'required', 'message' => '密码不可以为空'],
            ['password', 'string', 'min' => 6, 'tooShort' => '密码至少填写6位'],
            // default 默认在没有数据的时候才会进行赋值
            // [['created_at', 'updated_at'], 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }
     public static function findIdentity($id)
    {
        return static::findOne($id);
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accesstoken' => $token]);
    }

     public function getId()
     {
         return $this->user_id;
     }
     public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->user_name);
        }

        return $this->_user;
    }

        public static function findByUsername($user_name)
    {
          $user = User::find()
            ->where(['user_name' => $user_name])
            ->asArray()
            ->one();

            if($user){
            return new static($user);
        }

        return null;
        /*foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;*/
    }
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

}
