<?php

// models/EnrollmentsTab.php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "enrollments_tab".
 *
 * @property int $id
 * @property int $course_id
 * @property int $user_id
 * @property string $date_begin_enrollments
 * @property string $date_end_enrollments
 * @property int $counter_enrollments
 * @property string $teacher_name
 * @property string $status
 *
 * @property CoursesTab $course
 * @property PublicUserTab $publicUser
 */
class EnrollmentsTab extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PAYMENT_PENDING = 'payment_pending';

    public static function tableName()
    {
        return 'enrollments_tab';
    }

    public function rules()
    {
        return [
            [['course_id', 'user_id', 'date_begin_enrollments', 'date_end_enrollments', 'teacher_name'], 'required'],
            [['course_id', 'user_id', 'counter_enrollments'], 'integer'],
            [['date_begin_enrollments', 'date_end_enrollments'], 'date', 'format' => 'php:Y-m-d'],
            [['teacher_name'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 20],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_PAYMENT_PENDING, self::STATUS_CONFIRMED, self::STATUS_CANCELLED]],            [['course_id'], 'exist', 
                'skipOnError'     => true, 
                'targetClass'     => CoursesTab::class, 
                'targetAttribute' => ['course_id' => 'id']
            ],
            [['user_id'], 'exist', 
                'skipOnError'     => true, 
                'targetClass'     => PublicUserTab::class, 
                'targetAttribute' => ['user_id' => 'id']
            ],
            [['voucher_path'], 'string', 'max' => 500],
            [['payment_verified_by'], 'integer'],
            [['payment_verified_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                     => 'ID',
            'course_id'              => 'Curso',
            'user_id'                => 'Usuario',
            'date_begin_enrollments' => 'Fecha inicio',
            'date_end_enrollments'   => 'Fecha fin',
            'counter_enrollments'    => 'Contador',
            'teacher_name'           => 'Docente',
            'status'                 => 'Estado',
            'voucher_path' => 'Comprobante',
            'payment_verified_by' => 'Verificado por',
            'payment_verified_at' => 'Fecha verificacion'
        ];
    }

    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PAYMENT_PENDING => 'Pago Pendiente',  // ← Agregar
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getCourse()
    {
        return $this->hasOne(CoursesTab::class, ['id' => 'course_id']);
    }

    public function getPublicUser()
    {
        return $this->hasOne(PublicUserTab::class, ['id' => 'user_id']);
    }
}