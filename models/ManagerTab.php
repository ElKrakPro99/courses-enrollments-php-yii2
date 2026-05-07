<?php

//models/ManagerTab.php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class ManagerTab extends ActiveRecord implements IdentityInterface  // ← Cambiar a MAYÚSCULA
{
    const SCENARIO_LOGIN = 'login';

    public static function tableName()
    {
        return 'manager_tab';
    }

    public function rules()
    {
        return [
            [['user_nickname', 'hash_pass'], 'required', 'on' => ['default', 'create']],
            [['user_nickname', 'hash_pass'], 'required', 'on' => self::SCENARIO_LOGIN],
            [['user_nickname'], 'string', 'max' => 50],
            [['hash_pass'], 'string', 'max' => 255],
            [['user_nickname'], 'unique', 'on' => ['default', 'create']],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_LOGIN => ['user_nickname', 'hash_pass'],
            'default' => ['user_nickname', 'hash_pass'],
            'create' => ['user_nickname', 'hash_pass'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'user_nickname' => 'Usuario',
            'hash_pass'     => 'Contraseña',
        ];
    }

    public static function findByUsername($username)
    {
        return static::findOne(['user_nickname' => $username]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->hash_pass);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }
}