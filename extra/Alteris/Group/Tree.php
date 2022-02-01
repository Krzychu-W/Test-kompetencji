<?php
namespace Alteris\Group;


/**
 * W celu łatwej prezentacji zdecydowałem się, że Tree nie będzie przechowywał \Alteris\Group\Record,
 * ale wyłącznie id i nazwy, co pozwala na łatwą prezentację na ekranie. Po tym względem spreparowany jest obiekt.
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class Tree
{

    /**
     * ID grupy
     *
     * @var integer
     */
    private $id;

    /**
     * Nazwa grupy
     *
     * @var string
     */
    private $name;

    /**
     * Elementy podległe
     *
     * @var array
     */
    private array $children = [];

    /**
     * @param \Alteris\Group\Record $obj
     */
    public function __construct(\Alteris\Group\Record $obj)
    {
        $this->id = $obj->id;
        $this->name = $obj->name;
        foreach ($obj->getChildren() as $item) {
            $this->children[] = new Tree($item);
        }
    }

}

