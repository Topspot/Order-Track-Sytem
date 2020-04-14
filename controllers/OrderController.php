<?php

namespace app\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\OrderForm;

class OrderController extends Controller
{
    /**
     * Show orders listing and add form with map
     *
     * @return Array
     */
    public function actionIndex()
    {
        $model = new OrderForm();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->refresh();
        }
        $query = OrderForm::find()->orderBy('created_at DESC')->limit(10);
        $data = $query->all();
        $locations = [];
        foreach ($data as $value) {
            if ($value->order_type == 1) {
                $order_type = 'Delivery';
            } else if ($value->order_type == 2) {
                $order_type = 'Servicing';
            } else {
                $order_type = 'Installation';
            }
            $image=$this->actionGetImage($value->status);
            $locations[] = ['<b>Name</b>:&nbsp' . $value->first_name . ' ' . $value->last_name . '<br><b>Location</b>:&nbsp' . $value->address . '<br><b>Order Type</b>:&nbsp' . $order_type . '<br>', $value->latitude, $value->longitude, $image, $value->id];
        }
//        print_r($locations);exit;
        $this->view->params['locations'] = $locations;

        return $this->render('index', [
            'model' => $model,
            'orders' => $data,
        ]);
    }

    /**
     * Update status of an order through Ajax
     *
     * @return Json Array
     */
    public function actionStatus()
    {

        if (Yii::$app->request->isAjax) {
            $status = Yii::$app->request->post('status');
            $order_id = Yii::$app->request->post('order_id');

            if (isset($status) && isset($order_id)) {
                $post = OrderForm::findOne($order_id);
                $post->status = $status;
                $post->save();
                $image=$this->actionGetImage($post->status);

                return $this->asJson([
                    'data' => [
                        'success' => true,
                        'model' => $post,
                        'image' => $image,
                        'message' => 'Model has been saved.',
                    ],
                    'code' => 0,
                ]);
            } else {
                return $this->asJson([
                    'data' => [
                        'success' => false,
                        'model' => null,
                        'message' => 'An error occured.',
                    ],
                    'code' => 1, // Some semantic codes that you know them for yourself
                ]);
            }
        }
    }

    /**
     * Delete Order by order_id through Ajax
     *
     * @return Json Array
     */
    public function actionDelete()
    {
        $order_id = Yii::$app->request->post('order_id');
        if (isset($order_id)) {
            $model = OrderForm::find()->where(['id' => $order_id])->one();
            $model->delete();

            return $this->asJson([
                'data' => [
                    'success' => true,
                    'message' => 'Order has been deleted.',
                ],
                'code' => 0,
            ]);
        } else {
            return $this->asJson([
                'data' => [
                    'success' => false,
                    'message' => 'An error occurred.',
                ],
                'code' => 1, // Some semantic codes that you know them for yourself
            ]);
        }
    }

    /**
     * Get image name by status
     *
     * @return Json Array
     */
    private function actionGetImage($status)
    {
        if ($status == 0) {
            $image = Yii::$app->request->hostInfo . '/images/icons/057-stopwatch_1_50x50.png';
        } elseif ($status == 1) {
            $image = Yii::$app->request->hostInfo . '/images/icons/005-calendar_1_50x50.png';
        } elseif ($status == 2) {
            $image = Yii::$app->request->hostInfo . '/images/icons/028-express-delivery_50x50.png';
        } elseif ($status == 3) {
            $image = Yii::$app->request->hostInfo . '/images/icons/015-delivered_50x50.png';
        } else {
            $image = Yii::$app->request->hostInfo . '/images/icons/016-delivery-failed_50x50.png';
        }
        return $image;
    }

}