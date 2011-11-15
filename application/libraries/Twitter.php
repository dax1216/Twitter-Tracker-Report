<?php

$path = dirname(__FILE__) . '/../../twitteroauth';

set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once('twitteroauth.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @author Dax Panganiban
 * @created 10/11/2011

 */
class Twitter {
    private $connection = null;

    public function __construct($params) {
        $this->connection = new TwitterOAuth($params['consumer_key'], $params['consumer_secret'], $params['oauth_token'], $params['oauth_token_secret']);        
    }

    public function get($request_uri, $params = null) {
        $content = $this->connection->get($request_uri, $params);

        return $content;
    }

}