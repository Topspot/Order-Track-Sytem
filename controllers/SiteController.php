<?php

namespace app\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Contact;
use app\models\OrderForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }


    public function actionOrders()
    {

        $model = new OrderForm();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->refresh();
        }
        $query = OrderForm::find()->orderBy('created_at DESC')->limit(10);
        $data = $query->all();
//        print_r($data);exit;
        $locations = [];
        foreach ($data as $value) {
            if($value->status == 0){
                $image=Yii::$app->request->hostInfo.'/images/icons/057-stopwatch_1_50x50.png';
            }elseif($value->status == 1){
                $image=Yii::$app->request->hostInfo.'/images/icons/005-calendar_1_50x50.png';
            }elseif($value->status == 2){
                $image=Yii::$app->request->hostInfo.'/images/icons/028-express-delivery_50x50.png';
            }elseif($value->status == 3){
                $image=Yii::$app->request->hostInfo.'/images/icons/015-delivered_50x50.png';
            }else{
                $image=Yii::$app->request->hostInfo.'/images/icons/016-delivery-failed_50x50.png';
            }
            if($value->order_type == 1) {
                $order_type = 'Delivery';
            }else if($value->order_type == 2) {
                $order_type='Servicing';
            }else{
                $order_type='Installation';
            }

            $locations[] = ['<b>Name</b>:&nbsp' . $value->first_name . ' '.$value->last_name.'<br><b>Location</b>:&nbsp' . $value->address . '<br><b>Order Type</b>:&nbsp' . $order_type . '<br>', $value->latitude, $value->longitude, $image, $value->id];
        }
//        print_r($locations);exit;
        $this->view->params['locations'] = $locations;

        return $this->render('orders', [
            'model' => $model,
            'orders' => $data,
        ]);
    }
}
