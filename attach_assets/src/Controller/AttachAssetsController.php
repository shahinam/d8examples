<?php

/**
 * @file
 * Contains \Drupal\assets_test\Controller\AttachAssetsController.
 */

namespace Drupal\attach_assets\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Url;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AttachAssetsController.
 */
class AttachAssetsController extends ControllerBase
{

  /**
   * The database object.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;


  /**
   * The link generator.
   *
   * @var \Drupal\Core\Utility\LinkGeneratorInterface
   */
  protected $linkGenerator;

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;


  /**
   * AttachAssetsController constructor.
   *
   * @param Connection $database
   *   The database.
   * @param LinkGeneratorInterface $link_generator
   *   The link generator.
   * @param EntityStorageInterface $node_storage
   *   The node storage.
   */
  public function __construct(Connection $database, LinkGeneratorInterface $link_generator, EntityStorageInterface $node_storage) {
    $this->database = $database;
    $this->linkGenerator = $link_generator;
    $this->nodeStorage = $node_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('link_generator'),
      $container->get('entity.manager')->getStorage('node')
    );
  }


  /**
   * Index page for attach assets example.
   *
   * @return mixed
   *   Render array.
   */
  public function index() {
    $rows = [];
    $header = [
      $this->t('Title'),
      $this->t('Operations')
    ];

    $result = $this->recentNodes();
    foreach ($result as $node) {
      $links = [];
      $links['delete'] = [
        'title' => $this->t('Delete'),
        'url' => Url::fromRoute('attach_assets.node.delete', ['nid' => $node->nid]),
      ];

      $rows[] = [
        $this->linkGenerator->generate($node->title, new Url('entity.node.canonical', ['node' => $node->nid])),
        [
          'data' => [
            '#type' => 'operations',
            '#links' => $links,
          ],
        ],
      ];
    }

    $build['messages'] = [
      '#type' => 'markup',
      '#markup' => '',
      '#prefix' => '<div class="message">',
      '#suffix' => '</div>',
    ];
    $build['content'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No content created yet.'),
    ];

    $build['#attached']['library'][] = 'attach_assets/node_delete';

    $build['#attached']['drupalSettings']['attach_assets']['delete_confirmation'] = '<span class="deleted">Deleted</span>';

    return $build;
  }

  /**
   *
   * @return mixed
   */

  /**
   * Get recent nodes.
   *
   * @param int $count
   *   Number of nodes to fetch.
   * @return \Drupal\Core\Database\StatementInterface|null
   *   A prepared statement, or NULL if the query is not valid.
   */
  protected function recentNodes($count = 10) {
    $query = $this->database->select('node', 'n');
    $query->join('node_field_data', 'nfd', 'n.nid = nfd.nid');

    return $query->fields('n')
      ->fields('nfd')
      ->addTag('node_access')
      ->addMetaData('base_table', 'node')
      ->orderBy('nfd.created', 'DESC')
      ->range(0, $count)
      ->execute();
  }

  /**
   * Delete a node.
   *
   * @param $nid
   *   Node ID.
   * @return array
   *   Render array.
   */
  public function nodeDelete($nid) {
    // Make sure we have valid argument.
    if (!is_numeric($nid)) {
      throw new AccessDeniedHttpException();
    }

    $node = $this->nodeStorage->load($nid);
    if ($node instanceof NodeInterface) {
      $this->nodeStorage->delete([$node]);
    } else {
      throw new NotFoundHttpException();
    }

    return [];
  }

}
