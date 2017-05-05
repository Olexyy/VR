<?php

namespace Drupal\vr_base\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

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
    $element = array();

    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $element[$delta] = array(
        '#type' => 'markup',
        '#markup' => 'hello world'//$item->value,
      );
    }

    return $element;
  }

}