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
   * Helper to define whether form has needed args.
   * @param FormStateInterface $formState
   * @return bool
   */
  public static function formHasArgs(FormStateInterface $formState) {
    return $formState->has('vr_view') && $formState->has('yaw') && $formState->has('pitch');
  }

}