<?php

namespace Drupal\codelar_product;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a product entity type.
 */
interface ProductInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
