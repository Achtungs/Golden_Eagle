<?php
namespace app\controllers;
use app\models\Ride;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;
use Yii;
use app\models\Reservation;
use app\models\User;
class ReservationController extends FunctionController
{

    public function behaviors()
    {
        /*
         * Указание на аутентификации по токену
         */
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'=>['order'] //Перечислите для контроллера методы, требующие аутентификации
            //здесь метод actionAccount()
        ];
        return $behaviors;
    }

    public $modelClass = 'app\models\reservation';

    public function actionOrder($id){
        $user=Yii::$app->user->identity; // Получить идентифицированного пользователя
        $request=Yii::$app->request->getBodyParams();

        $ride=Ride::findOne($id);
        if (!$ride) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Рейс не найден']]);


        if (!$user) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);

        $reservation=new reservation();
        $reservation->ride_id=$id;
        $reservation->user_id=$user->id;
        $reservation->ride_data=$request['ride_data'];
        if (!$reservation->validate()) return $this->validation($reservation); //Валидация модели
        $reservation->save();//Сохранение модели в БД
        return $this->send(200, $reservation);//Отправка сообщения пользователю
    }
}
?>
