##Yii2 attachments

yii2 文件上传扩展

This fork has the aim to implement some missing features such as multiple fields and simplifying installation

示例
----
You can see the demo on the [krajee](http://plugins.krajee.com/file-input/demo) website

安装
------------

1. 修改composer.json文件
	
	```
      "repositories": [
        {
          "url": "https://github.com/c4ys/yii2-attachments.git",
          "type": "vcs"
        }
      ]
    ```
    
    和
    
    ```
    "c4ys/yii2-attachments": ">=2.0.0"
    ```
	
	to the require section of your `composer.json` file.

2.  添加模块配置
	
	```php
	'modules' => [
		'file' => [
			'class' => 'file\FileModule',
			'webDir' => 'files',
			'tempPath' => '@common/uploads/temp',
			'storePath' => '@common/uploads/store',
			'rules' => [ 
				'maxFiles' => 20,
				'maxSize' => 1024 * 1024 * 20 // 20 MB
			],
		],
	],
	```
	
	添加命令行配置
	
	```php
	'controllerMap' => [
		'file' => [
			'class' => 'yii\console\controllers\MigrateController',
			'migrationPath' => '@file/migrations'
		],
	],
    ```

3. 应用数据库迁移

	```
	yii migrate
	```

4. Attach behavior to your model (be sure that your model has "id" property)
	
	```php
	use yii\helpers\ArrayHelper;
	
	/**
	 * Declare file fields
	 */
	public $my_field_multiple_files;
	public $my_field_single_file;

	/**
	 * Adding the file behavior
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'fileBehavior' => [
				'class' => \file\behaviors\FileBehavior::className()
			]
		]);
	}
	
	/**
	 * Add the new fields to the file behavior
	 */
	public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
			[['my_field_multiple_files', 'my_field_single_file'], 'file'],
        ]);
    }
	```
	
5. Make sure that you have added `'enctype' => 'multipart/form-data'` to the ActiveForm options
	
6. Make sure that you specified `maxFiles` in module rules and `maxFileCount` on `AttachmentsInput` to the number that you want

