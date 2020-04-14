<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
/**
 * OrderForm is the model behind the contact form.
 */
class OrderForm extends ActiveRecord
{
    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */

    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @return rule for the table associated with this ActiveRecord class.
     */

    public function rules()
    {
        return [
            [['first_name','city','phone_number','state','country','order_type','date','address'], 'required'],
            [['last_name','first_name'], 'match','pattern' => '/^[a-zA-Z\s]+$/','message' => 'This Field can only contain word characters'],
            ['email', 'email'],
            ['phone_number', 'match', 'pattern' => '/\d?(\s?|-?|\+?|\.?)((\(\d{1,4}\))|(\d{1,3})|\s?)(\s?|-?|\.?)((\(\d{1,3}\))|(\d{1,3})|\s?)(\s?|-?|\.?)((\(\d{1,3}\))|(\d{1,3})|\s?)(\s?|-?|\.?)\d{3}(-|\.|\s)\d{4}/', 'message' => 'Phone should be in the format +X (XXX) XXX-XXXX'],
            [['order_value','postal'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
            [['latitude', 'longitude'], 'safe'],
            ['date', 'date', 'format' => 'php:Y-m-d'],

        ];

    }
}
