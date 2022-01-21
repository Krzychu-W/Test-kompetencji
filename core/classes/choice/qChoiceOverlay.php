<?php
/**

 * @author Krzysztof WaĹ‚ek
 */
class qChoiceOverlay extends qItems
{
    protected $checkbox = [];

    /**
     * Inicjacja pustymi zmiennymi.
     */
    public function init()
    {
        $this->question = '';
        $this->items = [];
        $this->description = '';
        $this->danger = false;
        $this->success = false;
    }

    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Ustawienie colormoxa z ostrzeĹĽenim.
     */
    public function danger()
    {
        $this->danger = true;
    }

    /**
     * Wstawienie opisu.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Dodanie przycisku zwykĹ‚ego.
     *
     * @param string $value - tekst na przycisku
     * @param string $link  - link (ajaxowy)
     * @param string $class - klasa
     * @param string $param - dodatkowe parametry
     */
    public function addItem($value, $link, $class = false, $param = false)
    {
        $this->addItem_($value, $link, $class, $param, false, false);
    }

    /**
     * Dodanie przycisku z klasÄ… success.
     *
     * @param string $value - tekst na przycisku
     * @param string $link  - link (ajaxowy)
     * @param string $class - klasa
     * @param string $param - dodatkowe parametry
     */
    public function addItemSuccess($value, $link, $class = false, $param = false)
    {
        $this->addItem_($value, $link, $class, $param, false, true);
    }

    /**
     * Dodanie przycisku z klasÄ… danger.
     *
     * @param string $value - tekst na przycisku
     * @param string $link  - link (ajaxowy)
     * @param string $class - klasa
     * @param string $param - dodatkowe parametry
     */
    public function addItemDanger($value, $link, $class = false, $param = false)
    {
        $this->addItem_($value, $link, $class, $param, true, false);
    }

    public function addCheckbox($key, $label)
    {
        $this->checkbox[$key] = $label;
    }

    /**
     * Generowanie html.
     *
     * @return string
     */
    public function html()
    {
        $items = $this->items;
        if (count($this->checkbox) > 0) {
            foreach ($items as &$item) {
                $item->onclick = "qAnt.ajax.formElement('#form-field-choice-wrapper','{$item->link}')";
            }
        } else {
            foreach ($items as &$item) {
                if ($item->param) {
                    $item->onclick = "qAnt.ajax.load('{$item->link}','{$item->param}')";
                } else {
                    $item->onclick = "qAnt.ajax.load('{$item->link}')";
                }
            }
        }
        $this->items = $items;
        $block = new qTemplate();
        $block->question = $this->question;
        $block->items = $this->items;
        $block->description = $this->description;
        $block->danger = $this->danger;
        $block->checkbox = $this->checkbox;

        return $block->render('choice/overlay');
    }

    /**
     * Dodanie przycisku.
     *
     * @param string $value   - tekst na przycisku
     * @param string $link    - link (ajaxowy)
     * @param string $class   - klasa
     * @param string $param   - dodatkowe parametry
     * @param bool   $danger  - czy przycisk z ostrzeĹĽenim
     * @param bool   $success - czy przycisk a sukcesem
     */
    protected function addItem_($value, $link, $class, $param, $danger, $success)
    {
        $item = new qItems();
        $item->value = $value;
        $item->link = $link;
        $item->class = $class;
        $item->danger = $danger;
        $item->success = $success;
        $item->param = $param;
        $this->addItemArray('items', $item);
    }
}
