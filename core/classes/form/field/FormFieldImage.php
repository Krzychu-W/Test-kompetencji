<?php

class FormFieldImage extends qFormField
{
    public $tagAttrib = array('onChange');

    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_IMAGE, $formAttr);
        $this->setHtmlAttrib('class');
        $this->refresh = array('table' => '', 'field' => '', 'id' => '', 'lang' => '');
        $this->setItem('disptitle', 1);
        $this->setItem('dispdesc', 1);
        $this->setItem('list', 1);
        $this->setItem('call', '');
        $this->setItem('thumb', '');
        qLayout::cssModule('table', 'jquery.tabledrag.css');
        qLayout::jsModule('upload', 'img.single.upload.js');
        qLayout::addScript(file_get_contents(dirname(__DIR__).DS.'javascript'.DS.'img.single.upload.js'));
        $size = Upload::maxFilesize(qSetting::dot('upload.file.maxsize', false), 'M', false) / 1048576;
        qLayout::setting('uploadFileMaxsize', $size);
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
        $html .= '<span class="description">Maksymalny rozmiar pliku wynosi '.Upload::maxFilesize(qSetting::dot('upload.file.maxsize', false), 'M').'.</span>';
        $html .= '<div id="image-update"><div class="upload-progress-bar"><div class="progress-bar"></div></div>';
        $html .= '<div id="'.$this->field.'-drop-zone" class="drop-zone" ondragover="return false"><span><i></i>kliknij lub przeciągnij zdjęcie aby dodać</span></div>';
        /*
        $html .= '<label>...lub dodaj w sposób tradycyjny</label>';
        $html .= '<input id="'.$this->field.'-upload-name" type="file" style="display:none;" name="'.$this->field.'-upload-name" class="upload-name"';
        $html .= ' onchange="$(\'#'.$this->field.'-pathname\').val($(this).val());" />';
        */
        $html .= '<div class="input-append">';
        /*
        $html .= '<input id="'.$this->field.'-pathname" name="'.$this->field.'-pathname" type="text" class="upload-pathname" />';
        $html .= '<a class="btn" onclick="$(\'#'.$this->field.'-upload-name\').click();">Przeglądaj</a>';
        $html .= '<a class="btn" onclick="return uploadAction(this,'.$this->disptitle.','.$this->dispdesc.');">Wyślij</a>';
        */
        $html .= '<input type="hidden" class="maxUploadImageCount" value="1" />';
        $html .= '<input type="hidden" class="thumbStyle" value="'.$this->thumb.'" />';
        $html .= '</div>';

        if ($this->disptitle > 0) {
            $html .= '<input class="upload-title" type="hidden" name="'.$this->field.'-upload_title" value="" />'."\n";
        }
        if ($this->dispdesc > 0) {
            $html .= '<input class="upload-description" type="hidden" name="'.$this->field.'-upload_description" value="" />'."\n";
        }

        $html .= '<input class="upload-new-obj" type="hidden" name="'.$this->field.'-upload_refresh_table" value="'.$this->item('#name').'" />'."\n";
        $html .= '<input class="upload-new-field" type="hidden" name="'.$this->field.'-upload_refresh_field" value="'.$this->field.'" />'."\n";
        $html .= '<input class="upload-folder" type="hidden" name="'.$this->field.'-upload_folder" value="'.$this->folder.'" />'."\n";
        $html .= '<input class="upload-thumb" type="hidden" name="'.$this->field.'-upload_thumb" value="'.$this->thumb.'" />'."\n";
        $html .= '<input class="upload-list" type="hidden" name="'.$this->field.'-upload_list" value="'.$this->list.'" />'."\n";
        $html .= '<input class="upload-call" type="hidden" name="'.$this->field.'-upload_call" value="'.$this->call.'" />'."\n";
        $html .= '</div>';

        $html .= '<div id="'.$this->field.'-image-update-wait" style="display:none">';
        $html .= 'Poczekaj';
        $html .= '</div>';
        if ($this->list) {
            if ($this->value > 0) {
                $html .= '<div id="'.$this->field.'-image-update-image" class="empty-img hide">';
            } else {
                $html .= '<div id="'.$this->field.'-image-update-image" class="empty-img">';
            }
            $html .= 'Brak obrazów';
            $html .= '</div>';
            $html .= '<table class="image-update-image draggable">';

            $html .= "<tr>\n<td class=\"upload-row-image\">";
            // pole deleted
            $atItems = $this->items();
            $atItems['name'] .= '[deleted]';
            $html .= '<input class="upload-deleted" type="hidden" name="'.$atItems['name'].'" value="0" />'."\n";

            $file_value = $this->value;
            if (is_array($file_value)) {
                $file_value = end($file_value);
            }
            // pole fid
            $atItems = $this->items();
            $atItems['name'] .= '[fid]';
            $atItems['value'] = '0';
            $html .= '<input type="hidden" name="'.$atItems['name'].'" value="'.$file_value.'" />'."\n";
            $file = FileDataImage::getById($file_value);
            if ('crm' == qConfig::get('site') && $file) {
                $file->path = '';
            }
            if ($file) {
                $html .= '<img alt="" src="'.$file->getUrlFile($this->thumb).'" id="image-'.$file_value.'"/>';
            } else {
                $html .= '&nbsp;';
            }
            $html .= '</td>'."\n";
            // pole title
            if ($this->disptitle > 0) {
                $atItems = $this->items();
                $atItems['name'] .= $index.'[title]';
                $atItems['value'] = $value['title'];
                $html .= '<td class="upload-row-title"><label for="'.$this->field.'-upload_new_title">Tytuł</label><input type="text" name="'.$atItems['name'].'" value="'.$atItems['value'].'" /></td>'."\n";
            }
            // pole description
            if ($this->dispdesc > 0) {
                $atItems = $this->items();
                $atItems['name'] .= $index.'[description]';
                $atItems['value'] = $value['description'];
                $html .= '<td class="upload-row-desc"><label for="'.$this->field.'-upload_new_desc">Opis</label><input type="text" name="'.$atItems['name'].'" value="'.$atItems['value'].'" /></td>'."\n";
            }
            // akcja usuń
            if ($file) {
                $html .= '<td class="upload-row-actions"><input class="btn" type="button" title="Usuń" value="Usuń" onclick="qAnt.module.upload.rowDelete(this); return false;" /></td>';
            }
            $html .= "</tr>\n";

            $html .= '</table>';
        }

        //$image = FileDataImage::getById($this->value);

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
            qAnt.module.upload.image.up_ie('image');
          });
        });
      </script>
      <![endif]-->
      <!--[if !IE]> -->
      <input style=\"display:none;\" type=\"file\" name=\"file\" id=\"".$this->field."-new_img_input\" multiple />
      <script type=\"text/javascript\">
        $(function(){
          $('#".$this->field."-drop-zone').click(function(e){
            e.preventDefault();
            $('#".$this->field."-new_img_input').click();
          });
          $('#".$this->field."-new_img_input').change(function(e){
            qAnt.module.upload.image.up('image', e, false, false, this);
          });
        });
      </script>
      <!-- <![endif]-->";

        $html .= "</fieldset>\n";
        //$html .= '<input name="'.$this->field.'-js-upload-name" type="hidden" value="'.$this->field.'-upload-name" />';
        //$html .= '<script type="text/javascript">';
        //$html .= '$(\'input[id=upload-name-'.$this->field.']\').change(function() {';
        //$html .= '   $(\''.$this->field.'-photoCover\').val($(this).val());';
        //$html .= '});';
        //$html .= '</script>';
        return $html;
    }
}
