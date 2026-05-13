<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "public_user_tab".
 *
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string|null $phone
 * @property string $email
 * @property int|null $age
 * @property int|null $ci
 * @property string|null $public_entity
 * @property int $n_courses_enrollment
 *
 * @property EnrollmentsTab[] $enrollmentsTabs
 */
class PublicUserTab extends ActiveRecord
{

    const NATIONALITY_VENEZOLANO = 'venezolano';
    const NATIONALITY_EXTRANJERO = 'extranjero';

    public $voucher;

    public static function tableName()
    {
        return 'public_user_tab';
    }

    public function rules()
    {
        return [
            [['name', 'last_name', 'email', 'ci', 'nationality'], 'required'],
            [['age', 'n_courses_enrollment', 'ci'], 'integer'],
            [['age'], 'integer', 'min' => 1, 'max' => 120],
            [['ci'], 'integer', 'min' => 0, 'max' => 1000000000],
            [['ci'], 'unique', 'message' => 'Esta cedula ya esta registrada.', 'on' => ['create']],
            [['name', 'last_name', 'public_entity'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['nationality'], 'string', 'max' => 20],
            [['nationality'], 'in', 'range' => [self::NATIONALITY_VENEZOLANO, self::NATIONALITY_EXTRANJERO]],
            [['phone'], 'match', 'pattern' => '/^[0-9+\-\(\) ]+$/'],
            [['voucher'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, pdf', 'maxSize' => 2 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'name'                  => 'Nombre',
            'last_name'             => 'Apellido',
            'phone'                 => 'Telefono',
            'email'                 => 'Correo electronico',
            'age'                   => 'Edad',
            'ci'                    => 'Cedula de Identidad',
            'nationality'           => 'Nacionalidad',
            'public_entity'         => 'Entidad publica',
            'n_courses_enrollment'  => 'Total cursos inscritos',
            'voucher'               => 'Comprobante de pago', 
        ];
    }

    public function getEnrollmentsTabs()
    {
        return $this->hasMany(EnrollmentsTab::class, ['user_id' => 'id']);
    }

    public function getFullName()
    {
        return $this->name . ' ' . $this->last_name;
    }

    public function isVenezolano()
    {
        return $this->nationality === self::NATIONALITY_VENEZOLANO;
    }

}