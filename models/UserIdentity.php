<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

class UserIdentity extends BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $role;
    public $model;

    public static function findIdentity($id)
    {
        $admin = AdminTab::findOne($id);
        if ($admin) {
            return new static([
                'id'       => $admin->id,
                'username' => $admin->user_nickname,
                'role'     => 'admin',
                'model'    => $admin,
            ]);
        }
        $manager = ManagerTab::findOne($id);
        if ($manager) {
            return new static([
                'id'       => $manager->id,
                'username' => $manager->user_nickname,
                'role'     => 'manager',
                'model'    => $manager,
            ]);
        }
        return null;
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

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    // Getter para acceder a user_nickname directamente desde la identidad
    public function getUserNickname()
    {
        return $this->model->user_nickname ?? $this->username;
    }
}