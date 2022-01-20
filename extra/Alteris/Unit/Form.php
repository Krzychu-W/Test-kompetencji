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
        $field->description = 'Nazwa jednostki miary';
        $field->size = 24;
        $field->maxlength = 250;
        $field->label = 'nazwa';
        $field->value = $record->name;
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

    // funkcje walidacji
    public function fieldNameValidate()
    {
        $value = trim($this->name->value);
        if ($value == '') {
            $this->name->error = 'Pole nie może być puste';
            return false;
        }
        // dodać unikalność
        // dodać max długości (odczytać z definicji)

        return true;
    }


}

