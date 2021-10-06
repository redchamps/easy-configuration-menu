<?php
namespace RedChamps\EasyConfigMenu\Plugin\Backend\Model;

use Magento\Backend\Model\Menu\Item;
use RedChamps\EasyConfigMenu\Model\IsAllowedMenuChildren;
use RedChamps\EasyConfigMenu\Model\Config;

class MenuItem
{
    private $isAllowedMenuChildren;

    public function __construct(IsAllowedMenuChildren $isAllowedMenuChildren)
    {
        $this->isAllowedMenuChildren = $isAllowedMenuChildren;
    }

    /*
    * Force allowed ACL for Settings menu
    * */
    public function afterIsAllowed(Item $subject, $result)
    {
        $menuItemData = $subject->toArray();
        if (($menuItemData["resource"] ?? '') === Config::MENU_ID) {
            return $this->isAllowedMenuChildren->execute($subject);
        }
        return $result;
    }
}
