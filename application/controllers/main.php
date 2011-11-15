<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $klout_api_key = $this->config->item('KLOUT_API_KEY');
        $peerindex_api_key = $this->config->item('PEERINDEX_API_KEY');

        $this->load->library('twitter', array( 'consumer_key' => $this->config->item('CONSUMER_KEY'),
                                               'consumer_secret' => $this->config->item('CONSUMER_SECRET'),
                                               'oauth_token' => $this->config->item('OAUTH_TOKEN'),
                                               'oauth_token_secret' => $this->config->item('OAUTH_TOKEN_SECRET')));

        $this->load->library('klout', array('api_key' => $klout_api_key));
        $this->load->library('peerindex', array('api_key' => $peerindex_api_key));

        $this->load->model('report_model');
        $this->load->helper('util');
    }

    public function index() {
        $content['reports'] = $this->report_model->get_reports();

        $data['content'] = $this->load->view('main/index', $content, TRUE);

        $this->load->view('layout', $data);
    }

    public function process() {
        if(isset($_POST['submit'])) {                    
            if($_FILES['csv']['size'] > 0) {
                $file = $_FILES['csv']['tmp_name'];

                session_start();
                
                ob_start();

                $report = array();
                $followers_data = array();
                
                echo "<div style='font-size: 11px;'>";
                
                if (($handle = fopen($file, "r")) !== FALSE) {
                    $report_data = array('report_name' => $_FILES['csv']['name'], 'date' => date('Y-m-d H:i:s'), 'status' => '0');

                    $report_id = $this->report_model->insert_report($report_data);
                    
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $temp = array();
                        
                        $username = trim($data[0]);                       
                       
                        $account_data = array(  'report_id' => $report_id,                                                
                                                'username' => $username                                                
                                                );

                        $this->report_model->insert_account($account_data);
                    }                    
                    
                    fclose($handle);

                }                
                
                $this->session->set_flashdata( 'message', array('content' => 'Twitter data uploaded. The report will be available as soon as all data has been gathered.', 'type' => 'success' ));
            } else {
                $this->session->set_flashdata( 'message', array('content' => 'No file uploaded. Please upload a csv file.', 'type' => 'error' ));
            }

            redirect('main/');
        }
    }
    
    public function extract_data() {
        // Limit extracting of data from APIs to an hour only
        set_time_limit(60*60);

        $report = $this->report_model->get_incomplete_reports();
        
        $accounts = $this->report_model->get_accounts($report['id'], 0);

        if(count($accounts) > 0) {
            foreach($accounts as $row) {
                $this->_check_rate_limit();

                if($row['username']) {
                    $twitter_response = $this->twitter->get('users/show', array('screen_name' => $row['username']));
                } else {
                    $twitter_response = $this->twitter->get('users/show', array('user_id' => $row['twitter_id']));
                }

                if(isset($twitter_response->error)) {
                    $this->report_model->update_account($row['id'], array('status' => 1));

                    sleep(5);
                } else {
                    $username = isset($twitter_response->screen_name) ? $twitter_response->screen_name : null;

                    $twitter_id = isset($twitter_response->id) ? $twitter_response->id : null;

                    $name = isset($twitter_response->name) ? $twitter_response->name : '';
                    $followers = isset($twitter_response->followers_count) ? $twitter_response->followers_count : '';
                    $following_count = isset($twitter_response->friends_count) ? $twitter_response->friends_count : '';
                    $list_count = isset($twitter_response->listed_count) ? $twitter_response->listed_count : '';

                    if(!empty($followers) && $row['reference_id'] == null) {
                        $this->_check_rate_limit();
                        
                        $res = $this->twitter->get('followers/ids', array('screen_name' => $username, 'cursor' => -1));
                        $followers_ids = $res->ids;

                        foreach($followers_ids as $follower_id) {
                            $this->report_model->insert_account(array('report_id' => $report['id'], 'reference_id' => $row['id'], 'twitter_id' => $follower_id));
                        }
                    }

                    $klout_score_response = $this->klout->get('klout', array('users' => $username), 'json');

                    if(isset($klout_score_response->users) && count($klout_score_response->users) > 0) {
                        foreach($klout_score_response->users as $user) {
                            $kscore = $user->kscore;
                            break;
                        }
                    } else {
                        $kscore = '';
                    }

                    $klout_topics_response = $this->klout->get('users/topics', array('users' => $username), 'json');

                    $klout_topics = array();

                    if(isset($klout_topics_response->users) && count($klout_topics_response->users) > 0) {
                        foreach($klout_topics_response->users as $user) {                            
                            if(count($user->topics) > 0) {
                                foreach($user->topics as $topic) {
                                    $klout_topics []= $topic;
                                }
                            }
                        }
                    }

                    $peerindex_response = $this->peerindex->get('profile/show', array('id' => $username), 'json');
                    
                    $authority = isset($peerindex_response->authority) ? $peerindex_response->authority : '';
                    $audience = isset($peerindex_response->audience) ? $peerindex_response->audience : '';
                    $peerindex = isset($peerindex_response->peerindex) ? $peerindex_response->peerindex : '';

                    $peerindex_topics = array();

                    if(!empty($peerindex_response->topics)) {
                        foreach($peerindex_response->topics as $topic) {
                            $peerindex_topics []= $topic;
                        }
                    }

                    $account_data = array(  'username' => $username,
                                            'twitter_id' => $twitter_id,
                                            'name' => $name,
                                            'audience' => $audience,
                                            'authority' => $authority,
                                            'followers' => $followers,
                                            'following' => $following_count,
                                            'list_count' => $list_count,
                                            'peerindex' => $peerindex,
                                            'peerindex_topics' => serialize($peerindex_topics),
                                            'klout_score' => $kscore,
                                            'klout_topics' => serialize($klout_topics),
                                            'status' => 1
                                            );

                    $this->report_model->update_account($row['id'], $account_data);

                    sleep(5.5);
                }
            }

            if($this->report_model->check_report_status($report['id']))
                $this->report_model->update_report_status($report['id']);
        }
    }     

    public function export($report_id) {
            
        $report = $this->report_model->get_report($report_id);

        if($report['status'] == 0) {
            $this->session->set_flashdata( 'message', array('content' => 'This report is still in progress. Download will be unavailable.', 'type' => 'error' ));
            redirect('main/');
        }

        $data = "#, Twitter Handle, # of Followers, # Following, # of lists, Name, Authority, Audience, PeerIndex, Topic1, Topic2, ";
        $data .= "Topic3, Topic4, Topic5, Klout Score, Topic1, Topic2, Topic3";
        $data .= "\n";

        $filename = 'report.csv';

        $main_accounts = $this->report_model->get_main_accounts($report_id);
        
        $ctr = 0;
        $row_count = 1;
        foreach($main_accounts as $row) {
            $data .=  "\"{$row_count}\",";

            $data .= $this->_format_csv($row);

            $data .= "\n";

            $followers_data = $this->report_model->get_followers($report_id, $row['id']);
            
            if(count($followers_data) > 0) {
                $ext_row_count = 1;
                foreach($followers_data as $ext_row) {
                    $data .= "\"{$row_count}.{$ext_row_count}\",";

                    $data .= $this->_format_csv($ext_row);

                    $data .= "\n";
                    $ext_row_count++;
                }
            }

            $row_count++;
            $ctr++;
        }

        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
        {
            header('Content-Type: "text/x-comma-separated-values"');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Content-Transfer-Encoding: binary");
            header('Pragma: public');
            header("Content-Length: ".strlen($data));
        }
        else
        {
            header('Content-Type: "text/csv"');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header("Content-Transfer-Encoding: binary");
            header('Expires: 0');
            header('Pragma: no-cache');
            header("Content-Length: ".strlen($data));
        }

        exit($data);
    }

    private function _format_csv($row) {
        $data = "";
        $data .= "\"{$row['username']}\", \"{$row['followers']}\", \"{$row['following']}\", \"{$row['list_count']}\",";
        $data .= "\"{$row['name']}\", \"{$row['audience']}\", \"{$row['authority']}\", \"{$row['peerindex']}\",";

        $peerindex_topics = unserialize($row['peerindex_topics']);

        if(is_array($peerindex_topics)) {
            for($ctr = 0, $topic_ctr = 1; $ctr < 5; $ctr++, $topic_ctr++) {
                $data .= (isset($peerindex_topics[$ctr])) ? "\"{$peerindex_topics[$ctr]}\"," : "\"\",";
            }
        }

        $data .= "\"{$row['klout_score']}\",";

        $klout_topics = unserialize($row['klout_topics']);

        if(is_array($klout_topics)) {
            for($ctr = 0, $topic_ctr = 1; $ctr < 3; $ctr++, $topic_ctr++) {
                $data .= (isset($klout_topics[$ctr])) ? "\"{$klout_topics[$ctr]}\"," : "\"\",";
            }
        }
        $data = rtrim($data, ',');

        return $data;

    }

    public function remove_report($report_id) {
        $this->report_model->remove_report($report_id);

        $this->session->set_flashdata( 'message', array('content' => 'Report removed.', 'type' => 'success' ));

        redirect('main');
    }

    private function _check_rate_limit() {
        $hits = $this->twitter->get('account/rate_limit_status')->remaining_hits;

        if($hits > 350) {
            exit('Twitter API hit limit exceeded.');
        }
    }
}

?>