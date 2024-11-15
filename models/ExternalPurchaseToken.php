<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "external_purchase_token".
 *
 * @property int $id
 * @property string $external_purchase_id
 * @property int $notify_time
 * @property string $bundle
 * @property string $base64_token
 * @property int $created_at
 */
class ExternalPurchaseToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'external_purchase_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notify_time', 'created_at'], 'integer'],
            [['base64_token'], 'required'],
            [['bundle', 'base64_token','external_purchase_id'], 'string', 'max' => 255],
            [['base64_token','external_purchase_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_purchase_id' => 'External Purchase Id',
            'notify_time' => 'Notify Time',
            'bundle' => 'Bundle',
            'base64_token' => 'Base64 Token',
            'created_at' => 'Created At',
        ];
    }
    
    public function setNotifyTime(){
        
        $this->notify_time = time();
        $this->save();
    }
}
