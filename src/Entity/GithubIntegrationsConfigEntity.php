<?php

namespace Drupal\github_integrations\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the GitHub Integrations Configuration entity.
 *
 * @ConfigEntityType(
 *   id = "github_integrations_config",
 *   label = @Translation("GitHub Integrations Configuration"),
 *   handlers = {
 *     "list_builder" = "Drupal\github_integrations\GithubIntegrationsConfigEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\github_integrations\Form\GithubIntegrationsConfigEntityForm",
 *       "edit" = "Drupal\github_integrations\Form\GithubIntegrationsConfigEntityForm",
 *       "delete" = "Drupal\github_integrations\Form\GithubIntegrationsConfigEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\github_integrations\GithubIntegrationsConfigEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "github_integrations_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/github_integrations_config/{github_integrations_config}",
 *     "add-form" = "/admin/config/github_integrations_config/add",
 *     "edit-form" = "/admin/config/github_integrations_config/{github_integrations_config}/edit",
 *     "delete-form" = "/admin/config/github_integrations_config/{github_integrations_config}/delete",
 *     "collection" = "/admin/config/github_integrations_config"
 *   }
 * )
 */
class GithubIntegrationsConfigEntity extends ConfigEntityBase implements GithubIntegrationsConfigEntityInterface {

  /**
   * The GitHub Integrations Configuration ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The GitHub Integrations Configuration label.
   *
   * @var string
   */
  protected $label;

  /**
   * The private key that represents this integration.
   */
  protected $private_key;

  /**
   * The GitHub ID of the integration.
   */
  protected $integration_id;

}
