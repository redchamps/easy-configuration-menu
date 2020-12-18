<?php
namespace RedChamps\EasyConfigMenu\Plugin\Backend\Model;

use Magento\Backend\Model\Menu\Item;
use RedChamps\EasyConfigMenu\Model\Config;

class MenuItem
{
    /*
    * Force allowed ACL for Settings menu
    * */
    public function afterIsAllowed(Item $subject, $result)
    {
        $menuItemData = $subject->toArray();
        if(isset($menuItemData["resource"]) && $menuItemData["resource"] == Config::MENU_ID) {
            return true;
        }
        return $result;
    }
}
