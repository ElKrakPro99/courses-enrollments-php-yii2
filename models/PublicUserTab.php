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
    public static function tableName()
    {
        return 'public_user_tab';
    }

    public function rules()
    {
        return [
            [['name', 'last_name', 'email'], 'required'],
            [['age', 'n_courses_enrollment', 'ci'], 'integer'],
            [['age'], 'integer', 'min' => 1, 'max' => 120],
            [['ci'], 'integer', 'min' => 0, 'max' => 1000000000],  // ← NUEVO
            [['ci'], 'unique', 'message' => 'Esta cédula ya está registrada.'],  // ← Único
            [['name', 'last_name', 'public_entity'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['phone'], 'match', 'pattern' => '/^[0-9+\-\(\) ]+$/'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'name'                  => 'Nombre',
            'last_name'             => 'Apellido',
            'phone'                 => 'Teléfono',
            'email'                 => 'Correo electrónico',
            'age'                   => 'Edad',
            'ci'                    => 'Cédula de Identidad',  // ← NUEVO
            'public_entity'         => 'Entidad pública',
            'n_courses_enrollment'  => 'Total cursos inscritos',
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
}