<?php

namespace Drupal\github_integrations\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

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

    $github_integrations_config_entit = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $github_integrations_config_entit->label(),
      '#description' => $this->t("Label for the GitHub Integrations Configuration."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $github_integrations_config_entit->id(),
      '#machine_name' => [
        'exists' => '\Drupal\github_integrations\Entity\GithubIntegrationsConfigEntity::load',
      ],
      '#disabled' => !$github_integrations_config_entit->isNew(),
    ];

    $form['private_key'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Private key'),
      '#rows' => 15,
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $github_integrations_config_entit = $this->entity;
    $status = $github_integrations_config_entit->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label GitHub Integrations Configuration.', [
          '%label' => $github_integrations_config_entit->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label GitHub Integrations Configuration.', [
          '%label' => $github_integrations_config_entit->label(),
        ]));
    }
    $form_state->setRedirectUrl($github_integrations_config_entit->toUrl('collection'));
  }

}
