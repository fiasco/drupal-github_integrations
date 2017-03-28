<?php

namespace Drupal\github_integrations\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\github_integrations\Client;

/**
 * Class GithubIntegrationsConfigEntityForm.
 *
 * @package Drupal\github_integrations\Form
 */
class GithubIntegrationsConfigEntityForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#description' => $this->t("Label for the GitHub Integration."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\github_integrations\Entity\GithubIntegrationsConfigEntity::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['integration_id'] = [
      '#type' => 'number',
      '#title' => $this->t('Integration ID'),
      '#default_value' => $entity->get('integration_id'),
      '#rows' => 15,
    ];

    $form['private_key'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Private key'),
      '#default_value' => $entity->get('private_key'),
      '#rows' => 15,
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validation is optional.
    try {
      Client::installations($this->entity);
    }
    catch (\Exception $e) {
      $form_state->setErrorByName('integration_id', get_class($e) . ': ' . $e->getMessage());
      return;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label GitHub Integration Private Key.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label GitHub Integration Private Key.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirectUrl($entity->toUrl('collection'));
  }

}
