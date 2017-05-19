<?php

namespace Drupal\vr_base\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\eck\Entity\EckEntity;
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
    $summary = [];
    $settings = $this->getSettings();
    $summary[] = $this->t('Displays the VR view image.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    foreach ($items as $delta => $item) {
      $entity = $item->getEntity();
      $js_settings = $this->jsSettings($entity);

      $element[$delta]['widget'] = [
        '#type' => 'item',
        '#title' => '<div id="vrview"></div>
                    <div class="vrview-position position">
                        <div class="position-title">Yaw: <span class="position-yaw value" id="yaw-value">0</span></div>
                        <div class="position-title">Pitch: <span class="position-pitch value" id="pitch-value">0</span></div>
                    </div>
                    <a id="modal-button-create" class="use-ajax" href="/hotspot/create/'.$entity->id->value.'/0/0">'.t('Add new').'</a>'
                    .$this->hotspotsLinks($entity),
        '#attached' => [
          'library' => [ 'vr_base/vr_library', 'core/drupal.dialog.ajax' ],
          'drupalSettings' => [ 'vr_base' => $js_settings ],
        ],
        '#markup' => t('Add new or edit existing hotspots, using current pitch and yaw.'),
      ];
    }
    return $element;
  }

  // TODO 1) TEST implementation; 2)MERGE 'LINK' RENERABLE!!! 3) register own theme element!!!
  private function jsSettings(EntityInterface $entity) {
    $start_image_uri = file_create_url('public://pics/blank.png');
    $vr_view_name = $entity->title->value.'_'.$entity->id->value;
    $js_settings = [
      'start_image' => $start_image_uri,
      'start_view' => $vr_view_name,
      'views' => [],
    ];
    $this->vrViewToJsSettings($entity, $js_settings);
    $hotspots = $entity->field_vr_hotspots->referencedEntities();
    foreach ($hotspots as $hotspot) {
      if($vr_view = $hotspot->field_vr_view_target->entity) {
        $this->vrViewToJsSettings($vr_view, $js_settings);
      }
    }
    return $js_settings;
  }

  private function vrViewToJsSettings(EntityInterface $entity, array &$js_settings) {
    $vr_view_name = $entity->title->value.'_'.$entity->id->value;
    $is_stereo = $entity->field_is_stereo->value;
    $file = $entity->field_image->entity;
    $image_uri = file_create_url($file->getFileUri());
    $js_settings['views'][$vr_view_name] = [
      'source' => $image_uri,
      'is_stereo' => $is_stereo,
      'hotspots' => $this->hotspotsToJsSettings($entity),
    ];
  }

  private function hotspotsToJsSettings(EntityInterface $entity) {
    $hotspot_settings = [];
    $hotspots = $entity->field_vr_hotspots->referencedEntities();
    foreach ($hotspots as $hotspot) {
      if($vr_view = $hotspot->field_vr_view_target->entity) {
        $vr_view_name = $vr_view->title->value.'_'.$vr_view->id->value.'_'.$hotspot->title->value.'_'.$hotspot->id->value;
        $yaw = $hotspot->field_yaw->value;
        $pitch = $hotspot->field_pitch->value;
        $radius = $hotspot->field_radius->value;
        $distance = $hotspot->field_distance->value;
        $hotspot_settings[$vr_view_name] = [
          'pitch' => $pitch,
          'yaw' => $yaw,
          'radius' => $radius,
          'distance' => $distance,
        ];
      }
    }
    return $hotspot_settings;
  }

  private function hotspotsLinks(EntityInterface $entity) {
    $html = '';
    $hotspots = $entity->field_vr_hotspots->referencedEntities();
    foreach ($hotspots as $hotspot) {
      if($vr_view = $hotspot->field_vr_view_target->entity) {
        $hotspot_id = $hotspot->id->value;
        $hotspot_name = $hotspot->title->value;
        $vr_view_name = $vr_view->title->value;
        $name = $hotspot_name .'('. $vr_view_name .')';
        $html .= Link::fromTextAndUrl($name, Url::fromUri("internal:/hotspot/edit/{$hotspot_id}/0/0", [ 'attributes' => ['id' => 'modal-button-edit', 'class' => ['modal-button-edit', 'use-ajax', 'button',] ]]))->toString();
      }
    }
    return $html;
  }
}