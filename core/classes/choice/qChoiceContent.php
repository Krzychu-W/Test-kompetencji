<?php

class qChoiceContent extends qItems {
    
    public function __construct() {
        $init = [
          'question' => '',
          'items' => [],
          'description' => '',
        ];
        parent::__construct($init);
        
    }

    public function setQuestion($question)
    {
        $this->question = $question;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function addItem($value, $link, $class = false)
    {
        $item = new qItems();
        $item->value = $value;
        $item->link = $link;
        $item->class = $class;
        $this->addItemArray('items', $item);
    }

    public function get()
    {
        $block = new qTemplate();
        $block->question = $this->question;
        $block->items = $this->items;
        $block->description = $this->description;

        return $block->render('choice/content');
    }
}
