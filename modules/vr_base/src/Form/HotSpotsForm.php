<?php

namespace Drupal\vr_base\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hotspots form.
 */
class HotSpotsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hotspots_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $vr_view = '', $yaw = '', $pitch = '') {
    $storage = \Drupal::service('entity.manager')->getStorage('vr_view');
    $entity = $storage->load($vr_view);
    $form_state->set('vr_view', $entity);
    $form['yaw'] = [
      '#type' => 'number',
      '#default_value' => $yaw,
      '#title' => $this->t('Yaw'),
      '#description' => $this->t('Set valid "yaw" position.'),
    ];
    $form['pitch'] = [
      '#type' => 'number',
      '#default_value' => $pitch,
      '#title' => $this->t('Pitch'),
      '#description' => $this->t('Set valid "pitch" position.'),
    ];
    $form['variant'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Select action'),
      '#default_value' => 0,
      '#options' => [ 0 => $this->t('Add new'), 1 => $this->t('Change existing') ],
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('phone_number')) < 3) {
      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('Your phone number is @number', array('@number' => $form_state->getValue('phone_number'))));
  }

}