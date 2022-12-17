<?php

namespace app\models;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "reservation".
 *
 * @property int $id
 * @property int|null $ride_id
 * @property int|null $user_id
 * @property string $ride_data
 *
 * @property Ride $ride
 * @property User $user
 */
class Reservation extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reservation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ride_data'], 'required'],
            [['id', 'ride_id', 'user_id'], 'integer'],
            [['ride_data'], 'safe'],
            [['id'], 'unique'],
            [['ride_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ride::className(), 'targetAttribute' => ['ride_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ride_id' => 'Ride ID',
            'user_id' => 'User ID',
            'ride_data' => 'Ride Data',
        ];
    }

    /**
     * Gets query for [[Ride]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRide()
    {
        return $this->hasOne(Ride::className(), ['id' => 'ride_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login]);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return ;
    }

    public function validateAuthKey($authKey)
    {
        return ;
    }
    public function validatePassword($password)

    {
        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);

        if (Yii::$app->getSecurity()->validatePassword($password, $hash)) {
            return $this;
        } else {
            return 0;
        }


    }
}
