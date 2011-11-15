<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller
{

    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        ob_start(true);

        $ctr = 1;
        while($ctr <= 5) {
            echo $ctr. ' test <br />';
            ob_flush();
            flush();
            sleep(15.5);
            $ctr++;
        }
        //echo '2nd test';
        //ob_flush();
        //flush();
    }
    
    public function klout() {
        $klout_api_key = $this->config->item('KLOUT_API_KEY');

        $this->load->library('klout', array('api_key' => $klout_api_key));

        $klout_score_response = $this->klout->get('klout', array('users' => 'Bonnes_Affaires'), 'json');

        if(count($klout_score_response->users) > 0) {
            foreach($klout_score_response->users as $user) {
                echo $user->kscore;
            }
        }

        $klout_topics_response = $this->klout->get('users/topics', array('users' => 'tbrandonbeasley'), 'json');

        if(count($klout_topics_response->users) > 0) {
            foreach($klout_topics_response->users as $user) {
                if(count($user->topics) > 0) {
                    foreach($user->topics as $topic) {
                        echo $topic;
                    }
                }
            }
        }
    }

    public function peerindex() {
        $peerindex_api_key = $this->config->item('PEERINDEX_API_KEY');

        $this->load->library('peerindex', array('api_key' => $peerindex_api_key));

        $peerindex_response = $this->peerindex->get('profile/show', array('id' => 'Bonnes_Affaires'), 'json');
        var_dump($peerindex_response);
        echo $peerindex_response->authority;
        echo $peerindex_response->audience;
        echo $peerindex_response->name;
        echo $peerindex_response->peerindex;

        if(!empty($peerindex_response->topics)) {
            foreach($peerindex_response->topics as $topic) {
                echo $topic;
            }
        }
    }

    public function twitter()
    {
         $this->load->library('twitter', array( 'consumer_key' => 'v7a2naVwpnjwnUuCXBcrww',
                                               'consumer_secret' => 'QItbzqwr5PFoLh3ro4K6pDtkfm3pnlcyIexZD5fwz4',
                                               'oauth_token' => '16962669-KxMtBs1B5CaHkVuq5oRLJzyhQ7lCjfPOyAiZ0zCzg',
                                               'oauth_token_secret' => '0JY8WHbPW48AXQM4ZIb1TfJxXQq0K6rVzsnvKAcE2I'));


        $this->_check_rate_limit();

        echo 'hiiiiiii';
        //var_dump($twitter_response);
        //$twitter_response = $this->twitter->get('account/rate_limit_status', array(), 'json');
        //var_dump($twitter_response->error);
        //$twitter_response = $this->twitter->get('followers/ids', array('screen_name' => 'Biz_Dell_AU', 'cursor' => -1), 'json');

        //var_dump($twitter_response);
        //echo $followers = $twitter_response->followers_count;
        //echo $following_count = $twitter_response->friends_count;
        //echo $list_count = $twitter_response->listed_count;
        

    }

    private function _check_rate_limit() {
        $hits = $this->twitter->get('account/rate_limit_status')->remaining_hits;

        if($hits > 345) {
            exit('nothing here');
        }
    }
}

?>