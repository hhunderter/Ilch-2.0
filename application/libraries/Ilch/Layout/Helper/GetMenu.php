<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Ilch\Layout\Helper;

use Ilch\Layout\Base as Layout;
use Ilch\Layout\Helper\Menu\Mapper as MapperHelper;
use Modules\Admin\Mappers\Menu as MenuMapper;

class GetMenu
{
    public const DEFAULT_OPTIONS = [
        'menus' => [
            'ul-class-root' => 'list-unstyled ilch_menu_ul',
            'ul-class-child' => 'list-unstyled ilch_menu_ul',
            'li-class-root' => '',
            'li-class-root-nesting' => '',
            'li-class-child' => '',
            'li-class-active' => '',
            'a-class'          => '',
            'span-class'       => '',
            'allow-nesting' => true,
        ],
        'boxes' => [
            'render' => true,
        ],
    ];

    /** @var Layout */
    private Layout $layout;

    /**
     * Injects the layout.
     *
     * @param Layout $layout
     */
    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Gets the menu for the given position.
     *
     * @param int $menuId
     * @param string $tpl
     * @param array $options
     * @return string
     */
    public function getMenu(int $menuId, string $tpl = '', array $options = []): string
    {
        $helperMapper = new MapperHelper($this->layout);
        $menuMapper = new MenuMapper();
        $menu = $helperMapper->getMenu($menuMapper->getMenuIdForPosition($menuId) ?? 1);

        return $menu->getItems($tpl, array_replace_recursive(self::DEFAULT_OPTIONS, $options));
    }
}
