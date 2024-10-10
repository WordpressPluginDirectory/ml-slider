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
        'default' => '#d48931',
        'dependencies' => 'links'
    ),
    array(
        'label' => esc_html__( 'Arrows Hover', 'ml-slider' ),
        'name' => 'arrows_color_hover',
        'type' => 'color',
        'default' => '#174d51',
        'dependencies' => 'links'
    ),
    array(
        'label' => esc_html__( 'Navigation', 'ml-slider' ),
        'name' => 'navigation_color',
        'type' => 'color',
        'default' => '#d48931',
        'dependencies' => 'navigation'
    ),
    array(
        'label' => esc_html__( 'Navigation Hover', 'ml-slider' ),
        'name' => 'navigation_color_hover',
        'type' => 'color',
        'default' => '#174d51',
        'dependencies' => 'navigation'
    ),
    array(
        'label' => esc_html__( 'Navigation Active', 'ml-slider' ),
        'name' => 'navigation_color_active',
        'type' => 'color',
        'default' => '#174d51',
        'dependencies' => 'navigation'
    ),
    array(
        'label' => esc_html__( 'Caption background', 'ml-slider' ),
        'name' => 'caption_background',
        'type' => 'color',
        'default' => 'rgba(12,37,33,0.8)'
    ),
    array(
        'label' => esc_html__( 'Caption text', 'ml-slider' ),
        'name' => 'caption_text_color',
        'type' => 'color',
        'default' => '#ffffff'
    ),
    array(
        'label' => esc_html__( 'Caption links', 'ml-slider' ),
        'name' => 'caption_links_color',
        'type' => 'color',
        'default' => '#faf461'
    ),
    array(
        'label' => esc_html__( 'Button', 'ml-slider' ),
        'name' => 'image_button_color',
        'type' => 'color',
        'default' => '#D48931'
    ),
    array(
        'label' => esc_html__( 'Button Hover', 'ml-slider' ),
        'name' => 'image_button_hover',
        'type' => 'color',
        'default' => '#a86619'
    )
);
