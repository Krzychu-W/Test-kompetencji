<?php


/**
 * Parsowanie tagów
 * 
 * @author Krzysztof Wałek <krzysztof.w@investmag.pl>
 */
class HtmlTag
{
    protected $type;
    protected $attributes = array();
    protected $value = '';

    public static $voidElement = [
      'area', 'base', 'br', ' col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 
      'param', 'source', 'track', 'wbr',
      ];

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function __set($name, $value)
    {
        $this->setAttr($name, $value);
    }

    public function setAttr($name, $value)
    {
        $value = str_replace('"', '', $value);
        $this->attributes[$name] = $value;
    }

    public function setAttrbs($table)
    {
        foreach ($table as $name => $value) {
            $this->setAttr($name, $value);
        }
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function html()
    {
        $result = '<'.$this->type;
        foreach ($this->attributes as $attr => $value) {
            $result .= ' '.$attr.'="'.$value.'"';
        }
        if (in_array($this->type, self::$voidElement)) {
            $result .= ' />';
        } else {
            $result .= '>'.$this->value.'</'.$this->type.'>';
        }

        return $result;
    }
}
