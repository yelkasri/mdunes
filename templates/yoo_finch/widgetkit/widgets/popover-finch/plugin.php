<?php
/**
* @package   yoo_finch
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

return array(

    'name' => 'widget/popover-finch',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'      => 'popover-finch',
        'label'     => 'Popover Finch',
        'core'      => false,
        'icon'      => 'plugins/widgets/popover-finch/widget.svg',
        'view'      => 'plugins/widgets/popover-finch/views/widget.php',
        'item'      => array('title', 'content', 'media', 'left', 'right'),
        'fields' => array(
            array(
                'type' => 'text',
                'name' => 'top',
                'label' => 'Top'
            ),
            array(
                'type' => 'text',
                'name' => 'left',
                'label' => 'Left'
            )
        ),
        'settings'  => array(

            'image'         => '',
            'drop_position' => 'right-center',
            'drop_mode'     => 'hover',
            'link_popover'  => false,
            'class'         => '',

            'media'         => true,
            'image_width'   => 'auto',
            'image_height'  => 'auto',

            'title'         => true,
            'content'       => true,
            'link'          => true,
            'link_style'    => 'text',
            'title_size'    => 'h3',
            'link_text'     => 'more',
            'content_size'  => '',
        )

    ),

    'events' => array(

        'init.site' => function($event, $app) {

        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('popover-finch.edit', 'plugins/widgets/popover-finch/views/edit.php', true);
        }

    )

);
