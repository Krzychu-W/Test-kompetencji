<?php

namespace Alteris\Group;

/**
 * Edycja rekordu, funkcje walidacji, formularze danych itp
 *
 * dla jednego modelu może być wiele klas edycji
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class Form extends \Alteris\Model\Form
{
    /**
     * Inicjacja formularza
     * @see qForm::init()
     *
     * @param $record
     * @param array $atribs
     */
    public function init(object $record, array $atribs = [])
    {
        //
        $attribs = $this->attribs();
        $attribs->name = 'group';

        $field = $this->FormFieldValue('id');
        $field->value = $record->id;

        $field = $this->FormFieldText('name');
        $field->size = 128;
        $field->maxlength = 128;
        $field->label = 'Nazwa grupy';
        $field->value = $record->name;
        $field->required = true;

        $field = $this->FormFieldSelect('parent_id');

        $options = [];
        $classes = [];
        $gTable = $record->getTable();
        foreach ($gTable->getOptions(0, $record->id) as $key => $item) {
            $options[$key] = $item['label'];
            if ($item['none'] === true) {
                $classes[$key] = 'red-option';
            }
        }
        $field->options = $options;
        $field->classes = $classes;

        $field->label = 'Rodzic';
        $field->value = intval($record->parent_id);
        $field->required = true;

        $actions = $this->FormFieldActions('actions');

        $field = $actions->FormFieldSubmit('submit');
        $field->value = 'Zapisz';
        $field->class = 'btn btn-primary';

        if (!$record->isNew()) {
            $field = $actions->FormFieldAlter('delete');
            $field->value = 'Usuń';
            $field->link = 'group/delete/'.$record->id;
            $field->cancelText = 'Przerwij';
            $field->confirmText = 'Usuń bezpowrotnie';
            $field->class = 'btn';
        }
    }

    // funkcje walidacji w/g nazy pól

    public function fieldNameValidate()
    {
        $value = $this->name->value = trim($this->name->value);
        if ($value == '') {
            $this->name->error = 'Pole nie może być puste';

            return false;
        }
        if (strlen($value) > $this->getRecord()->getTable()->getFields()['name']['size']) {
            $this->name->error = 'Pole jest za długie';

            return false;
        }

        return true;
    }

    public function fieldParent_idValidate() {
        $parent_id = $this->parent_id->value;
        $gTable = $this->getRecord()->getTable();
        foreach ($gTable->getOptions(0, $this->id->value) as $key => $item) {
            if ($key == $parent_id) {
                if ($item['none'] === true) {
                    $this->parent_id->error = 'Tego rodzica wybrać nie możesz';

                    return false;
                }
            }
        }

        return true;
    }



}

