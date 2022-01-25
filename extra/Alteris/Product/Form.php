<?php

namespace Alteris\Product;

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
        $attribs->name = 'product';

        $field = $this->FormFieldValue('id');
        $field->value = $record->id;

        $field = $this->FormFieldText('indeks');
        $field->size = 24;
        $field->maxlength = 24;
        $field->label = 'Kod produktu';
        $field->value = $record->indeks;
        $field->required = true;

        $field = $this->FormFieldText('name');
        $field->size = 128;
        $field->maxlength = 128;
        $field->label = 'Nazwa materiału';
        $field->value = $record->name;
        $field->required = true;

        $field = $this->FormFieldSelect('unit_id');
        $uTable = new \Alteris\Unit\Table();

        $field->options = $uTable->getOptions('- wybierz jednostkę miary');
        $field->label = 'Jednostka miary';
        $field->value = $record->unit_id;
        $field->required = true;


        $actions = $this->FormFieldActions('actions');

        $field = $actions->FormFieldSubmit('submit');
        $field->value = 'Zapisz';
        $field->class = 'btn btn-primary';

        if (!$record->isNew()) {
            $field = $actions->FormFieldAlter('delete');
            $field->value = 'Usuń';
            $field->link = 'product/delete/'.$record->id;
            $field->cancelText = 'Przerwij';
            $field->confirmText = 'Usuń bezpowrotnie';
            $field->class = 'btn';
        }
    }

    // funkcje walidacji w/g nazy pól

    public function fieldIndeksValidate()
    {
        $value = $this->indeks->value = trim($this->indeks->value);
        if ($value == '') {
            $this->indeks->error = 'Pole nie może być puste';

            return false;
        }
        if (strlen($value) > $this->getRecord()->getTable()->getFields()['indeks']['size']) {
            $this->indeks->error = 'Pole jest za długie';

            return false;
        }
        // unikalność
        $idByIndeks = $this->getRecord()->getTable()->getIdByIndeks($value);
        if ($idByIndeks && (
              $this->getRecord()->isNew()
              || (!$this->getRecord()->isNew() && $this->getRecord()->id != $idByIndeks)
            )) {
            $this->indeks->error = 'Ten kod jest już używany';

            return false;
        }

        return true;
    }

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

    public function fieldUnit_idValidate()
    {
        $value = intval($this->unit_id->value);
        if ($value == 0) {
            $this->unit_id->error = 'Pole nie może być puste';

            return false;
        }

        return true;
    }

}
