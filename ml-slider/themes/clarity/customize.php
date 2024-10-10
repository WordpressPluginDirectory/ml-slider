<?php
/**
 * Keep this file as is. 
 * You can optionally add array() values to allow to customize theme design
 * See themes/customize.php as reference
 */

return array(
    array(
        'label' => esc_html__( 'Arrows', 'ml-slider' ),
        'name' => 'arrows_color',
        'type' => 'color',
        'default' => '#fff',
        'dependencies' => 'links'
    ),
    array(
        'label' => esc_html__( 'Arrows Hover', 'ml-slider' ),
        'name' => 'arrows_color_hover',
        'type' => 'color',
        'default' => '#07383C',
        'dependencies' => 'links'
    ),
    array(
        'label' => esc_html__( 'Arrows Icon', 'ml-slider' ),
        'name' => 'arrows_icon',
        'type' => 'color',
        'default' => '#50585C',
        'dependencies' => 'links'
    ),
    array(
        'label' => esc_html__( 'Arrows Icon Hover', 'ml-slider' ),
        'name' => 'arrows_icon_hover',
        'type' => 'color',
        'default' => '#fff',
        'dependencies' => 'links'
    ),
    array(
        'label' => esc_html__( 'Navigation', 'ml-slider' ),
        'name' => 'navigation_color',
        'type' => 'color',
        'default' => '#07383C',
        'dependencies' => 'navigation'
    ),
    array(
        'label' => esc_html__( 'Navigation Hover', 'ml-slider' ),
        'name' => 'navigation_color_hover',
        'type' => 'color',
        'default' => '#07383C',
        'dependencies' => 'navigation'
    ),
    array(
        'label' => esc_html__( 'Navigation Active', 'ml-slider' ),
        'name' => 'navigation_color_active',
        'type' => 'color',
        'default' => '#07383C',
        'dependencies' => 'navigation'
    ),
    array(
        'label' => esc_html__( 'Caption background', 'ml-slider' ),
        'name' => 'caption_background',
        'type' => 'color',
        'default' => '#fff'
    ),
    array(
        'label' => esc_html__( 'Caption text', 'ml-slider' ),
        'name' => 'caption_text_color',
        'type' => 'color',
        'default' => '#000'
    ),
    array(
        'label' => esc_html__( 'Caption links', 'ml-slider' ),
        'name' => 'caption_links_color',
        'type' => 'color',
        'default' => '#F9F9F9'
    )
);
