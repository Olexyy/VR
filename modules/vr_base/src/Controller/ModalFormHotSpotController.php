<?php

namespace Drupal\vr_base\Controller;

use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\vr_base\VrBase;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * ModalFormExampleController class.
 */
class ModalFormHotSpotController extends ControllerBase {

   /**
    * Entity form builder.
    * @var \Drupal\Core\Entity\EntityFormBuilder
    */
   protected $entityFormBuilder;

  /**
   * Entity type manager.
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
   protected $entityTypeManager;

  /**
   * Allowed actions in get request
   * @var array
   */
   protected static $actionTypes = [ 'create', 'edit' ];
   public static $create = 'create';
   public static $edit = 'edit';

   protected function isValidAction($action) {
     return in_array($action, self::$actionTypes);
   }

   /**
    * ModalFormHotSpotController constructor.
    * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
    * @param \Drupal\Core\Entity\EntityFormBuilder $entity_form_builder
    */
   public function __construct(EntityTypeManager $entity_type_manager, EntityFormBuilder $entity_form_builder) {
     $this->entityTypeManager = $entity_type_manager;
     $this->entityFormBuilder = $entity_form_builder;
   }

   /**
    * {@inheritdoc}
    * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
    * The Drupal service container.
    * @return static
    */
   public static function create(ContainerInterface $container) {
     return new static($container->get('entity_type.manager'), $container->get('entity.form_builder'));
   }

   /**
    * Initial ajax callback for hotspot form.
    * @param $action
    * @param $vr_item
    * @param $yaw
    * @param $pitch
    * @return AjaxResponse
    * See more at: https://www.mediacurrent.com/blog/loading-and-rendering-modal-forms-drupal-8#sthash.a1xRiRVP.dpuf
    */
  public function createHotSpotModalForm($action, $vr_item, $yaw, $pitch) {
    $response = new AjaxResponse();
    if($this->isValidAction($action)) {
      if($action == self::$create) {
        $entityStorage = $this->entityTypeManager->getStorage(VrBase::entityTypeVRHotspot);
        $entity = $entityStorage->create(['type' => VrBase::entityBundleBaseVRHotspot]);
        $additions = ['action' => $action, 'vr_view' => $vr_item, 'yaw' => $yaw, 'pitch' => $pitch, 'ajax' => TRUE];
        $modal_form = $this->entityFormBuilder->getForm($entity, $operation = 'default', $additions);
        $response->addCommand(new OpenModalDialogCommand('New hotspot', $modal_form, ['width' => '800']));
      }
      else if ($action == self::$edit) {
        $entityStorage = $this->entityTypeManager->getStorage(VrBase::entityTypeVRHotspot);
        $entity = $entityStorage->load($vr_item);
        $additions = ['action' => $action, 'yaw' => $yaw, 'pitch' => $pitch, 'ajax' => TRUE];
        $modal_form = $this->entityFormBuilder->getForm($entity, $operation = 'default', $additions);
        $response->addCommand(new OpenModalDialogCommand('Edit hotspot', $modal_form, ['width' => '800']));
      }
    }
    return $response;
  }

  /**
   * Ajax processing callback for hotspot form.
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function formAjaxCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if ($form_state->getErrors()) {
      unset($form['#prefix']);
      unset($form['#suffix']);
      $form['status_messages'] = [
        '#type' => 'status_messages',
        '#weight' => -10,
      ];
      $response->addCommand(new HtmlCommand('#'.VrBase::formWrapper, $form));
    }
    else {
      $storage = $this->entityTypeManager->getStorage('vr_view');
      $parent_entity = $storage->load($form_state->get(VrBase::EntityTypeVRView));
      $id = $form_state->getFormObject()->getEntity()->id->value;
      $parent_entity->field_vr_hotspots[] = $id;
      $parent_entity->save();
      $http_referrer = \Drupal::request()->server->get('HTTP_REFERER');
      $response->addCommand(new RedirectCommand($http_referrer));
    }
    return $response;
  }
}