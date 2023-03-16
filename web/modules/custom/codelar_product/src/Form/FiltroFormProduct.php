<?php

namespace Drupal\codelar_product\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class FiltroFormProduct extends FormBase
{


  private $session;
  public function __construct(SessionInterface $session)
  {
    $this->session = $session;
  }
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('session')
    );
  }
  public function getFormId()
  {

    return 'codelar_product_filter';
  }


  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $filtros = $this->session->get('codelar_product_filter', []);

    $form['price_min'] = [
      '#type' => 'number',
      '#title' => t('Price min'),
      '#default_value' => isset($filtros['price_min']) ? $filtros['price_min'] : '',
      '#step' => 1,
    ];

    $form['price_max'] = [
      '#type' => 'number',
      '#title' => t('Price max'),
      '#default_value' => isset($filtros['price_max']) ? $filtros['price_max'] : '',
      '#step' => 1,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Filtrar',

    ];
    $form['actions']['reset'] = [
      '#type' => 'submit',
      '#value' => 'Resetear',
      '#submit' => ['::resetSubmit']

    ];
    return $form;
  }



  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $filtro = [];
    // Aplicar filtro de rango de precio
      $filtro['price_min'] =  $form_state->getValue('price_min');

      $filtro['price_max'] =  $form_state->getValue('price_max');
    $this->session->set('codelar_product_filter', $filtro);
  }
  public function resetSubmit(array &$form, FormStateInterface $form_state)
  {
    $filtro = [];
    $this->session->set('codelar_product_filter', $filtro);
  }
}
