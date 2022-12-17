<?php
namespace app\controllers;
use Yii;
use app\models\Ride;
use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;
use yii\rest\ActiveController;
class RideController extends FunctionController
{

    public function behaviors()
    {
        /*
         * Указание на аутентификации по токену
         */
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'=>['create', 'red', 'del'] //Перечислите для контроллера методы, требующие аутентификации
            //здесь метод actionAccount()
        ];
        return $behaviors;
    }

    public $modelClass = 'app\models\ride';

    public function actionCreate(){
        $request=Yii::$app->request->post(); //получение данных из post запроса
        $ride=Yii::$app->user->identity;
        $ride=new Ride($request); // Создание модели на основе присланных данных
        if (!$ride->validate()) return $this->validation($ride); //Валидация модели
        $ride->save();//Сохранение модели в БД
        return $this->send(200, ['content'=>['code'=>200, 'status'=>'ok']]);//Отправка сообщения пользователю
    }
    public function actionTickets()
    {
        $ride = Ride::find()->indexBy('id')->all();
        return $this->send(200, ['content'=> ['Билеты'=>$ride]]);
    }
    public function  actionRed($id)
    {

        $user=Yii::$app->user->identity; // Получить идентифицированного пользователя
        $request=Yii::$app->request->getBodyParams();
        $ride=Ride::findOne($id);
       // die($ride-$id);
        if (!$ride) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Рейс не найден']]);
       // return $this->send(200, $ride);
        if (isset($request['code'])) $ride->code = $request['code'];
        if (isset($request['rideFrom'])) $ride->rideFrom = $request['rideFrom'];
        if (isset($request['rideTo'])) $ride->rideTo = $request['rideTo'];
        if (isset($request['count'])) $ride->count = $request['count'];
        if (isset($request['timestart'])) $ride->timestart = $request['timestart'];
        if (isset($request['timefinish'])) $ride->timefinish = $request['timefinish'];
        if (isset($request['status'])) $ride->status = $request['status'];

        if (!$ride->validate()) return $this->validation($ride);
        $ride->save();
        return $this->send(200, ['content'=>['code'=>200, 'message'=>'Данные обновлены']]);

    }
    public function actionDel($id)
    {
        $ride = ride::findOne($id);
        $ride->delete();
        return $this->send(200, ['content'=> ['Status'=>'ok']]);
    }
}
?>
