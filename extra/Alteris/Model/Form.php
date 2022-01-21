<?php

namespace Alteris\Model;

/**
 * Formularz edycji i walidacji
 *
 * dla jednego modelu może być wiele klas edycji
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class Form extends \qForm
{
    /**
     * Referencja do rekordu
     *
     * @var object
     */
    protected object $record;

    /**
     * dodatkowe parametry
     *
     * @var array
     */
    protected array $param = [];

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->setRecord($params);
    }

    /**
     * Referencja do rekordu
     *
     * @param $record
     */
    public function setRecord(&$record)
    {
        $this->record = $record;
    }

    /**
     * Pobranie rekordu
     *
     * @return object
     */
    public function getRecord():object
    {
        return $this->record;
    }

    /**
     * Wygodny skrót do tabeli modelu
     *
     * @return object
     */
    public function getTable():object
    {
        return $this->record->getTable();
    }

}

