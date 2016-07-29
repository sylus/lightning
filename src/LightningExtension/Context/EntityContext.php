<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\DrupalApiTrait;
use Behat\Gherkin\Node\TableNode;
use Drupal\Core\Entity\EntityInterface;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;

/**
 * Contains steps for working with entities.
 */
class EntityContext extends DrupalSubContextBase {

  use DrupalApiTrait;

  /**
   * Entities created by this context.
   *
   * @var EntityInterface[]
   */
  protected $entities = [];

  /**
   * Path aliases created by this context.
   *
   * @var string[]
   */
  protected $aliases = [];

  /**
   * Deletes entities and aliases created during the scenario.
   *
   * @AfterScenario
   */
  public function clean() {
    $alias_storage = \Drupal::service('path.alias_storage');
    array_walk($this->aliases, [$alias_storage, 'delete']);
    $this->aliases = [];

    while ($this->entities) {
      array_shift($this->entities)->delete();
    }
  }

  /**
   * Stores a node's path alias for post-scenario deletion.
   *
   * This method acts only on nodes created by the Drupal Extension.
   *
   * @param \Drupal\DrupalExtension\Hook\Scope\EntityScope $scope
   *   The hook scope.
   *
   * @afterNodeCreate
   */
  public function cleanNodeAlias(EntityScope $scope) {
    $node = Node::load($scope->getEntity()->nid);
    $this->cleanAlias($node);
  }

  /**
   * Stores a taxonomy term's path alias for post-scenario deletion.
   *
   * This method acts only on terms created by the Drupal Extension.
   *
   * @param \Drupal\DrupalExtension\Hook\Scope\EntityScope $scope
   *   The hook scope.
   *
   * @afterTermCreate
   */
  public function cleanTermAlias(EntityScope $scope) {
    $term = Term::load($scope->getEntity()->tid);
    $this->cleanAlias($term);
  }

  /**
   * Store's a user's path alias for post-scenario deletion.
   *
   * This method acts only on users created by the Drupal Extension.
   *
   * @param \Drupal\DrupalExtension\Hook\Scope\EntityScope $scope
   *   The hook scope.
   *
   * @afterUserCreate
   */
  public function cleanUserAlias(EntityScope $scope) {
    $user = User::load($scope->getEntity()->uid);
    $this->cleanAlias($user);
  }

  /**
   * Stores an entity's path alias for post-scenario deletion.
   *
   * Automatic alias cleanup on entity deletion is something core SHOULD do,
   * but doesn't -- so we're filling a hole in core here.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The aliased entity.
   */
  protected function cleanAlias(EntityInterface $entity) {
    $this->assertBootstrap();

    $path = '/' . $entity->toUrl()->getInternalPath();

    $alias = \Drupal::service('path.alias_manager')->getAliasByPath($path);
    if ($alias != $path) {
      array_push($this->aliases, ['alias' => $alias]);
    }
  }

  /**
   * Creates a set of entities of a particular type.
   *
   * All entities created by this method will be automatically deleted after
   * the scenario finishes.
   *
   * @param string $entity_type
   *   The entity type ID.
   * @param \Behat\Gherkin\Node\TableNode $entities
   *   The entities to create, in spreadsheet form.
   *
   * @Given :entity_type entities:
   */
  public function entityCreateBatch($entity_type, TableNode $entities) {
    $this->assertBootstrap();

    $definition = \Drupal::entityTypeManager()->getDefinition($entity_type);

    foreach ($entities as $values) {
      // Add the uid if not set.
      if (($uid_key = $definition->getKey('uid')) && !isset($values[$uid_key])) {
        $values[$uid_key] = \Drupal::currentUser()->id();
      }

      $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->create($values);
      $entity->save();

      array_push($this->entities, $entity);
      $this->cleanAlias($entity);
    }
  }

}
