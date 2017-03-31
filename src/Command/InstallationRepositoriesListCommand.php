<?php

namespace Drupal\github_integrations\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Drupal\Console\Core\Command\Shared\ContainerAwareCommandTrait;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\github_integrations\Client;

/**
 * Class InstallationRepositoriesListCommand.
 *
 * @package Drupal\github_integrations
 *
 * @DrupalCommand (
 *     extension="github_integrations",
 *     extensionType="module"
 * )
 */
class InstallationRepositoriesListCommand extends Command {

  use ContainerAwareCommandTrait;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('integrations:installations:repositories:list')
      ->setAliases(['repo:list', 'iirl'])
      ->setDescription($this->trans('commands.integrations.installations.repositories.list.description'))
      ->addArgument('installation', InputArgument::REQUIRED, 'The node id of the installation to list from.')
      ;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    $nid = $input->getArgument('installation');
    $node = node_load($nid);

    $node->field_id->getString();

    $client = Client::init($node, 600);
    // $repos = $client->api('integrations')->listRepositories();


    $io->info($this->trans('commands.integrations.installations.repositories.list.messages.success'));
  }
}
