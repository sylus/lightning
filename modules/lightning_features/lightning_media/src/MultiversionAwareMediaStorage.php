<?php

/**
 * @file
 * Contains \Drupal\lightning_media\MultiversionAwareMediaStorage.
 */

namespace Drupal\lightning_media;

use Drupal\media_entity\MediaStorage;
use Drupal\multiversion\Entity\Storage\ContentEntityStorageTrait;

class MultiversionAwareMediaStorage extends MediaStorage {

  use ContentEntityStorageTrait;

}
