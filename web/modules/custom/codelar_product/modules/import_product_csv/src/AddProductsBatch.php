<?php

/**
 * Containt class Drupal\import_product_csv\AddProductsBatch
 */

namespace Drupal\import_product_csv;

use Drupal\codelar_product\Entity\Product;

class AddProductsBatch
{

  /**
   * @param $users_emails, array of users emails.
   * @param $context
   */
  public static function addProduct($item, &$context)
  {
    $message = 'adding products...';
    $results = array();
    if (!empty($item)) {
     Product::create([
      'label' => $item['name'],
      'description' => $item['description'],
      'url' => $item['url'],
      'price' => $item['price'],
      'status' => 1
     ])->save();
    }
    $context['message'] = $message;
    $context['results'] = $results;
  }

  /**
   * @param $success
   * @param $results
   * @param $operations
   */
  function addProductCallback($success, $results, $operations)
  {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One product added.',
        '@count products added.'
      );
    } else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()->addMessage($message);
  }
}
