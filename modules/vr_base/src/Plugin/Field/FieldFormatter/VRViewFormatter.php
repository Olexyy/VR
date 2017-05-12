<?php

namespace Drupal\vr_base\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\file\Entity\File;

/**
 * Plugin implementation of the 'VRView' formatter.
 *
 * @FieldFormatter(
 *   id = "VRView",
 *   label = @Translation("VR view image"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class VRViewFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $settings = $this->getSettings();

    $summary[] = t('Displays the VR view image.');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $a = 1;
    foreach ($items as $delta => $item) {
      $entity = $item->getEntity();
      $is_stereo = $entity->field_is_stereo->value;
      $value = $item->getValue();
      $file_uri = File::load($value['target_id'])->getFileUri();
      $image_uri = file_create_url($file_uri);
      $start_image = file_create_url('public://pics/blank.png');
      $js_settings = [
        'start_image' => $start_image,
        'source' => $image_uri,
        'is_stereo' => $is_stereo,
      ];
      $element[$delta]['widget'] = [
        '#type' => 'item',
        '#title' => '<div id="vrview"></div>
                    <div class="vrview-position position">
                        <div class="position-title">Yaw: </div>
                        <div class="position-yaw value" id="yaw-value">0</div>
                        <div class="position-title">Pitch: </div>
                        <div class="position-pitch value" id="pitch-value">0</div>
                    </div>
                    <a id="modal-button" class="use-ajax" data-dialog-type="modal" href="/hotspot/'.$entity->id->value.'/0/0">'.t('Hot spots').'</a>',
        '#attached' => [
          'library' => [ 'vr_base/vr_library' ],
          'drupalSettings' => [ 'vr_base' => $js_settings ],
        ],
        '#description' => 'Hello kitty!',
      ];
      $element[$delta]['link'] = [
        '#type' => 'link',
        '#title' => 'Open Modal',
        '#url' => "/hotspot/{$entity->id->value}/0/0",
        '#attributes' => [
          'class' => [ 'use-ajax', 'button', ],
          ],
        '#attached' => [ // Attach the library for pop-up dialogs/modals.
          'library' => [ 'core/drupal.dialog.ajax' ],
        ],
      ];
    }

    return $element;
  }

}