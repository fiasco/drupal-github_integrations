<?php

namespace Drupal\github_integrations;

use Lcobucci\JWT\Builder as JWTBuilder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Github\HttpClient\Builder as GithubBuilder;
use Github\Client as GithubClient;
use Drupal\github_integrations\Entity\GithubIntegrationsConfigEntityInterface as ConfigEntityInterface;

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

    $time = time();

    $jwt = (new JWTBuilder)
        ->setIssuer($entity->get('integration_id'))
        ->setIssuedAt($time)
        ->setExpiration($time + $exp)
        ->sign(new Sha256(),  new Key($entity->get('private_key')))
        ->getToken();


    $github->authenticate($jwt, null, GithubClient::AUTH_JWT);

    $installations = $github->api('integrations')->findInstallations();
    return $installations;
  }
}

 ?>
