<?php

class FormFieldAlias extends qFormField
{
    public $tagAttrib = array('onChange');

    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_ALIAS, $formAttr);
        $this->setHtmlAttrib('id', 'class');
    }

    public function html(): string
    {
        $html = '<fieldset';
        $html .= $this->getHtmlAttr($this->items());
        $html .= ">\n";
        if ($this->hasItem('title')) {
            $html .= "<legend>{$this->item('title')}</legend>\n";
        }
        $html .= $this->htmlDescription();

        $html .= "<table id=\"alias-table\">\n";
        $html .= "<tr>\n";
        $html .= "<th>Alias główny</th>\n";
        $html .= "<th>Domena</th>\n";
        $html .= "<th>Alias</th>\n";
        $html .= "</tr>\n";
        $count = 0;
        foreach ($this->rows as $key => $alias) {
            $html .= "<tr id=\"alias-tr-{$count}\">\n";
            $checked = '';
            if (1 == $alias['main']) {
                $checked = ' checked="checked"';
            }
            $html .= '<td><input class="main" type="radio" name="'.$this->name.'['.$count.'][main]" value="1"'.$checked.' onclick="aliasMain('.$count.');" /></td>'."\n";

            $html .= '<td><select class="domain" id="'.$this->id.'-'.$count.'-domain" name="'.$this->name.'['.$count.'][domain]" onChange="'.$this->onChange.'">'."\n";


            $html .= "</select></td>\n";
            $html .= '<td><input type="text" name="'.$this->name.'['.$count.'][alias]" value="'.$alias['alias'].'" maxlength="100" /></td>'."\n";

            $html .= '<td><input type="button" name="'.$this->name.'['.$count.'][button]" value="Usuń" onclick="aliasDelete('.$count.')" />'."\n";
            $html .= '<input type="hidden" name="'.$this->name.'['.$count.'][aid]" value="'.$alias['aid'].'" /></td>'."\n";
            $html .= "</tr>\n";
            ++$count;
        }

        $html .= "</table>\n";
        $html .= "<hr class=\"alias-hr\" />\n";

        //$html .= '<div class="form-description">Dodaj nowy adres bez rozszerzenia .html</div>'."\n";
        $html .= "<table>\n";
        $html .= "<tr>\n";
        $html .= '<td><select id="alias-domain" name="alias[domain]">'."\n";

        $html .= "</select></td>\n";
        $html .= '<td><input id="alias-alias" type="text" name="alias[alias]" value="" maxlength="100" /></td>'."\n";
        $html .= '<td><input type="button" name="alais[button]" value="Dodaj" onclick="aliasAddNew();" />'."\n";
        $html .= '<input id="alias-count" type="hidden" name="alias[count]" value="'.$count.'" /></td>'."\n";
        $html .= "</tr>\n";
        $html .= "</table>\n";

        $html .= "</fieldset>\n";

        return $html;
    }
}
