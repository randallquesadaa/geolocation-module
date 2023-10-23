<?php

namespace Drupal\geolocation_module\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Creation Geolocation Form.
 */
class GeolocationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'geolocation_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'geolocation_module.geolocation_form',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('geolocation_module.geolocation_form');
    $form['google_cloud_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Cloud APIs Key'),
      '#default_value' => $config->get('google_cloud_api_key'),
    ];
    $form['geolocation_block_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Geolocation block text'),
      '#default_value' => $config->get('geolocation_block_text'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('geolocation_module.geolocation_form')
      ->set('google_cloud_api_key', $form_state->getValue('google_cloud_api_key'))
      ->set('geolocation_block_text', $form_state->getValue('geolocation_block_text'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
