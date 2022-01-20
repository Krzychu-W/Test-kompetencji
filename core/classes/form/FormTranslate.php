<?php
/*
 * Atrybuty zareówno forny jak i pól są dwojga rodzaju zwykłe i sterujące, które zaczynają się od #
 */
class FormTranslate extends qForm
{
    protected $fromLang;
    protected $toLang;

    public function __construct($fromLang, $toLang)
    {
        $this->fromLang = $fromLang;
        $this->toLang = $toLang;
        parent::__construct(array());
    }

    public function addFieldTranslated()
    {
        $this->FormFieldText('aaaa');
        $this->value = 'aaa';
    }

    public function generateFieldFromForm($form, $fields, $objFromLang = false)
    {
        // tabela
        $attribs = $this->attribs();
        $attribs->class = 'translate-form';

        $table = $this->FormFieldTableHeader('table');
        $table->headers = array(
      'Oryginał (język '. qLang::getName($this->fromLang).' )',
      'Tłumaczenie (język '.qLang::getName($this->toLang).')', );
        $table->class = 'table table-translated';

        $lp = 1;
        foreach ($fields as $fieldName => $type) {
            $item = $form->field($fieldName);
            if ($item) {
                $wrapper = $this->FormFieldTableTr('tr-'.$lp);
                $field = $wrapper->addFieldHtml($lp.'-left');
                $field->class = 'trans-original';
                $field->block = $item->htmlLabelSmall();
                if ($objFromLang) {
                    $field->block .= '<div class="oryginal-value">'.$objFromLang->v($fieldName).'</div>';
                } else {
                    $field->block .= '';
                }
                if (1 == $type) {
                    $item->title = '';
                    $item->label = '';
                    $wrapper->setField($item);
                    $f1 = $this->field($fieldName);
                    $field = $f1->value;
                    if (is_object($field) && $field->isField() && $field->isEmpty()) {
                        $new = $field->getSchemaNew();
                        if (isset($new['content'])) {
                            $new['content'] = $objFromLang->v($fieldName);
                            $field->addFieldValue($new);
                        }
                    }
                } else {
                    $field = $wrapper->addFieldHtml($lp.'-right');
                    $field->class = 'trans-translate';
                    $field->block = '';
                }
                ++$lp;
            }
        }
        $table = $this->FormFieldTableFooter('footer');
        $plugins = $form->field('pluginsTabs');
        if ($plugins && 'tabs' == $plugins->type) {
            $this->setField($plugins);
        }
        $action = $this->FormFieldActions('actions');
        $field = $action->FormFieldSubmit('submit');
        $field->value = 'Zapisz';
        //$field->primary = true;
        $field->class = 'btn btn-primary';
    }
}
