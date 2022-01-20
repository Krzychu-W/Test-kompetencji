<?php

class FormFieldFile2 extends qFormField
{
    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_IMAGE, $formAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'size', 'value');
        $this->setItem('size', '12');
        $this->setItem('disptitle', 0);
        $this->setItem('dispdesc', 0);
        $this->setItem('displink', 0);
        $this->setItem('maxCount', 1);
        $this->setItem('accept', '');
        qLayout::cssModule('table', 'jquery.tabledrag.css');
        qLayout::cssModule('storm', 'image.admin.less');
    }

    public function html(): string
    {
        $html = $this->htmlWrapper();
        $html .= $this->htmlLabel();
        $html .= $this->htmlContent();
        $html .= $this->_html();
        $html .= $this->htmlContentEnd();
        $html .= $this->htmlWrapperEnd();

        return $html;
    }

    public function htmlContent()
    {
        $res = '<span class="'.$this->class.'-content '.$this->item('#class').'-field-content">';

        return $res;
    }

    public function htmlContentEnd()
    {
        $res = '</span>';

        return $res;
    }

    public function htmlDescription()
    {
        $html = '';
        if ($this->hasItem('description')) {
            $description = $this->item('description');
            if (strlen($description) > 1) {
                $html .= "<div id=\"{$this->id}-description\" class=\"{$this->item('#class')}-description\">\n";
                $html .= $this->item('description')."\n</div>\n";
            }
        }

        return $html;
    }

    public function _html(): string
    {
        $options = $this->getOptions();
        qLayout::js('public://javascript/fine-uploader/fine-uploader.js');
        $html = '<script type="text/template" id="qq-template-'.$this->field.'">
        <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="'.qTrans::get('storm.fineuploader-drop-images-here').'">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="buttons">
                <div class="qq-upload-button-selector qq-upload-button">
                    <div>'.qTrans::get('storm.fineuploader-select-files').'</div>
                </div>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>'.qTrans::get('storm.fineuploader-processing-dropped-files').'</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
            <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <div class="qq-progress-bar-container-selector">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <a href="#"><span class="qq-upload-file-selector qq-upload-file"></span></a>
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">'.qTrans::get('storm.fineuploader-cancel').'</button>
                    <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">'.qTrans::get('storm.fineuploader-retry').'</button>
                    <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">'.qTrans::get('storm.fineuploader-delete').'</button>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">'.qTrans::get('storm.fineuploader-close').'</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">'.qTrans::get('storm.fineuploader-no').'</button>
                    <button type="button" class="qq-ok-button-selector">'.qTrans::get('storm.fineuploader-yes').'</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">'.qTrans::get('storm.fineupload-cancel').'</button>
                    <button type="button" class="qq-ok-button-selector">'.qTrans::get('storm.fineupload-ok').'</button>
                </div>
            </dialog>
        </div>
    </script>';
        $html .= '<div id="'.$this->id.'-file-list" data-field="'.$this->field.'"></div>';
        $html .= '<div id="'.$this->id.'-dropzone-file" data-form="'.$this->{'#name'}.'" data-field="'.$this->field.'" data-options="'.htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8').'" class="form-field-'.$this->field.'-dropzone file-uploader"></div>';
        qLayout::css('public://javascript/fine-uploader/fine-uploader-new.css');
        qLayout::jsModule('storm', 'file2.js');

        return $html;
    }

    public function getOptions()
    {
        $options = [
      'maxCount' => $this->item('maxCount'),
    ];
        if ($this->item('accept')) {
            $acceptExtensions = $this->item('accept');
            $accept = [];
            foreach ($acceptExtensions as $extension) {
                $accept[] = ltrim($extension, '.');
            }
            $options['accept'] = $accept;
        }
        if ($this->item('folder')) {
            $options['folder'] = $this->item('folder');
        }
        if ($this->item('maxSize')) {
            $options['maxSize'] = $this->item('maxSize');
        }

        //TODO: move translations below out of data attribute (try using clouder settings)
        $options['messages'] = [
      'emptyError' => qTrans::get('storm.fineuploader-empty-error'),
      'maxHeightImageError' => qTrans::get('storm.fineuploader-max-height-image-error'),
      'maxWidthImageError' => qTrans::get('storm.fineuploader-max-width-image-error'),
      'minHeightImageError' => qTrans::get('storm.fineuploader-min-height-image-error'),
      'minWidthImageError' => qTrans::get('storm.fineuploader-min-width-image-error'),
      'minSizeError' => qTrans::get('storm.fineuploader-min-size-error'),
      'noFilesError' => qTrans::get('storm.fineuploader-no-files-error'),
      'onLeave' => qTrans::get('storm.fineuploader-on-leave'),
      'retryFailTooManyItemsError' => qTrans::get('storm.fineuploader-retry-fail-too-many-items-error'),
      'sizeError' => qTrans::get('storm.fineuploader-size-error'),
      'tooManyItemsError' => qTrans::get('storm.fineuploader-too-many-items-error'),
      'typeError' => qTrans::get('storm.fineuploader-type-error'),
      'unsupportedBrowserIos8Safari' => qTrans::get('storm.fineuploader-unsupported-browser-ios8-safari'),
    ];
        $options['text'] = [
      'defaultResponseError' => qTrans::get('storm.fineuploader-default-response-error'),
      'failUpload' => qTrans::get('storm.fineuploader-fail-upload'),
      'formatProgress' => qTrans::get('storm.fineuploader-format-progress'),
      'paused' => qTrans::get('storm.fineuploader-paused'),
      'waitingForResponse' => qTrans::get('storm.fineuploader-waiting-for-response'),
    ];
        $options['deleteFile'] = [
      'confirmMessage' => qTrans::get('storm.fineuploader-delete-confirm-message'),
      'deletingStatusText' => qTrans::get('storm.fineuploader-deleting-status-text'),
      'deletingFailedText' => qTrans::get('storm.fineuploader-deleting-failed-text'),
    ];
        $options['additionalFields'] = [
      'title' => $this->disptitle > 0,
      'description' => $this->dispdesc > 0,
      'link' => $this->displink > 0,
    ];
        $options['files'] = $this->getFiles();

        return $options;
    }

    protected function getFiles()
    {
        $files = $this->item('value');
        $result = [];
        foreach ($files as $file) {
            $image = FileDataImage::getById($file['file']);
            if ($image) {
                $fileData = [
          'uuid' => $image->fid,
          'name' => $image->name,
          'size' => $image->size,
          'thumbnailUrl' => $image->getUrlFile('news'),
          'field' => $this->field,
        ];
                if ($this->dispdesc > 0) {
                    $fileData['description'] = $file['description'];
                }
                if ($this->disptitle > 0) {
                    $fileData['title'] = $file['title'];
                }
                if ($this->displink > 0) {
                    $fileData['link'] = $file['link'];
                }
                $result[] = $fileData;
            }
        }

        return $result;
    }
}
