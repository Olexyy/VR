<?php

namespace Drupal\vr_base;
use Drupal\Core\Form\FormStateInterface;

class VrBase {

  /**
   * ECK entity type for vr_view entity.
   */
  const EntityTypeVRView = 'vr_view';

  /**
   * ECK entity type for vr_hotspot entity.
   */
  const entityTypeVRHotspot = 'vr_hotspot';

  /**
   * ECK entity bundle for vr_hotspot entity.
   */
  const entityBundleBaseVRView = 'base';

  /**
   * ECK entity bundle for vr_hotspot entity.
   */
  const entityBundleBaseVRHotspot = 'base_hotspot';
  /**
   * Wrapper for modal form.
   */
  const formWrapper = 'vr_hotspot_form_wrapper';

  /**
   * Helper to define whether form is ajax.
   * @param FormStateInterface $formState
   * @return bool
   */
  public static function formIsAjax(FormStateInterface $formState) {
    return $formState->has('ajax');
  }

  /**
   * Helper to define whether form has needed args for create.
   * @param FormStateInterface $formState
   * @return bool
   */
  public static function formHasViewArgs(FormStateInterface $formState) {
    return $formState->has('vr_view') && $formState->has('yaw') && $formState->has('pitch');
  }

  /**
   * Helper to define whether form has needed args for edit form.
   * @param FormStateInterface $formState
   * @return bool
   */
  public static function formHasHotspotArgs(FormStateInterface $formState) {
    return $formState->has('yaw') && $formState->has('pitch');
  }

  /**
   * Helper to get action of from.
   * @param FormStateInterface $formState
   * @return string
   */
  public static function formAction(FormStateInterface $formState) {
    if($formState->has('action')) {
      return $formState->get('action');
    }
    return '';
  }

}