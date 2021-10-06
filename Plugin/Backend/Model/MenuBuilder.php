<?php
namespace RedChamps\EasyConfigMenu\Plugin\Backend\Model;

use Psr\Log\LoggerInterface;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Builder;
use RedChamps\EasyConfigMenu\Model\Config;
use Magento\Config\Model\Config\Structure;
use Magento\Backend\Model\Menu\ItemFactory;

class MenuBuilder
{
    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var Structure
     */
    private $configStructure;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ItemFactory $itemFactory,
        Structure $configStructure,
        LoggerInterface $logger
    ) {
        $this->itemFactory = $itemFactory;
        $this->configStructure = $configStructure;
        $this->logger = $logger;
    }

    /**
     * @param Builder $subject
     * @param Menu $menu
     *
     * @return Menu
     */
    public function afterGetResult(Builder $subject, Menu $menu)
    {
        try {
            $menu = $this->addMenuItems($menu);
        } catch (\Exception $e) {
            $this->logger->critical(
                "Error while generating Easy Config Menu: ".$e->getMessage()." Trace: ".$e->getTraceAsString()
            );
        }

        return $menu;
    }

    /**
     * @param Menu $menu
     *
     * @return Menu
     *
     * @throws \Exception
     */
    protected function addMenuItems(Menu $menu)
    {
        $item = $menu->get(Config::MENU_ID);
        if (!$item) {
            return $menu;
        }

        foreach ($this->configStructure->getTabs() as $tab) {
            if (!$tab->isVisible()) continue;
            $itemId = $tab->getId() . '::container';
            /** @var \Magento\Backend\Model\Menu\Item $module */
            $tabMenuItem = $this->prepareMenuItem(
                $itemId,
                $tab->getLabel()->getText(),
                "RedChamps_EasyConfigMenu::item"
            );
            $menu->add($tabMenuItem, Config::MENU_ID, 1);
            foreach ($tab->getChildren() as $section) {
                $childMenuItem = $this->prepareMenuItem(
                    $section->getId(),
                    $section->getLabel()->getText(),
                    $section->getData()["resource"],
                    'adminhtml/system_config/edit/section/' . $section->getId()
                );
                $menu->add($childMenuItem, $itemId, 1);
            }
        }
        return $menu;
    }

    /**
     * @param $id
     * @param $title
     * @param $module
     * @param $resource
     * @param string $action
     * @return Menu\Item
     */
    protected function prepareMenuItem(
        $id,
        $title,
        $resource,
        $action = "",
        $module = "RedChamps_EasyConfigMenu"
    ) {
        $data = [
            'id'       => $id,
            'title'    => $this->formatTitle($title),
            'module'   => $module,
            'resource' => $resource
        ];
        if ($action) {
            $data["action"] = $action;
        }
        return $this->itemFactory->create(
            [
                'data' => $data
            ]
        );
    }

    /**
     * @param string $title
     * @return string
     */
    protected function formatTitle(string $title): string
    {
        return preg_match('/^.{1,50}\b/s', $title, $match)?$match[0]:$title;
    }
}
