<?php

namespace Drupal\lightning_workflow\Plugin\views\access;

use Drupal\Core\Session\AccountInterface;
use Drupal\node\Access\NodeRevisionAccessCheck;
use Drupal\views\Plugin\views\access\AccessPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Access plugin that emulates the controls at /node/{node}/revisions.
 *
 * @ViewsAccess(
 *   id = "node_revision",
 *   title = @Translation("Node Revision"),
 *   help = @Translation("Will be available on node pages if the user can view revisions.")
 * )
 */
class NodeRevision extends AccessPluginBase {

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The node revision access checker.
   *
   * @var \Drupal\node\Access\NodeRevisionAccessCheck
   */
  protected $accessChecker;

  /**
   * NodeRevision constructor.
   *
   * @param array $configuration
   *   Plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param \Drupal\node\Access\NodeRevisionAccessCheck $access_checker
   *   The node revision access checker.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Request $request, NodeRevisionAccessCheck $access_checker) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->request = $request;
    $this->accessChecker = $access_checker;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('access_check.node.revision')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    $node = $this->request->get('node');
    return $this->accessChecker->checkAccess($node, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function alterRouteDefinition(Route $route) {
    // Unconditionally allow access to the route; we'll do the heavy lifting
    // in static::access().
    $route->setRequirement('_access', 'TRUE');
  }

}
