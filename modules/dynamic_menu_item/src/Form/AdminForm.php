<?php

namespace Drupal\dynamic_menu_item\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuParentFormSelectorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contains AdminForm for dynamic_menu_item.
 */
class AdminForm extends ConfigFormBase {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The parent form selector service.
   *
   * @var \Drupal\Core\Menu\MenuParentFormSelectorInterface
   */
  protected $menuParentSelector;

  /**
   * Constructs a new \Drupal\Core\Menu\Form\MenuLinkDefaultForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Menu\MenuParentFormSelectorInterface $menu_parent_selector
   *   The menu parent form selector service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MenuParentFormSelectorInterface $menu_parent_selector) {
    $this->entityTypeManager = $entity_type_manager;
    $this->menuParentSelector = $menu_parent_selector;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.menu.link'),
      $container->get('menu.parent_form_selector')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'dynamic_menu_item.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dynamic_menu_item_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dynamic_menu_item.adminsettings');

    $menu_names = menu_ui_get_menus();
    $parent_element = $this->menuParentSelector
      ->parentSelectElement($config->get('menu_parent'), '', $menu_names);
    // If no possible parent menu items were found, there is nothing to display.
    if (empty($parent_element)) {
      $this->messenger()->addWarning($this->t('No possible parent menu items found.'));
      return;
    }

    $form['menu_parent'] = $parent_element;
    $form['menu_parent']['#title'] = $this->t('Parent item');
    $form['menu_parent']['#attributes']['class'][] = 'menu-parent-select';

    $types = $this->entityTypeManager
      ->getStorage('node_type')
      ->loadMultiple();

    foreach ($types as $type) {
      $content_types[$type->id()] = $type->label();
    }

    $form['option_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Option Title'),
      '#description' => $this->t('Label to be used for checkbox on node.'),
      '#default_value' => $config->get('option_title'),
    ];

    $form['menu_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Menu Title'),
      '#description' => $this->t('Title to be used for Menu Item.'),
      '#default_value' => $config->get('menu_title'),
    ];

    $form['menu_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Menu Weight'),
      '#description' => $this->t('Weight to be used for Menu Item.'),
      '#default_value' => $config->get('menu_weight'),
    ];

    $form['enabled_content_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Content Types'),
      '#description' => $this->t('This dynamic menu item will be available on enabled content types'),
      '#options' => $content_types,
      '#default_value' => $config->get('enabled_content_types'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('dynamic_menu_item.adminsettings')
      ->set('menu_parent', $form_state->getValue('menu_parent'))
      ->set('option_title', $form_state->getValue('option_title'))
      ->set('menu_title', $form_state->getValue('menu_title'))
      ->set('menu_weight', $form_state->getValue('menu_weight'))
      ->set('enabled_content_types', $form_state->getValue('enabled_content_types'))
      ->save();
  }

}
