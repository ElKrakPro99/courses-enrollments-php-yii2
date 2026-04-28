<?php

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
 *
 * @property CoursesTab $course
 * @property PublicUserTab $publicUser
 */
class EnrollmentsTab extends ActiveRecord
{
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
            [['course_id'], 'exist', 
                'skipOnError'     => true, 
                'targetClass'     => CoursesTab::class, 
                'targetAttribute' => ['course_id' => 'id']
            ],
            [['user_id'], 'exist', 
                'skipOnError'     => true, 
                'targetClass'     => PublicUserTab::class, 
                'targetAttribute' => ['user_id' => 'id']
            ],
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
        ];
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