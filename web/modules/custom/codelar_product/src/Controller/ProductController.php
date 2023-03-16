<?php

namespace Drupal\codelar_product\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Returns responses for codelar_product routes.
 */
class ProductController extends ControllerBase
{

  protected $session;
  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The controller constructor.
   *
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(SessionInterface $session, FormBuilderInterface $form_builder, EntityTypeManagerInterface $entity_type_manager)
  {
    $this->session = $session;
    $this->formBuilder = $form_builder;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('session'),
      $container->get('form_builder'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Builds the response.
   */
  public function build()
  {

    $build = [];
    $build[] = $this->formBuilder()->getForm('Drupal\codelar_product\Form\FiltroFormProduct');
    $filters = $this->session->get('codelar_product_filter');

    $query = $this->entityTypeManager()->getStorage('product')->getQuery();
    $query->condition('status', 1);
    $query->accessCheck(FALSE);
    if (isset($filters['price_min'])) {
      if (!empty($filters['price_min'])) {
        $query->condition('price', $filters['price_min'], '>=');
      }
    }
    if (isset($filters['price_max'])) {
      if (!empty($filters['price_max'])) {
        $query->condition('price', $filters['price_max'], '<=');
      }
    }

    $result = $query->execute();
    $products = $this->entityTypeManager()->getStorage('product')->loadMultiple($result);
    $products_array = [];

    foreach ($products as $product) {

      $products_array[] = [
        'id' => $product->id(),
        'label' => $product->label(),
        'url' => $product->get('url')->getValue()[0]['value'],
        'price' => $this->formatPrice($product->get('price')->getValue()[0]['value'])
      ];
    }

    $build[] = [
      '#theme' => 'product_list',
      '#products' => $products_array,
    ];
    return $build;
  }

  function formatPrice($price)
  {
    $formatted_price = number_format($price, 2, ',', '.');
    return '$' . $formatted_price . ' COP';
  }
}
