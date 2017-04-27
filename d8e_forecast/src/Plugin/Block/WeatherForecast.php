<?php

/**
 * @file
 * Contains \Drupal\forecast\Plugin\Block\WeatherForecast.
 */

namespace Drupal\d8e_forecast\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Forecast\Forecast;

/**
 * Provides a 'WeatherForecast' block.
 *
 * @Block(
 *  id = "weather_forcast",
 *  admin_label = @Translation("Weather Forecast"),
 *  category = @Translation("Forecast"),
 * )
 */
class WeatherForecast extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'latitude' => 0,
      'longitude' => 0,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
     // Delhi coordinates: (28.6139, 77.2090).
    $forecast = new Forecast('7411b0e6d5e0c99fbd7405fd6de00cd5');
    $fc = $forecast->get($this->configuration['latitude'], $this->configuration['longitude']);

    $build = [];
    $build['forecast']['#markup'] = $fc->daily->summary;

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['latitude'] = array(
      '#title' => t('Latitude'),
      '#type' => 'textfield',
      '#default_value' => $this->configuration['latitude'],
      '#description' => $this->t('Enter latitude of a place.'),
    );
    $form['longitude'] = array(
      '#title' => t('Longitude'),
      '#type' => 'textfield',
      '#default_value' => $this->configuration['longitude'],
      '#description' => $this->t('Enter Longitude of place.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    $latitude = $form_state->getValue('latitude');
    if ($latitude < -90 || $latitude > 90) {
      $form_state->setErrorByName('latitude', $this->t('Error'));
    }

    $longitude = $form_state->getValue('longitude');
    if ($longitude < -180 || $longitude > 180) {
      $form_state->setErrorByName('longitude', $this->t('Error.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('latitude', $form_state->getValue('latitude'));
    $this->setConfigurationValue('longitude', $form_state->getValue('longitude'));
  }

}

