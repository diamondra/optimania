<?php

namespace Drupal\fa_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'fa_formatter_int' formatter.
 *
 * @FieldFormatter(
 *   id = "fa_formatter_int",
 *   label = @Translation("FA Int Formatter"),
 *   field_types = {
 *     "list_integer" *
 *   }
 * )
 */
class FAFormatterInt extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays the Stars');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    $icon_setting = $this->getSetting('icon_class');

    //get the maximum values for star/icon caluclation
    $allowed_values = $items->getFieldDefinition()->getSettings();
    $maximum_value = count($allowed_values['allowed_values']);
    $markup = '';

    $values = $items->getValue();

    //we have values set fo the field
    if(!empty($values)) {
      foreach ($items as $delta => $item) {
        $on = $item->value;
        $off = $maximum_value - $item->value;

        $markup = "<div class='fa-formatter'>";
        $markup .= "<span class='rate-value'>$item->value</span>";

        //stars ON
        for ($i = 1; $i <= $on; $i++) {
          $markup .= "<span class='rate-image star-on odd s1'>$icon_setting</span>";
        }

        //starts OFF
        for ($i = 1; $i <= $off; $i++) {
          $markup .= "<span class='rate-image star-off odd s1'>$icon_setting</span>";
        }
        $markup .= "</div>";

        $element[$delta] = [
          '#markup'   => $markup,
          '#attached' => [
            'library' => [
              'fa_formatter/fa_formatter.usage',
            ],
          ],
        ];
      }
    }
    //if the user does no tselect anything we generate "no result"
    else {
      $element[0] = [
        '#markup'   => t('Noch keine Bewertung.'),
        '#attached' => [
          'library' => [
            'fa_formatter/fa_formatter.usage',
          ],
        ],
      ];
    }

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        // Declare a setting named 'icon_class', with
        // a default value of 'short'
        'icon_class' => '<i class="fas fa-star"></i>',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['icon_class'] = [
      '#title' => $this->t('Icon class'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('icon_class'),
    ];

    return $form;
  }


}
