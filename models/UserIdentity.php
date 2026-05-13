<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

class UserIdentity extends BaseObject implements IdentityInterface
{

    public $id;          // ID compuesto: "admin-1", "manager-1", "payment-1"
    public $username;
    public $role;
    public $model;
    public $realId;      // ID real en su tabla

    public static function findIdentity($id)
    {
        // El ID es compuesto: "rol-id" (ej: "admin-1", "payment-1")
        $parts = explode('-', $id);
        
        if (count($parts) !== 2) {
            return null;
        }
        
        $role = $parts[0];
        $realId = $parts[1];

        if ($role === 'admin') {
            $admin = AdminTab::findOne($realId);
            if ($admin) {
                return new static([
                    'id'       => 'admin-' . $admin->id,
                    'username' => $admin->user_nickname,
                    'role'     => 'admin',
                    'model'    => $admin,
                    'realId'   => $admin->id,
                ]);
            }
        } elseif ($role === 'manager') {
            $manager = ManagerTab::findOne($realId);
            if ($manager) {
                return new static([
                    'id'       => 'manager-' . $manager->id,
                    'username' => $manager->user_nickname,
                    'role'     => 'manager',
                    'model'    => $manager,
                    'realId'   => $manager->id,
                ]);
            }
        } elseif ($role === 'payment') {
            $payment = PaymentTab::findOne($realId);
            if ($payment) {
                return new static([
                    'id'       => 'payment-' . $payment->id,
                    'username' => $payment->user_nickname,
                    'role'     => 'payment',
                    'model'    => $payment,
                    'realId'   => $payment->id,
                ]);
            }
        }
        
        return null;
    }

    // ... resto igual ..

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

    public function isPayment()
    {
        return $this->role === 'payment';
    }
}