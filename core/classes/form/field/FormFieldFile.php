<?php

use Core\Javascript;

class FormFieldFile extends qFormField
{
    public $tagAttrib = array('onChange');

    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_FILE, $formAttr);
        $this->setHtmlAttrib('class');
        $this->refresh = array('table' => '', 'field' => '', 'id' => '');
        $this->setItem('call', '');
        $this->setItem('private', false);
        qLayout::cssModule('table', 'jquery.tabledrag.css');
        qLayout::addScript(file_get_contents(dirname(__DIR__).DS.'javascript'.DS.'file.single.upload.js'));
    }

    public function html(): string
    {
        $html = '<fieldset';
        $html .= $this->getHtmlAttr($this->items());
        $html .= ">\n";
        if ($this->hasItem('title')) {
            $html .= "<legend>{$this->item('title')}</legend>\n";
        }
        $html .= $this->htmlDescription();
        $html .= '<h5><i></i>Pliki<span class="description">Maksymalny rozmiar pliku wynosi '.Upload::maxFilesize(qSetting::dot('upload.file.maxsize', false), 'M').'.</span></h5>';
        $html .= '<div id="image-update">';
        $html .= '<div id="'.$this->field.'-drop-zone" class="drop-zone drop-zone-file" ondragover="return false"><div class="upload-progress-bar"><div class="progress-bar"></div></div><span><i></i>kliknij lub przeciągnij plik aby dodać</span></div>';
        $html .= '<div class="input-append">';
        $html .= '<input type="hidden" class="maxUploadImageCount" value="1" />';
        $html .= '<input type="hidden" class="thumbStyle" value="'.$this->thumb.'" />';
        $html .= '</div>';

        $html .= '<input class="upload-new-obj" type="hidden" name="'.$this->field.'-upload_refresh_table" value="'.$this->item('#name').'" />'."\n";
        $html .= '<input class="upload-new-field" type="hidden" name="'.$this->field.'-upload_refresh_field" value="'.$this->field.'" />'."\n";
        $html .= '<input class="upload-folder" type="hidden" name="'.$this->field.'-upload_folder" value="'.$this->folder.'" />'."\n";
        if ($this->private) {
            $html .= '<input class="upload-private" type="hidden" name="'.$this->field.'-upload_private" value="1" />'."\n";
        } else {
            $html .= '<input class="upload-private" type="hidden" name="'.$this->field.'-upload_private" value="0" />'."\n";
        }
        $html .= '<input class="upload-thumb" type="hidden" name="'.$this->field.'-upload_thumb" value="'.$this->thumb.'" />'."\n";
        $html .= '<input class="upload-call" type="hidden" name="'.$this->field.'-upload_call" value="'.$this->call.'" />'."\n";
        if ($this->hasItem('accept')) {
            $html .= '<input class="upload-extensions" type="hidden" name="'.$this->field.'-upload-extensions" value="sukcesik" />'."\n";
        }
        $html .= '</div>';

        $html .= '<div id="'.$this->field.'-image-update-wait" style="display:none">';
        $html .= 'Poczekaj';
        $html .= '</div>';
        if ($this->value > 0) {
            $html .= '<div id="'.$this->field.'-image-update-image" class="empty-img hide">';
        } else {
            $html .= '<div id="'.$this->field.'-image-update-image" class="empty-img">';
        }
        $html .= 'Brak pliku';
        $html .= '</div>';
        $html .= '<table class="image-update-image draggable">';

        $html .= "<tr>\n<td class=\"upload-row-image\">";
        // pole deleted
        $atItems = $this->items();
        $atItems['name'] .= '[deleted]';
        $html .= '<input class="upload-deleted" type="hidden" name="'.$atItems['name'].'" value="0" />'."\n";

        // pole fid
        $atItems = $this->items();
        $atItems['name'] .= '[fid]';
        $atItems['value'] = '0';
        $html .= '<input type="hidden" name="'.$atItems['name'].'" value="'.$this->value.'" />'."\n";
        $file = FileDataBase::getById($this->value);
        if ($file) {
            //$html .= '<img alt="" src="'.$file->getUrlFile($this->thumb).'" />';
            $html .= $file->name;
        } else {
            $html .= '&nbsp;';
        }
        $html .= '</td>'."\n";
        // akcja usuń
        if ($file) {
            $html .= '<td class="upload-row-actions"><input class="btn" type="button" title="Usuń" value="Usuń" onclick="qAnt.module.upload.rowDelete(this); return false;" /></td>';
        }
        $html .= "</tr>\n";

        $html .= '</table>';

        $image = FileDataBase::getById($this->value);
        /*
         * checking mime types, continues in module "upload" inside js file "file.single.upload";
         * @autor Paweł Rychter
         */
        $type = MimeItems::items();
        if ($this->hasItem('accept')) {
            $extensions = $this->item('accept');
            if (is_string($extensions)) {
                $extensions = explode('.', $extensions);
            }
            $extensionsHtml = '';
            $mimeTypes = array();
            foreach ($extensions as $extension) {
                if (isset($type[$extension])) {
                    $extensionsHtml .= '.'.$extension.',';
                    $mimeTypes[] = $type[$extension];
                }
            }
        }
        $extensions = count(0 == $extensions) ? json_encode($extensions) : 'false';
        $extensionsHtml = rtrim($extensionsHtml, ',');
        $acceptedMime = count(0 == $mimeTypes) ? json_encode($mimeTypes) : 'false';
        /*
         * end
         */
        $html .= '
      <!--[if IE]>
      <form action="" method="POST" name="file_upload" enctype="multipart/form-data"><input style="display:block;" type="file" name="file" id="'.$this->field."-new_img_input_ie\" multiple /></form>
      <script type=\"text/javascript\">
        $(function(){
          $('#".$this->field."-drop-zone').click(function(e){
            e.preventDefault();
            $('#".$this->field."-new_img_input_ie').click();
          });
          $('#".$this->field."-new_img_input_ie').change(function(){
            qAnt.module.upload.image.up_ie('file');
          });
        });
      </script>
      <![endif]-->
      <!--[if !IE]> -->
      <input style=\"display:none;\" type=\"file\" name=\"file\" id=\"".$this->field.'-new_img_input" multiple accept="'.$extensionsHtml."\"/>
      <script type=\"text/javascript\">
        jQuery(function(){
          jQuery('#".$this->field."-drop-zone').click(function(e){
            e.preventDefault();
            jQuery('#".$this->field."-new_img_input').click();
          });
          jQuery('#".$this->field."-new_img_input').change(function(e){
            qAnt.module.upload.file.up('file', e, false, false, this, ".$acceptedMime.', '.$extensions.');
          });
        });
      </script>
      <!-- <![endif]-->';

        $html .= "</fieldset>\n";
        $html .= '<script type="text/javascript">';
        $html .= Javascript::getFileContents('upload', 'file.single.upload.js');
        $html .= '</script>';
        if (isset($this->prefix) && isset($this->suffix)) {
            $html = $this->prefix.$html.$this->suffix;
        }

        return $html;
    }
}
