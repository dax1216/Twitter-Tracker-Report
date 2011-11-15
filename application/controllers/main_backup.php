<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $klout_api_key = $this->config->item('KLOUT_API_KEY');
        $peerindex_api_key = $this->config->item('PEERINDEX_API_KEY');

        $this->load->library('twitter');
        $this->load->library('klout', array('api_key' => $klout_api_key));
        $this->load->library('peerindex', array('api_key' => $peerindex_api_key));

        $this->load->model('report_model');
    }

    public function index() {
        $data['content'] = $this->load->view('main/index', null, TRUE);

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
                        
                        $temp[]= $username = trim($data[0]);
                        $twitter_response = $this->twitter->get('users/show', array('screen_name' => $username), 'json');

                        $twitter_id = isset($twitter_response->id) ? $twitter_response->id : null;

                        if(is_null($twitter_id)) {
                            continue;
                        }
                        
                        $temp[]= $followers = isset($twitter_response->followers_count) ? $twitter_response->followers_count : '';
                        $temp[]= $following_count = isset($twitter_response->friends_count) ? $twitter_response->friends_count : '';
                        $temp[]= $list_count = isset($twitter_response->listed_count) ? $twitter_response->listed_count : '';

                        if(!empty($followers)) {
                            $res = $this->twitter->get('followers/ids', array('screen_name' => $username, 'cursor' => -1), 'json');
                            $followers_ids = $res->ids;
                        } else {
                            $followers_ids = array();
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

                        if(isset($klout_topics_response->users) && count($klout_topics_response->users) > 0) {
                            foreach($klout_topics_response->users as $user) {
                                $klout_topics = array();

                                if(count($user->topics) > 0) {
                                    foreach($user->topics as $topic) {
                                        $klout_topics []= $topic;
                                    }
                                }
                            }
                        }

                        $peerindex_response = $this->peerindex->get('profile/show', array('id' => $username), 'json');

                        $temp[]= $name = isset($peerindex_response->name) ? $peerindex_response->name : '';
                        $temp[]= $authority = isset($peerindex_response->authority) ? $peerindex_response->authority : '';
                        $temp[]= $audience = isset($peerindex_response->audience) ? $peerindex_response->audience : '';                        
                        $temp[]= $peerindex = isset($peerindex_response->peerindex) ? $peerindex_response->peerindex : '';

                        $peerindex_topics = array();

                        if(!empty($peerindex_response->topics)) {
                            foreach($peerindex_response->topics as $topic) {
                                $peerindex_topics []= $topic;
                            }
                        }

                        echo "Twitter Handle: {$username} --- # of Followers: {$followers} --- # Following: {$following_count} --- # of lists {$list_count} --- ";
                        echo "Name: {$name} --- Authority: {$authority} --- Audience: {$audience} --- PeerIndex: {$peerindex} --- ";

                        for($ctr = 0, $topic_ctr = 1; $ctr < 5; $ctr++, $topic_ctr++) {
                            $temp[]= $pi_topic = (isset($peerindex_topics[$ctr])) ? $peerindex_topics[$ctr] : '';

                            echo "Topic{$topic_ctr}: {$pi_topic} --- ";
                        }

                        echo "Klout Score: {$kscore} --- ";
                        $temp[] = $kscore;
                        
                        for($ctr = 0, $topic_ctr = 1; $ctr < 3; $ctr++, $topic_ctr++) {
                            $temp[]= $kl_topic = (isset($klout_topics[$ctr])) ? $klout_topics[$ctr] : '';

                            echo "Topic{$topic_ctr}: {$kl_topic} --- ";
                        }

                        echo "<br />";
                        echo "<br />";

                        //$report[] = $temp;
                        //$followers_data[] = $this->_extract_followers_data($followers_ids);
                        $account_data = array(  'report_id' => $report_id,
                                                'twitter_id' => $twitter_id,
                                                'username' => $username,
                                                'name' => $name,
                                                'audience' => $audience,
                                                'authority' => $authority,
                                                'followers' => $followers,
                                                'following' => $following_count,
                                                'list_count' => $list_count,
                                                'peerindex' => $peerindex,
                                                'peerindex_topics' => serialize($peerindex_topics),
                                                'klout_score' => $kscore,
                                                'klout_topics' => serialize($klout_topics)
                                                );
                        sleep(12);
                        ob_flush();
                        flush();
                    }
                    echo "</div>";
                    
                    fclose($handle);

                }                
                
                ob_end_clean();                

                unset($_SESSION['main_accounts'], $_SESSION['extended_accounts']);
                
                $_SESSION['main_accounts'] = serialize($report);
                $_SESSION['extended_accounts'] = serialize($followers_data);
            } else {
                echo 'No file submitted. Please upload csv file.';
            }
        }
    }

    private function _extract_followers_data($followers_ids) {
        if(!empty($followers_ids)) {
            $report = array();
            
            foreach($followers_ids as $id) {
                $temp = array();

                $twitter_response = $this->twitter->get('users/show', array('user_id' => $id), 'json');

                $temp[]= $username = isset($twitter_response->screen_name) ? $twitter_response->screen_name : '';
                $temp[]= $followers = isset($twitter_response->followers_count) ? $twitter_response->followers_count : '';
                $temp[]= $following_count = isset($twitter_response->friends_count) ? $twitter_response->friends_count : '';
                $temp[]= $list_count = isset($twitter_response->listed_count) ? $twitter_response->listed_count : '';

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

                if(isset($klout_topics_response->users) && count($klout_topics_response->users) > 0) {
                    foreach($klout_topics_response->users as $user) {
                        $klout_topics = array();

                        if(count($user->topics) > 0) {
                            foreach($user->topics as $topic) {
                                $klout_topics []= $topic;
                            }
                        }
                    }
                }

                $peerindex_response = $this->peerindex->get('profile/show', array('id' => $username), 'json');

                $temp[]= $name = isset($peerindex_response->name) ? $peerindex_response->name : '';
                $temp[]= $authority = isset($peerindex_response->authority) ? $peerindex_response->authority : '';
                $temp[]= $audience = isset($peerindex_response->audience) ? $peerindex_response->audience : '';
                $temp[]= $peerindex = isset($peerindex_response->peerindex) ? $peerindex_response->peerindex : '';

                $peerindex_topics = array();

                if(!empty($peerindex_response->topics)) {
                    foreach($peerindex_response->topics as $topic) {
                        $peerindex_topics []= $topic;
                    }
                }

                echo "Twitter Handle: {$username} --- # of Followers: {$followers} --- # Following: {$following_count} --- # of lists {$list_count} --- ";
                echo "Name: {$name} --- Authority: {$authority} --- Audience: {$audience} --- PeerIndex: {$peerindex} --- ";

                for($ctr = 0, $topic_ctr = 1; $ctr < 5; $ctr++, $topic_ctr++) {
                    $temp[]= $pi_topic = (isset($peerindex_topics[$ctr])) ? $peerindex_topics[$ctr] : '';

                    echo "Topic{$topic_ctr}: {$pi_topic} --- ";
                }

                echo "Klout Score: {$kscore} --- ";
                $temp[] = $kscore;

                for($ctr = 0, $topic_ctr = 1; $ctr < 3; $ctr++, $topic_ctr++) {
                    $temp[]= $kl_topic = (isset($klout_topics[$ctr])) ? $klout_topics[$ctr] : '';

                    echo "Topic{$topic_ctr}: {$kl_topic} --- ";
                }

                $report[] = $temp;

                echo "<br />";
                echo "<br />";

                sleep(1);
                ob_flush();
                flush();
            }

            return $report;
        } else {
            return array();
        }
    }

    public function export() {
        session_start();
        
        if(isset($_SESSION['main_accounts'])) {
            $main_accounts = unserialize($_SESSION['main_accounts']);
            $extended_accounts = unserialize($_SESSION['extended_accounts']);
            
            $data = "Twitter Handle, # of Followers, # Following, # of lists, Name, Authority, Audience, PeerIndex, Topic1, Topic2, ";
            $data .= "Topic3, Topic4, Topic5, Klout Score, Topic1, Topic2, Topic3";
            $data .= "\n";

            $filename = 'report.csv';

            $ctr = 0;
            $row_count = 1;
            foreach($main_accounts as $row) {
                $data .=  $row_count . ','. implode(',', $row);
                $data .= "\n";

                if(isset($extended_accounts[$ctr]) && count($extended_accounts[$ctr]) > 0) {
                    $ext_row_count = 1;
                    foreach($extended_accounts[$ctr] as $ext_row) {
                        $data .= $row_count . '.' . $ext_row_count . ','. implode(',', $row);
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
    }
}

?>