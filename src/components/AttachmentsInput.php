<?php

namespace file\components;

use kartik\file\FileInput;
use file\models\UploadForm;
use file\FileModuleTrait;
use yii\base\InvalidConfigException;
use yii\bootstrap\Widget;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
use yii\web\JsExpression;
use yii\jui\JuiAsset;

/**
 * Class AttachmentsInput
 * @package File\components
 * @property FileActiveRecord $model
 */
class AttachmentsInput extends FileInput
{
    use FileModuleTrait;

    public $id;

    public $attribute;

    /** @var FileActiveRecord $model */
    public $model;

    public $pluginOptions = [];

    public $options = [];

    public function init()
    {
        JuiAsset::register($this->view);

        if (empty($this->model)) {
            throw new InvalidConfigException("Property {model} cannot be blank");
        }

        FileHelper::removeDirectory($this->getModule()->getUserDirPath($this->attribute)); // Delete all uploaded files in past

        $this->pluginOptions = array_replace($this->pluginOptions, [
            //'uploadUrl' => Url::toRoute(['/file/file/upload', 'attribute' => $this->attribute, 'model' => get_class($this->model)]),
            'initialPreview' => $this->model->isNewRecord ? [] : $this->model->getInitialPreviewByAttributeName($this->attribute),
            'initialPreviewConfig' => $this->model->isNewRecord ? [] : $this->model->getInitialPreviewConfigByAttributeName($this->attribute),
            'uploadAsync' => false,
            'otherActionButtons' => '<button class="download-file btn-xs btn-default" title="download" {dataKey}><i class="glyphicon glyphicon-download"></i></button>',
            'overwriteInitial' => false,
            'validateInitialCount' => true,
            'maxFileCount' => 5,
            'showPreview' => true,
            'showCaption' => true,
            'showRemove' => false,
            'showUpload' => false,
        ]);

        $this->options = array_replace(
            $this->options,
            [
                //'id' => $this->id,
                'model' => $this->model,
                'attribute' => $this->attribute,
                //'name' => $this->attribute . '[]',
                'multiple' => true
            ]
        );

        $urlSetMain = Url::toRoute('/file/file/set-main');
        $urlRenameFile = Url::toRoute('/file/file/rename');
/*pr($this->id,'THISID');
        $js = <<<JS
            var fileInput{$this->attribute} = $('#{$this->id}');
            var form{$this->attribute} = fileInput{$this->attribute}.closest('form');
            var filesUploaded{$this->attribute} = false;
            var filesToUpload{$this->attribute} = 0;
            var uploadButtonClicked{$this->attribute} = false;
            
            form{$this->attribute}.on('beforeSubmit', function(event) { // form submit event
                if (!filesUploaded{$this->attribute} && filesToUpload{$this->attribute}) {
                    $('#{$this->id}').fileinput('upload').fileinput('lock');
                    return false;
                }
            });
            
            fileInput{$this->attribute}.on('filebatchpreupload', function(event, data, previewId, index) {
                uploadButtonClicked{$this->attribute} = true;
            });
            
            fileInput{$this->attribute}.on('filebatchuploadsuccess', function(event, data, previewId, index) {
                filesUploaded{$this->attribute} = true;
                $('#{$this->id}').fileinput('unlock');
                if (!uploadButtonClicked{$this->attribute}) {
                    form{$this->attribute}.submit();
                } else {
                    uploadButtonClicked{$this->attribute} = false;
                }
            });
            
            fileInput{$this->attribute}.on('filebatchselected', function(event, files) { // there are some files to upload
                filesToUpload{$this->attribute} = files.length
            });
            
            fileInput{$this->attribute}.on('filecleared', function(event) { // no files to upload
                filesToUpload{$this->attribute} = 0;
            });
            
            $('.formInput-{$this->getId()}').on('change', '.jsFileMain', function() {
                var element = $(this);
                var key = element.data('key');
                $.ajax(
                    '$urlSetMain',
                    {
                        method: "POST",
                        data: {
                            id:key,
                            value:element.prop('checked')
                        },
                        success: function(data) {
                            $('.formInput-{$this->getId()} .jsFileMain').prop('checked', false);
                            if(data.id) {
                                 $('.formInput-{$this->getId()} .jsFileMain[data-key="' + data.id + '"]').prop('checked', true);
                            }
                        }
                    }
                );
            });
            
            $('.formInput-{$this->getId()}').on('click', '.js-caption-rename', function() {
                var element = $(this);
                var key = element.data('key');
                var input = $(this).parents('.file-preview-frame').find('.js-custom-caption');
                var name = input.val();
                $.ajax(
                    '$urlRenameFile',
                    {
                        method: "POST",
                        data: {
                            id: key,
                            name: name
                        },
                        success: function(data) {
                        }
                    }
                );
            });
JS;

        \Yii::$app->view->registerJs($js);*/

        parent::init(); // TODO: Change the autogenerated stub
    }

    public function dont_run()
    {
        /*$fileInput = FileInput::widget(
            [
                'model' => new UploadForm(),
                'attribute' => 'file[]',
                'options' => $this->options,
                'pluginOptions' => $this->pluginOptions
            ]
        );*/

        $urlSetOrder = Url::toRoute('/file/file/set-order');
        $urlGetMainFlag = Url::toRoute('/file/file/get-main');

        Yii::$app->view->registerJs(<<<JS
            $('.file-preview-thumbnails').sortable({
                update: function(event, ui) {
                        var order = [];
                        $('.file-preview-thumbnails .kv-file-remove:visible').each(function(k, v) {
                            if($(v).data('key')) {
                                order[k] = $(v).data('key');
                            }
                        });
            
                        if(order.length) {
                            $.ajax(
                                '$urlSetOrder',
                                {
                                    method: "POST",
                                    data: {
                                        order: order
                                    },
                                    success: function(data) {
                                    }
                                }
                            );
                        }
                    }
            });
            
            var loadMainFlag = function() {
                $.ajax(
                    '$urlGetMainFlag',
                    {
                        method: "GET",
                        data: {
                            id: '{$this->model->id}',
                            model: '{$this->model->formName()}'
                        },
                        success: function(id) {
                            if(id !== 0) {
                                $('.file-preview-frame .jsFileMain[data-key='+id+']').prop('checked', true);
                            }
                        }
                    }
                );
            };
            
            loadMainFlag();
JS
);

        return Html::tag('div', $fileInput, ['class' => 'form-group formInput-' . $this->getId()]);
    }
}