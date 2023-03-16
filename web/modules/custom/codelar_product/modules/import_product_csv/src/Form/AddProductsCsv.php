<?php

/**
 * @file
 * Contains Drupal\import_product_csv\AddProductsCsv
 */

namespace Drupal\import_product_csv\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AddProductsCsv extends FormBase {

  /**
   * @return string
   */
  public function getFormId() {
    return 'add_product_csv_form';
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $validators = array(
      'file_validate_extensions' => array('csv'),
    );
    $form['products_file'] = [
      '#type' => 'managed_file',
      '#name' => 'my_file',
      '#title' => t('File *'),
      '#size' => 20,
      '#description' => t('CSV format only'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://',
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Upload CSV & Delete Users'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('products_file') == NULL) {
      $form_state->setErrorByName('products_file', 'Please upload a file in order to continue.');
    }
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $users_emails = [];
    $file = $form_state->getValue('products_file');
    $fid = $file[0];
    $file = \Drupal\file\Entity\File::load($fid);
    $destination = $file->toArray()['uri'][0]['value'];
    $data = $this->csvToArray($file->getFileUri(), ',');
    foreach($data as $row) {
      $operations[] = ['\Drupal\import_product_csv\AddProductsBatch::addProduct', [$row]];
    }
    $batch = array(
      'title' => t('Adding products..'),
      'operations' => $operations,
      'finished' => '\Drupal\import_product_csv\AddProductsBatch::addProductCallback',
    );
    batch_set($batch);

  }

  public function csvToArray($filename, $delimiter){
  if(!file_exists($filename) || !is_readable($filename)) return FALSE;
  $header = NULL;
  $data = array();

  if (($handle = fopen($filename, 'r')) !== FALSE ) {
    while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
    {
      if(!$header){
        $header = $row;
      }else{
        $data[] = array_combine($header, $row);
      }
    }
    fclose($handle);
  }

  return $data;
}

}
