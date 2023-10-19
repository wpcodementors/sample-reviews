<?php

namespace Wpdsr;
defined( 'ABSPATH' ) || exit;

class FieldText {

  public function __construct( $field, $value ){

    // input type

    $type = 'text';
    $min = '';

    if( ! empty( $field['attr']['type'] ) ){
      $type = $field['attr']['type'];
    }

    if( ! empty( $field['attr']['min'] ) ){
      $min = $field['attr']['min'];
    }

    // output -----

    // simple ar advanced input

    if( empty($field['before']) && empty($field['after']) && empty($field['icon']) ){

      echo '<input class="wpdsr-form-control wpdsr-form-input" name="wpdsr['. esc_attr($field['id']) .']" id="wpdsr['. esc_attr($field['id']) .']" type="'. esc_attr($type) .'" min="'. esc_attr($min) .'" value="'. esc_html($value) .'">';

    } else {

      echo '<div class="form-group has-addons has-addons-prepend has-addons-append has-icon has-icon-left">';

        if( ! empty($field['before']) ){
          echo '<span class="form-addon form-addon-prepend">'. esc_attr($field['before']) .'</span>';
        }

        echo '<div class="form-control">';
          echo '<input class="wpdsr-form-control wpdsr-form-input" name="wpdsr['. esc_attr($field['id']) .']" id="wpdsr['. esc_attr($field['id']) .']" type="'. esc_attr($type) .'" value="'. esc_html($value) .'">';

          if( ! empty($field['icon']) ){
            echo '<span class="wpdsr-icon wpdsr-icon-clone">'. esc_attr($field['icon']) .'</span>';
          }

        echo '</div>';

        if( ! empty($field['after']) ){
          echo '<span class="form-addon form-addon-append">'. esc_attr($field['after']) .'</span>';
        }

      echo '</div>';

    }

    if( ! empty($field['sub_desc']) ){
      echo '<p>'. esc_html($field['sub_desc']) .'</p>';
    }

  }

}
