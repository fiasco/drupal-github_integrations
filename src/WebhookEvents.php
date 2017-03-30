<?php

namespace Drupal\github_integrations;

final class WebhookEvents {
  const INSTALL = "integration_installation";

  /**
   * Find the Drupal event to dispatch for webhook event.
   */
  static public function find($event_type) {
    switch ($event_type) {
      case self::INSTALL:
        return 'github_integrations.install';
    }
    throw new WebhookEventNotFoundException("$event_type is not a supported event.");
  }
}

 ?>
