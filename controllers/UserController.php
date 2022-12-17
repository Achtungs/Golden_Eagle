<?php
namespace app\controllers;
use app\models\User;
use app\models\LoginForm;
use yii\rest\ActiveController;
use Yii;

use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;

class UserController extends FunctionController
{

    public function behaviors()
    {
        /*
         * Указание на аутентификации по токену
         */
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'=>['cab'] //Перечислите для контроллера методы, требующие аутентификации
            //здесь метод actionAccount()
        ];
        return $behaviors;
    }

    public $modelClass = 'app\models\User';

    public function actionRegister(){
        $request=Yii::$app->request->post(); //получение данных из post запроса
        $user=new User($request); // Создание модели на основе присланных данных
        if (!$user->validate()) return $this->validation($user); //Валидация модели
        $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password); //хэширование пароля
        $user->save();//Сохранение модели в БД
        return $this->send(204, $user);//Отправка сообщения пользователю
    }

    public function actionLogin(){
        $request=Yii::$app->request->post();//Здесь не объект, а ассоциативный массив
        $loginForm=new LoginForm($request);
        if (!$loginForm->validate()) return $this->validation($loginForm);
        $user=User::find()->where(['login'=>$request['login']])->one();
        if (isset($user) && Yii::$app->getSecurity()->validatePassword($request['password'], $user->password)){
            $user->token=Yii::$app->getSecurity()->generateRandomString();
            $user->save(false);
            return $this->send(200, ['content'=>['token'=>$user->token]]);
        }
        return $this->send(401, ['content'=>['code'=>401, 'message'=>'Неверный email или пароль']]);
    }
    public function actionCab()
    {
        $user=Yii::$app->user->identity; // Получить идентифицированного пользователя
        return $this->send(200, ['content'=> ['user'=>$user]]);
    }
    public function  actionCabred($id)
    {    $request=Yii::$app->request->getBodyParams();
        $user=User::findOne($id);
        if (!$user) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        $user=Yii::$app->user->identity; // Получить идентифицированного пользователя
        if (isset($request['login'])) $user->login = $request['login'];
        if (isset($request['password'])) $user->password = $request['password']  = Yii::$app->getSecurity()->generatePasswordHash($user->password);
        if (isset($request['first_name'])) $user->first_name = $request['first_name'];
        if (isset($request['last_name'])) $user->last_name = $request['last_name'];
        if (isset($request['phone'])) $user->phone = $request['phone'];
        if (isset($request['document_number'])) $user->document_number = $request['document_number'];

        if (!$user->validate()) return $this->validation($user);
        $user->save();
            return $this->send(200, ['content'=>['code'=>200, 'message'=>'Данные обновлены']]);

    }
    public function actionSubscription($id)
    {
        $request=Yii::$app->request->getBodyParams();
        $user=User::findOne($id);
        if (!$user) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        $user=Yii::$app->user->identity; // Получить идентифицированного пользователя
        if (isset($request['subscription'])) $user->subscription = $request['subscription'];
        if (!$user->validate()) return $this->validation($user);
        $user->save();
        return $this->send(200, ['content'=> ['code'=>200, 'Подписка'=>'Успешно оформлена']]);
    }
}
?>