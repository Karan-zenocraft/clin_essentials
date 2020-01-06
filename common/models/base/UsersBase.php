<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property int|null $role_id
 * @property string|null $user_name
 * @property string|null $email
 * @property string|null $password
 * @property int|null $age
 * @property int|null $gender
 * @property string|null $photo
 * @property int|null $badge_count
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $restaurant_id
 *
 * @property UserRole $role
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'age', 'gender', 'badge_count', 'status', 'restaurant_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_name', 'email', 'password', 'photo'], 'string', 'max' => 255],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRole::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'user_name' => 'User Name',
            'email' => 'Email',
            'password' => 'Password',
            'age' => 'Age',
            'gender' => 'Gender',
            'photo' => 'Photo',
            'badge_count' => 'Badge Count',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'restaurant_id' => 'Restaurant ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(UserRole::className(), ['id' => 'role_id']);
    }

    /**
     * {@inheritdoc}
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }
}
