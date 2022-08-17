<?php

namespace Drupal\setmore\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Setmore' Block.
 *
 * @Block(
 *   id = "setmore",
 *   admin_label = @Translation("setmore"),
 *   category = @Translation("book an appointment"),
 * )
 */
class Setmore extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $setmore_key=$this->getConfiguration();
    return [
      '#markup' => $this->t('<script type="text/javascript" src="https://setmore.com/js/setmoreFancyBox.js"></script>
      <button onclick="javascript:setmorePopup(\''.$setmore_key['setMore'].'\');" style="cursor:pointer"><img border="none" alt="Book an appointment using SetMore" style="cursor:pointer" 
          src="http://my.setmore.com/images/bookappt/SetMore-book-button.png" /></button>'),
      '#chache' => [
          'max-age' =>0,
      ]
    ];
  }

  /**
   * {@inheritdoc}
   */

  public function blockForm($form, FormStateInterface $form_state) {
    $setmore_key=$this->getConfiguration();
    $form['setMore'] = [
        '#type' => 'textfield',
        '#title' => t('Enter your SetMore key here'),
        '#default_value' => $setmore_key['setMore'] ?? '',
        '#size' => 60,
        '#description' => t("setMore appointments"),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['setMore'] = $values['setMore'];
  }

}
