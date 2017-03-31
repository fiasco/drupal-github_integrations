<?php

namespace Drupal\github_integrations;

use Lcobucci\JWT\Builder as JWTBuilder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Github\HttpClient\Builder as GithubBuilder;
use Github\Client as GithubClient;
use Drupal\github_integrations\Entity\GithubIntegrationsConfigEntityInterface as ConfigEntityInterface;
use Drupal\node\Entity\Node;

class Client {

  const AGENT = 'machine-man-preview';

  /**
   * Create an API connection to GitHub
   *
   * @param ConfigEntity $entity
   *
   * @param int $exp
   *    Token lifetime in seconds.
   */
  static public function installations(ConfigEntityInterface $entity, $exp = 60)
  {
    $builder = new GithubBuilder();
    $github = new GithubClient($builder, Client::AGENT);
    $github->authenticate(self::getJwtToken($entity, $exp), null, GithubClient::AUTH_JWT);

    $installations = $github->api('integrations')->findInstallations();
    return $installations;
  }

  /**
   * Initialise a github API client with a known installation.
   *
   * @param node $installation
   *
   * @param int $exp
   *    expiration of client session in seconds.
   */
  static public function init(Node $installation, $exp = 60)
  {
    $builder = new GithubBuilder();
    $config = $installation->get('field_integration')
                ->first()
                ->get('entity')
                ->getTarget()
                ->getValue();

    $github = new GithubClient($builder, Client::AGENT);
    $github->authenticate(self::getJwtToken($config, $exp), null, GithubClient::AUTH_JWT);
    $token = $github->api('integrations')->createInstallationToken((int) $installation->get('field_id')->getString());

    \Drupal::logger('github_integrations')->notice("Opened up API for @exp seconds with token @token for installation on @account", [
      '@exp' => $exp,
      '@token' => $token['token'],
      '@account' => $installation->getTitle(),
    ]);

    $github->authenticate($token['token'], null, GithubClient::AUTH_HTTP_TOKEN);
    return $github;
  }

  static public function getJwtToken(ConfigEntityInterface $entity, $exp = 60)
  {
    $time = time();

    $jwt = (new JWTBuilder)
        ->setIssuer($entity->get('integration_id'))
        ->setIssuedAt($time)
        ->setExpiration($time + $exp)
        ->sign(new Sha256(),  new Key($entity->get('private_key')))
        ->getToken();
    return $jwt;
  }
}

 ?>
