<?php
/**
 * A list of settings to customize themes that applies to all themes.
 */
return array(
    array(
        'label' => esc_html__( 'Play Button', 'ml-slider' ),
        'name' => 'play_button',
        'type' => 'color',
        'default' => '#000',
        'dependencies' => 'pausePlay'
    ),
    array(
        'label' => esc_html__( 'Play Button Hover', 'ml-slider' ),
        'name' => 'play_button_hover',
        'type' => 'color',
        'default' => '#000',
        'dependencies' => 'pausePlay'
    ),
    array(
        'label' => esc_html__( 'Play Button Icon', 'ml-slider' ),
        'name' => 'play_button_icon',
        'type' => 'color',
        'default' => '#fff',
        'dependencies' => 'pausePlay'
    ),
    array(
        'label' => esc_html__( 'Play Button Icon Hover', 'ml-slider' ),
        'name' => 'play_button_icon_hover',
        'type' => 'color',
        'default' => '#fff',
        'dependencies' => 'pausePlay'
    ),
);