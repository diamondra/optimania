<?php

namespace Drupal\vote_anon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\UserStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SingleNodeVoteAjaxController.
 */
class SingleNodeVoteAjaxController extends ControllerBase {

  /**
   * The current user service.
   *
   * @var use Drupal\Core\Session\AccountInterface;
   */
  protected $currentUser;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $user_storage;

  /**
   * Constructs SingleNodeVoteAjaxController object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection to be used.
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   The user storage.
   */
  public function __construct(AccountInterface $current_user, Connection $database, UserStorageInterface $user_storage) {
    $this->currentUser = $current_user;
    $this->database = $database;
    $this->userStorage = $user_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('database'),
      $container->get('entity_type.manager')->getStorage('user')
    );
  }

  /**
   * Rendersinglenodevotelinkrenderable.
   *
   * @return string
   *   Return Hello string.
   */
  public function renderSingleNodeVoteLinkRenderable($node, $nojs, Request $request) {
    // Get Vote Anon configuration items.
    $config = $this->config('vote_anon.voteconfiguration');
    $already_vote = $config->get('warning_for_duplicate_voting');
    $new_vote = $config->get('message_after_voting');
    // Get the session from the request object.
    $session = $request->getSession();
    $session_id = $session->getId();
    // Get UUID.
    $uid = 0;
    $uid = $this->currentUser->id();
    $user = $this->userStorage->load($uid);
    $uuid = $user->uuid();
    // Determine whether the request is coming from AJAX or not.
    if ($nojs == 'ajax') {
      $cookie = $config->get('voting_cookie');
      $disable_vote_link = $config->get('disable_vote_link');
      // Check if user has already vote for this node.
      $id = $this->database->select('vote_anon', 'vote_anon')
        ->fields('vote_anon', ['id'])
        ->condition('entity_id', $node)
        ->condition('session_id', $session_id)
        ->execute()->fetchField();
      if (!$id) {
        $this->database->insert('vote_anon')->fields(
          [
            'entity_type' => 'node',
            'uid' => $uid,
            'uuid' => $uuid,
            'entity_id' => $node,
            'session_id' => $session_id,
            'created' => time(),
          ]
        )->execute();
        $vote_id = $this->database->select('vote_anon_counts', 'vote')
          ->fields('vote', ['vote_id'])
          ->condition('entity_id', $node)
          ->execute()->fetchField();
        if ($vote_id) {
          $this->database->update('vote_anon_counts')
            ->expression('count', 'count + 1')
            ->condition('vote_id', $vote_id)
            ->condition('entity_id', $node)
            ->execute();
        }
        else {
          $this->database->insert('vote_anon_counts')->fields(
            [
              'entity_type' => 'node',
              'count' => 1,
              'entity_id' => $node,
              'last_updated' => time(),
            ]
          )->execute();
        }
        $output = '<div id="votedestinationdiv' . $node . '" class="ajax-message">' . $new_vote . '</div>';
      }
      else {
        $output = '<div id="votedestinationdiv' . $node . '" class="ajax-message">' . $already_vote . '</div>';
      }
    }
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand("#votedestinationdiv{$node}", $output));
    // Disable vote link.
    if ($disable_vote_link) {
      $response->addCommand(new InvokeCommand(NULL, 'disableVoteLinks', ["{$node}"]));
    }
    return $response;
  }

}
