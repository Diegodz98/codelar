<?php

namespace Drupal\codelar_product\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class SearchImage.
 *
 * @package Drupal\codelar_product\Controller
 */
class SearchImage extends ControllerBase {

  /**
   * Displays an image grid and copy button.
   */
  public function fetchImages() {
    // Obtener las imágenes de Pixabay API.
    $images = $this->getPixabayImages();

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

  /**
   * Obtener imágenes de Pixabay API.
   *
   * @return array
   *   Un arreglo con los datos de imagen obtenidos de la API.
   */
  protected function getPixabayImages() {
    // Hacer una solicitud GET a la API de Pixabay.
    $url = 'https://pixabay.com/api/?key=16580591-5dadd9bc0fb4b8fdddb681e98&q=agua&per_page=10';
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
