<?php

namespace Drupal\vote_anon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VoteAjaxController.
 */
class VoteAjaxController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs VoteAjaxController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection to be used.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Rendervotelinkrenderable.
   *
   * @return string
   *   Return string.
   */
  public function renderVoteLinkRenderable($node, $nojs = 'ajax') {
    // Determine whether the request is coming from AJAX or not.
    if ($nojs == 'ajax') {
      // Get Vote Anon configuration items.
      $config = $this->config('vote_anon.voteconfiguration');
      $already_vote = $config->get('warning_for_duplicate_voting');
      $new_vote = $config->get('message_after_voting');
      $cookie = $config->get('voting_cookie');
      $disable_vote_link = $config->get('disable_vote_link');
      if (!isset($_COOKIE[$cookie])) {
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
        $cookie_name = $cookie;
        $cookie_value = 1;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
        $output = '<div id="votedestinationdiv' . $node . '" class="ajax-message">' . $new_vote . '</div>';
      }
      else {
        $output = '<div id="votedestinationdiv' . $node . '" class="ajax-message">' . $already_vote . '</div>';
      }
      $response = new AjaxResponse();
      $response->addCommand(new ReplaceCommand("#votedestinationdiv{$node}", $output));
      // Disable vote link.
      if ($disable_vote_link) {
        $response->addCommand(new InvokeCommand(NULL, 'disableVoteLinks', ['/']));
      }
      return $response;
    }
  }

  /**
   * Votecount.
   *
   * @return string
   *   Return Hello string.
   */
  public function voteCount() {
    $items = [];
    $query = \Drupal::database()->select('vote_anon_counts', 'vote');
    $query->fields('vote', ['entity_id', 'count']);
    $query->join('node_field_data', 'node', 'vote.entity_id = node.nid');
    $query->fields('node', ['title']);
    $results = $query->execute()->fetchAll();
    if (count($results) > 0) {
      foreach ($results as $result) {
        $items[] = $result->title . ' Total Votes: ' . $result->count;
      }
    }
    return $output['voting'] = [
      '#title' => 'Voting Details',
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }

}
