<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "courses_tab".
 *
 * @property int $id
 * @property string $course_name
 * @property int $enrollments_counter
 * @property string $date_begin_enrollments
 * @property string $date_end_enrollments
 * @property string $teacher_name
 *
 * @property EnrollmentsTab[] $enrollmentsTabs
 */
class CoursesTab extends ActiveRecord
{
    public static function tableName()
    {
        return 'courses_tab';
    }

    public function rules()
    {
        return [
            [['course_name', 'date_begin_enrollments', 'date_end_enrollments', 'teacher_name'], 'required'],
            [['enrollments_counter'], 'integer'],
            [['date_begin_enrollments', 'date_end_enrollments'], 'date', 'format' => 'php:Y-m-d'],
            [['course_name', 'teacher_name'], 'string', 'max' => 100],
            [['date_end_enrollments', 'date_begin_enrollments'], 'validateDates'],
        ];
    }

    public function validateDates($attribute, $params)
    {
        if ($this->date_begin_enrollments && $this->date_end_enrollments) {
            if (strtotime($this->date_end_enrollments) < strtotime($this->date_begin_enrollments)) {
                $this->addError('date_end_enrollments', 'La fecha de fin no puede ser anterior a la fecha de inicio.');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'id'                      => 'ID',
            'course_name'             => 'Nombre del curso',
            'enrollments_counter'     => 'Total de inscritos',
            'date_begin_enrollments'  => 'Fecha inicio inscripciones',
            'date_end_enrollments'    => 'Fecha fin inscripciones',
            'teacher_name'            => 'Nombre del docente',
        ];
    }

    public function getEnrollmentsTabs()
    {
        return $this->hasMany(EnrollmentsTab::class, ['course_id' => 'id']);
    }
}