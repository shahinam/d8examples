<?php

/**
 * @file
 * Contains \Drupal\user_account_notify\Form\UserNotifySettingsForm.
 */

namespace Drupal\user_account_notify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure user account notify settings.
 */
class UserNotifySettingsForm extends ConfigFormBase {

  /**
   * Constructs a \Drupal\user_account_notify\Form\UserNotifySettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_account_notify_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'user_account_notify.general',
      'user_account_notify.mail',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $general_config = $this->config('user_account_notify.general');
    $mail_config = $this->config('user_account_notify.mail');

    // General settings.
    $form['general'] = array(
      '#type' => 'details',
      '#title' => $this->t('General'),
      '#open' => TRUE,
    );

    $form['general']['mail_notifications'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Email notifications'),
      '#default_value' => $general_config->get('mail_notification'),
      '#options' => array(
        USER_ACCOUNT_NOTIFY_ALL => $this->t('Send all notifications'),
        USER_ACCOUNT_NOTIFY_QUEUE => $this->t('Send already queued notifications'),
        USER_ACCOUNT_NOTIFY_NONE => $this->t('Disable all notifications'),
      ),
      '#description' => $this->t('Notify users about account updates.'),
    );
    $form['general']['mail_notification_address'] = array(
      '#type' => 'email',
      '#title' => $this->t('Notification email address'),
      '#default_value' => $general_config->get('mail_notification_address'),
      '#description' => $this->t("The email address to be used as the 'from' address for notifications."),
      '#maxlength' => 180,
    );

    // Email settings.
    $form['email'] = array(
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Emails'),
    );

    $form['email_new_user'] = array(
      '#type' => 'details',
      '#title' => $this->t('Welcome new user'),
      '#description' => $this->t(''),
      '#group' => 'email',
    );
    $form['email_new_user']['insert_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#default_value' => $mail_config->get('insert.subject'),
      '#maxlength' => 180,
    );
    $form['email_new_user']['insert_body'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Body'),
      '#default_value' =>  $mail_config->get('insert.body'),
      '#rows' => 15,
    );

    $form['email_update_user'] = array(
      '#type' => 'details',
      '#title' => $this->t('Account updates'),
      '#description' => $this->t('.'),
      '#group' => 'email',
    );
    $form['email_update_user']['update_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#default_value' => $mail_config->get('update.subject'),
      '#maxlength' => 180,
    );
    $form['email_update_user']['update_body'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Body'),
      '#default_value' => $mail_config->get('update.body'),
      '#rows' => 8,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('user_account_notify.general')
      ->set('mail_notification', $form_state->getValue('mail_notifications'))
      ->set('mail_notification_address', $form_state->getValue('mail_notifications_address'))
      ->save();
    $this->config('user_account_notify.mail')
      ->set('insert.subject', $form_state->getValue('insert_subject'))
      ->set('insert.body', $form_state->getValue('insert_body'))
      ->set('update.subject', $form_state->getValue('update_subject'))
      ->set('update.body', $form_state->getValue('update_body'))
      ->save();
  }

}
