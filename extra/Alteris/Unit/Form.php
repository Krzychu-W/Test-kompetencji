<?php

namespace Alteris\Unit;

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
        $attribs->name = 'unit';

        $field = $this->FormFieldValue('id');
        $field->value = $record->id;

        $field = $this->FormFieldText('name');
        $field->description = 'Nazwa jednostki miary<br>
<i>Dopuszczalny rozmiar to 24, pole celowo większe, żeby pokazać walidację<br>
Dodatkowo walidacja nie dopuszcza do duplikacji nazw';
        $field->size = 24;
        $field->maxlength = 250;
        $field->label = 'Nazwa jednostki';
        $field->value = $record->name;
        $field->required = true;

        $field = $this->FormFieldText('short');
        $field->size = 4;
        $field->maxlength = 4;
        $field->label = 'Skrót nazwy';
        $field->value = $record->short;
        $field->required = true;

        $actions = $this->FormFieldActions('actions');

        $field = $actions->FormFieldSubmit('submit');
        $field->value = 'Zapisz';
        $field->class = 'btn btn-primary';

        if (!$record->isNew()) {
            $field = $actions->FormFieldAlter('delete');
            $field->value = 'Usuń';
            $field->link = 'unit/delete/'.$record->id;
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
        // unikalność
        $idByName = $this->getRecord()->getTable()->getIdByName($value);
        if (($this->getRecord()->isNew() && $idByName)) {
            $this->name->error = 'Ta nazwa jest już zajęta';

            return false;
        }
        else if (!$this->getRecord()->isNew() && $idByName && $this->getRecord()->id != $idByName) {
            $this->name->error = 'Ta nazwa jest już zajęta';

            return false;
        }

        return true;
    }

    public function fieldShortValidate()
    {
        $value = $this->short->value = trim($this->short->value);
        if ($value == '') {
            $this->short->error = 'Pole nie może być puste';

            return false;
        }
        if (strlen($value) > $this->getRecord()->getTable()->getFields()['short']['size']) {
            $this->short->error = 'Pole jest za długie';

            return false;
        }

        return true;
    }


}

