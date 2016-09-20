<?php
/**
* @package   yoo_finch
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$image = isset($settings['image']) && $settings['image'] ? $settings['image'] :'';

if(!$image) {
    return;
}

// Title Size
switch ($settings['title_size']) {
    case 'large':
        $title_size = 'uk-heading-large';
        break;
    default:
        $title_size = $settings['title_size'] ? 'uk-' . $settings['title_size']:'';
}

// Content Size
switch ($settings['content_size']) {
    case 'large':
        $content_size = 'uk-text-large';
        break;
    case 'h2':
    case 'h3':
    case 'h4':
        $content_size = 'uk-' . $settings['content_size'];
        break;
    default:
        $content_size = '';
}

// Button: Link
switch ($settings['link_style']) {
    case 'icon-small':
        $button_link = 'uk-icon-small';
        break;
    case 'icon-medium':
        $button_link = 'uk-icon-medium';
        break;
    case 'icon-large':
        $button_link = 'uk-icon-large';
        break;
    case 'icon-button':
        $button_link = 'uk-icon-button';
        break;
    case 'button':
        $button_link = 'uk-button';
        break;
    case 'primary':
        $button_link = 'uk-button uk-button-primary';
        break;
    case 'button-large':
        $button_link = 'uk-button uk-button-large';
        break;
    case 'primary-large':
        $button_link = 'uk-button uk-button-large uk-button-primary';
        break;
    case 'button-link':
        $link_style = 'uk-button uk-button-link';
        break;
    default:
        $button_link = '';
}

switch ($settings['link_style']) {
    case 'icon':
    case 'icon-small':
    case 'icon-medium':
    case 'icon-large':
    case 'icon-button':
        $button_link .= ' uk-icon-' . $settings['link_icon'];
        $settings['link_text'] = '';
        break;
}

// Dropdown Options
$options[] = "'pos' : '" . $settings['drop_position'] . "'";
$options[] = "'mode' : '" . $settings['drop_mode'] . "'";

$options = implode(', ', $options);

?>

<div class="tm-popover-finch <?php echo $settings['class'] ? $settings['class'] : '' ?>">
    <div class="uk-position-relative uk-display-inline-block">

        <img src="<?php echo $image?>" alt="Widgetkit Popover Widget"/>

        <?php foreach ($items as $index => $item): ?>

            <?php

                // Position
                $left  = isset($item['left']) && $item['left'] ? (int) $item['left']:'';
                $top = isset($item['top']) && $item['top'] ? (int) $item['top']:'';

                if ($left !== 0 && !$left || $top !== 0 && !$top) continue;

                $left .= '%';
                $top  .= '%';

                // Media Type
                $attrs  = array('class' => '');
                $width  = $item['media.width'];
                $height = $item['media.height'];

                if ($item->type('media') == 'image') {
                    $attrs['alt'] = strip_tags($item['title']);
                    $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
                    $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';
                }

                $attrs['width']  = ($width) ? $width : '';
                $attrs['height'] = ($height) ? $height : '';

                if (($item->type('media') == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
                    $media = $item->thumbnail('media', $width, $height, $attrs);
                } else {
                    $media = $item->media('media', $attrs);
                }

                $items[$index]['image'] = $media;
            ?>

            <div class="uk-position-absolute uk-hidden-small" style="left:<?php echo $left?>;top:<?php echo $top?>;  " data-uk-dropdown="{<?php echo $options ?>}">

                <span class="tm-popover-item"></span>

                <div class="uk-dropdown-blank uk-panel uk-panel-box">

                    <?php if ($item['link'] && $settings['link'] && $settings['link_popover']) : ?>
                    <a href="<?php echo $item['link']; ?>" class="uk-cover uk-position-cover"></a>
                    <?php endif; ?>

                    <?php if ($item['media'] && $settings['media']) : ?>
                    <div class="uk-panel-teaser">
                        <?php echo $media; ?>
                    </div>
                    <?php endif ?>

                    <?php if ($settings['title']) : ?>
                    <h3 class="<?php echo $title_size ?>"><?php echo $item['title'] ?></h3>
                    <?php endif ?>

                    <?php if ($settings['content']) : ?>
                    <div class="uk-margin <?php echo $content_size ?>">
                        <?php echo $item['content'] ?>
                    </div>
                    <?php endif ?>

                    <?php if ($item['link'] && $settings['link']) : ?>
                    <p><a href="<?php echo $item['link']; ?>" class="<?php echo $button_link; ?>"><?php echo $settings['link_text']; ?></a></p>
                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach ?>
    </div>

    <div class="tm-popover-slideshow-container uk-position-relative uk-visible-small" data-uk-slideshow>

        <ul class="uk-slideshow">
            <?php foreach ($items as $item) : ?>
            <li>
                <div class="uk-panel uk-panel-box">
                    <?php if ($item['media'] && $settings['media']) : ?>
                    <div class="uk-panel-teaser">
                        <?php echo $item['image']; ?>
                    </div>
                    <?php endif ?>

                    <?php if ($settings['title']) : ?>
                    <h3 class="<?php echo $title_size ?>"><?php echo $item['title'] ?></h3>
                    <?php endif ?>

                    <?php if ($settings['content']) : ?>
                    <div class="uk-margin <?php echo $content_size ?>">
                        <?php echo $item['content'] ?>
                    </div>
                    <?php endif ?>

                    <?php if ($item['link'] && $settings['link']) : ?>
                    <p><a href="<?php echo $item['link']; ?>" class="<?php echo $button_link; ?>"><?php echo $settings['link_text']; ?></a></p>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach ?>
        </ul>

        <div class="uk-grid uk-margin uk-flex-center">
            <div><a href="#" class="uk-slidenav uk-slidenav-previous" data-uk-slideshow-item="previous" draggable="false"></a></div>
            <div><a href="#" class="uk-slidenav uk-slidenav-next" data-uk-slideshow-item="next" draggable="false"></a></div>
        </div>

    </div>

</div>
