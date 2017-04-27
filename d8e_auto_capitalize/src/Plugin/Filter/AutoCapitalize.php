<?php

/**
 * @file
 * Contains Drupal\auto_capitalize\Plugin\Filter\AutoCapitalize.
 */

namespace Drupal\d8e_auto_capitalize\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Filter to auto capitalize pre configured words.
 *
 * @Filter(
 *   id = "auto_capitalize",
 *   title = @Translation("Auto Capitalize"),
 *   description = @Translation("Auto capitalize pre configured words"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 * )
 */
class AutoCapitalize extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $auto_capitalize_words = explode(',', $this->settings['auto_capitalize_words']);
    $search = array_map('trim', $auto_capitalize_words);
    $replace = array_map('strtoupper', $search);
    $new_text = str_ireplace($search, $replace, $text);

    $result = new FilterProcessResult($new_text);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['auto_capitalize_words'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Words to Capitalize'),
      '#default_value' => $this->settings['auto_capitalize_words'],
      '#description' => $this->t('Enter list of words in small case which should be capitalized. Separate multiple words with comma.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('If a Word like "drupal" is added in auto capitalize settings, it will be replaced by "DRUPAL".');
  }

}
