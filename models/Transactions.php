<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property int $notify_time
 * @property string $bundle
 * @property string $base64_token
 * @property int $created_at
 */
class Transactions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notify_time', 'created_at'], 'integer'],
            [['base64_token'], 'required'],
            [['bundle', 'base64_token'], 'string', 'max' => 255],
            [['base64_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'notify_time' => 'Notify Time',
            'bundle' => 'Bundle',
            'base64_token' => 'Base64 Token',
            'created_at' => 'Created At',
        ];
    }
}
