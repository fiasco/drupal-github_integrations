<?php
namespace Drupal\github_integrations\Event;

use Symfony\Component\EventDispatcher\Event;
use Drupal\github_integrations\Entity\GithubIntegrationsConfigEntityInterface;

class WebhookEvent extends Event {
  protected $type;

  protected $payload;

  protected $config;

  protected $id;

  public function __construct($id, $type, $payload, GithubIntegrationsConfigEntityInterface $config)
  {
    $this->id = $id;
    $this->type = $type;
    $this->payload = $payload;
    $this->config = $config;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getPayload()
  {
    return $this->payload;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getConfig()
  {
    return $this->config;
  }

  public function packageData() {
    return [
      'id' => $this->id,
      'payload' => $this->payload,
      'type' => $this->type,
      'config' => $this->config->get('id')
    ];
  }
}
