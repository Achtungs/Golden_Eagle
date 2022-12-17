<?php

namespace app\models;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "ride".
 *
 * @property int $id
 * @property string|null $code
 * @property string $RideFrom
 * @property string $RideTo
 * @property string|null $timestart
 * @property string|null $timefinish
 * @property string $status
 *
 * @property Reservation[] $reservations
 */
class Ride extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ride';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['RideFrom', 'RideTo', 'status'], 'required'],
            [['timestart', 'timefinish'], 'safe'],
            [['code'], 'string', 'max' => 5],
            [['RideFrom', 'RideTo', 'status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'RideFrom' => 'Ride From',
            'RideTo' => 'Ride To',
            'timestart' => 'Timestart',
            'timefinish' => 'Timefinish',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Reservations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservations()
    {
        return $this->hasMany(Reservation::className(), ['ride_id' => 'id']);
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
