<?php

namespace Drupal\codelar_product\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the product entity edit forms.
 */
class ProductForm extends ContentEntityForm
{


  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildForm($form, $form_state);

    // Agregar campo de búsqueda.
    $form['search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search for images'),
      '#description' => $this->t('Enter a search term to find images on Pixabay'),
      '#required' => TRUE,
    ];

    // Agregar botón de búsqueda.
    $form['submit'] = [
      '#type' => 'button',
      '#value' => $this->t('Search'),
      '#ajax' => [
        'callback' => '::searchImagesCallback',
        'wrapper' => 'image-grid-wrapper',
      ],
    ];

    // Agregar contenedor de grid de imágenes.
    $form['image_grid_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'image-grid-wrapper',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state)
  {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New product %label has been created.', $message_arguments));
        $this->logger('codelar_product')->notice('Created new product %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The product %label has been updated.', $message_arguments));
        $this->logger('codelar_product')->notice('Updated product %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.product.canonical', ['product' => $entity->id()]);

    return $result;
  }

  public function searchImagesCallback(array &$form, FormStateInterface $form_state)
  {
    // Obtener el valor de búsqueda.
    $search_term = $form_state->getValue('search');

    // Obtener las imágenes de Pixabay API.
    $images = $this->getPixabayImages($search_term);

    // Crear una matriz de datos para pasar a Twig.
    $data = [
      'images' => $images,
    ];

    // Crear una respuesta renderizada.
    $response = [
      '#theme' => 'image_grid',
      '#data' => $data,
    ];

    return $response;
  }

  protected function getPixabayImages($search_term)
  {
    // Hacer una solicitud GET a la API de Pixabay con el término de búsqueda proporcionado.
    $url = 'https://pixabay.com/api/?key=16580591-5dadd9bc0fb4b8fdddb681e98&q=' . urlencode($search_term) . '&per_page=10';
    $response = \Drupal::httpClient()->get($url);

    // Decodificar la respuesta JSON.
    $data = json_decode($response->getBody(), TRUE);

    // Crear una matriz de imágenes para devolver.
    $images = [];
    foreach ($data['hits'] as $hit) {
      $images[] = [
        'url' => $hit['webformatURL'],
        'alt' => $hit['tags'],
      ];
    }

    return $images;
  }
}
