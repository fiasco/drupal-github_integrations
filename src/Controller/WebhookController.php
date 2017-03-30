<?php

namespace Drupal\github_integrations\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\github_integrations\WebhookEvents;
use Drupal\github_integrations\WebhookEventNotFoundException;
use Drupal\github_integrations\Event\WebhookEvent;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;

/**
 * Class WebhookController.
 *
 * @package Drupal\github_integrations\Controller
 */
class WebhookController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger channel factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * WebhookController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   A logger channel factory.
   * @param \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(
      EntityTypeManagerInterface $entity_type_manager,
      LoggerChannelFactoryInterface $logger_factory,
      RequestStack $request_stack,
      ContainerAwareEventDispatcher $event_dispatcher
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->loggerFactory = $logger_factory;
    $this->requestStack = $request_stack;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('logger.factory'),
      $container->get('request_stack'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * Receive.
   *
   * @return string
   *   Return Hello string.
   */
  public function receive($github_integrations_config) {
    $webhooks_storage = $this->entityTypeManager->getStorage('github_integrations_config');
    $config = $webhooks_storage->load($github_integrations_config);

    $request = $this->requestStack->getCurrentRequest();

    $event_type = $request->headers->get('X-GitHub-Event');
    $id = $request->headers->get('X-GitHub-Delivery');

    $this->loggerFactory->get('github_integrations')->info("Webhook $event_type incoming - $id");

    $type = WebhookEvents::find($event_type);
    $payload = json_decode($request->getContent());

    try {

      // Dispatch Webhook Receive event.
      $this->eventDispatcher->dispatch(
        $type,
        new WebhookEvent($id, $type, $payload, $config)
      );

      return new Response(200, [], 'OK');
    }
    catch (WebhookEventNotFoundException $e) {
      $this->loggerFactory->get('github_integrations')->error($e->getMessage());
      return new Response(404, [], 'Not Found');
    }
  }

}
