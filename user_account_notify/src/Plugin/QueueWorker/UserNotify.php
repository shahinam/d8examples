<?php

/**
 * @file
 * Contains Drupal\user_account_notify\Plugin\QueueWorker\UserNotify.
 */

namespace Drupal\user_account_notify\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\user\UserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Notify users about their account changes.
 *
 * @QueueWorker(
 *   id = "user_account_notify",
 *   title = @Translation("User Account Notify"),
 *   cron = {"time" = 30}
 * )
 */
class UserNotify extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The user storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $userStorage;

  /**
   * The mail manager.
   *
   * @var \Drupal\core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Creates a new UserNotify.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $user_storage
   *   The user storage.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   */
  public function __construct(EntityStorageInterface $user_storage, MailManagerInterface $mail_manager, LoggerInterface $logger) {
    $this->userStorage = $user_storage;
    $this->mailManager = $mail_manager;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity.manager')->getStorage('user'),
      $container->get('plugin.manager.mail'),
      $container->get('logger.factory')->get('user_account_notify')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    /** @var UserInterface $user */
    $account = $this->userStorage->load($data->uid);
    if ($account instanceof UserInterface) {
      $params = [
        'data' => $data,
        'account' => $account,
      ];

      $message = $this->mailManager
        ->mail('user_account_notify', $data->op, $account->getEmail(), $account->getPreferredLangcode(), $params);

      if ($message['result']) {
        $this->logger->notice('Sent email to %recipient', ['%recipient' => $message['to']]);
      }
      else {
        $this->logger->error('Unable to send email to %recipient', ['%recipient' => $message['to']]);
      }
    }
  }

}
