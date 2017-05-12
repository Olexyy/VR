<?php

namespace Drupal\vr_base\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;

/**
 * ModalFormExampleController class.
 */
class ModalFormHotSpotController extends ControllerBase {

   /**
    * The form builder.
    * @var \Drupal\Core\Form\FormBuilder
    */
   protected $formBuilder;

   /**
    * The ModalFormExampleController constructor.
    * @param \Drupal\Core\Form\FormBuilder $formBuilder
    * The form builder.
    */
   public function __construct(FormBuilder $formBuilder) {
     $this->formBuilder = $formBuilder;
   }

   /**
    * {@inheritdoc}
    * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
    * The Drupal service container.
    * @return static
    */
   public static function create(ContainerInterface $container) {
     return new static($container->get('form_builder'));
   }

   /**
    * @param $vr_view
    * @param $yaw
    * @param $pitch
    * @return AjaxResponse
    * Callback for opening the modal form.
    * See more at: https://www.mediacurrent.com/blog/loading-and-rendering-modal-forms-drupal-8#sthash.a1xRiRVP.dpuf
    */
   public function createHotSpotModalForm($vr_view, $yaw, $pitch) {
     $response = new AjaxResponse();
     // Get the modal form using the form builder.
     //$modal_form = $this->formBuilder->getForm('Drupal\modal_form_example\Form\ModalForm');
     //$this->entityFormBuilder()->getForm($entity);
     $entityStorage = \Drupal::service('entity.manager')->getStorage('vr_hotspot');
     $entity = $entityStorage->create(['type' => 'base_hotspot']);
     $additions = [ 'vr_view' => $vr_view, 'yaw' => $yaw, 'pitch' => $pitch ];
     $modal_form = \Drupal::service('entity.form_builder')->getForm($entity, $operation = 'default', $additions);
     // Add an AJAX command to open a modal dialog with the form as the content.
     $response->addCommand(new OpenModalDialogCommand('My Modal Form', $modal_form, ['width' => '800']));
     return $response;
   }
}