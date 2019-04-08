<?php

namespace file\models;

use file\FileModuleTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;
use yii\validators\DateValidator;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $model 模型
 * @property string $attribute 属性
 * @property int $item_id 实体ID
 * @property string $type 类型
 * @property string $name 文件名
 * @property string $hash HASH
 * @property string $mime MIME
 * @property int $is_main 主图
 * @property string $created_at 创建时间
 * @property int $sort 顺序
 * @property int $size 大小
 */
class File extends ActiveRecord
{
    use FileModuleTrait;

    const MAIN = 1;
    const NOT_MAIN = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return \Yii::$app->getModule('file')->tableName;
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'attribute', 'item_id', 'type', 'hash', 'mime'], 'required'],
            [['item_id', 'is_main', 'sort', 'size'], 'integer'],
            ['created_at', 'date', 'type' => DateValidator::TYPE_DATETIME],
            [['model', 'attribute', 'type', 'mime'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 200],
            [['hash'], 'string', 'max' => 64],
            [['hash'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => '模型',
            'attribute' => '属性',
            'item_id' => '实体ID',
            'type' => '类型',
            'name' => '文件名',
            'hash' => 'HASH',
            'mime' => 'MIME',
            'is_main' => '主图',
            'created_at' => '创建时间',
            'sort' => '顺序',
            'size' => '大小',
        ];
    }

    public function getUrl()
    {
        return Url::to(['/file/file/download', 'id' => $this->id]);
    }

    public function getWebUrl()
    {
        return str_replace('@webroot', '', Yii::$app->modules['file']->storePath . "/" . \Yii::$app->modules['file']->getSubDirs($this->hash) . DIRECTORY_SEPARATOR . $this->hash . '.' . $this->type);
    }

    public function getPath()
    {
        return $this->getModule()->getFilesDirPath($this->hash) . DIRECTORY_SEPARATOR . $this->hash . '.' . $this->type;
    }
}
