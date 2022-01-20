<?php

namespace Alteris\Model;

/**
 * Edycja rekordu, funkcje walidacji, formularze danych itp
 *
 * dla jednego modelu może być wiele klas edycji
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class Edit
{

    /**
     * Referencja do tabeli modelu
     *
     * @var object
     */
    protected object $table;


    /**
     * Referencja do rekordu
     *
     * @var object
     */
    protected object $record;

    public function __construct(&$record)
    {
        $this->record = $record;
        $this->table = $record->getTable();
    }

    /**
     * Formularz edycji może zawierać wiele typów
     *
     * Domyślnie pusty, gdyż Edit może służyć tylko do walidacji rekordu.
     *
     * @param string $type
     * @return array
     */
    public function form($type = ''):array
    {

        return [];
    }

}
