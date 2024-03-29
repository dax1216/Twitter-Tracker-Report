<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @author Dax Panganiban
 * @created 10/11/2011
 
 */
class Peerindex {

    private $CI; // CodeIgniter instance
    private $api_url = 'http://api.peerindex.net/';
    private $api_key;
    private $version = '1';
    private $supported_formats = array(
        'xml' => 'application/xml',
        'json' => 'application/json',
        'serialize' => 'application/vnd.php.serialized',
        'php' => 'text/plain',
        'csv' => 'text/csv'
    );
    private $auto_detect_formats = array(
        'application/xml' => 'xml',
        'text/xml' => 'xml',
        'application/json' => 'json',
        'text/json' => 'json',
        'text/csv' => 'csv',
        'application/csv' => 'csv',
        'application/vnd.php.serialized' => 'serialize'
    );
    private $format;
    private $mime_type;
    private $response_string;

    function __construct($config = array()) {
        $this->CI = & get_instance();        

        $this->CI->load->library('curl');

        // If a URL was passed to the library
        if (!empty($config)) {
            $this->initialize($config);
        }
    }

    public function initialize($config) {
        $this->api_key = @$config['api_key'];

        if (substr($this->api_url, -1, 1) != '/') {
            $this->api_url .= '/';
        }

        $this->http_auth = isset($config['http_auth']) ? $config['http_auth'] : '';
        $this->http_user = isset($config['http_user']) ? $config['http_user'] : '';
        $this->http_pass = isset($config['http_pass']) ? $config['http_pass'] : '';
    }

    public function get($uri, $params = array(), $format = 'json') {
        if (!empty($params)) {
            $uri = $this->version . '/' . $uri . '.' . $format . '?' . 'api_key=' . $this->api_key . '&' . http_build_query($params);
        }
        
        return $this->_call('get', $uri, NULL, $format);
    }
    
    private function _call($method = 'get', $uri, $params = array(), $format = NULL) {
        if ($format !== NULL) {
            $this->format($format);
        }

        $this->_set_headers();

        // Initialize cURL session
        $this->CI->curl->create($this->api_url . $uri);

        // If authentication is enabled use it
        if ($this->http_auth != '' && $this->http_user != '') {
            $this->CI->curl->http_login($this->http_user, $this->http_pass, $this->http_auth);
        }

        // We still want the response even if there is an error code over 400
        $this->CI->curl->option('failonerror', FALSE);

        // Call the correct method with parameters
        $this->CI->curl->{$method}($params);

        // Execute and return the response from the REST server
        $response = $this->CI->curl->execute();

        // Format and return
        return $this->_format_response($response);
    }

    // If a type is passed in that is not supported, use it as a mime type
    public function format($format) {
        if (array_key_exists($format, $this->supported_formats)) {
            $this->format = $format;
            $this->mime_type = $this->supported_formats[$format];
        } else {
            $this->mime_type = $format;
        }

        return $this;
    }

    public function debug() {
        $request = $this->CI->curl->debug_request();

        echo "=============================================<br/>\n";
        echo "<h2>REST Test</h2>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Request</h3>\n";
        echo $request['url'] . "<br/>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Response</h3>\n";

        if ($this->response_string) {
            echo "<code>" . nl2br(htmlentities($this->response_string)) . "</code><br/>\n\n";
        } else {
            echo "No response<br/>\n\n";
        }

        echo "=============================================<br/>\n";

        if ($this->CI->curl->error_string) {
            echo "<h3>Errors</h3>";
            echo "<strong>Code:</strong> " . $this->CI->curl->error_code . "<br/>\n";
            echo "<strong>Message:</strong> " . $this->CI->curl->error_string . "<br/>\n";
            echo "=============================================<br/>\n";
        }

        echo "<h3>Call details</h3>";
        echo "<pre>";
        print_r($this->CI->curl->info);
        echo "</pre>";
    }

    private function _set_headers() {
        $this->CI->curl->http_header('Accept: ' . $this->mime_type);
    }

    private function _format_response($response) {
        $this->response_string = & $response;

// It is a supported format, so just run its formatting method
        if (array_key_exists($this->format, $this->supported_formats)) {
            return $this->{"_" . $this->format}($response);
        }

// Find out what format the data was returned in
        $returned_mime = @$this->CI->curl->info['content_type'];

// If they sent through more than just mime, stip it off
        if (strpos($returned_mime, ';')) {
            list($returned_mime) = explode(';', $returned_mime);
        }

        $returned_mime = trim($returned_mime);

        if (array_key_exists($returned_mime, $this->auto_detect_formats)) {
            return $this->{'_' . $this->auto_detect_formats[$returned_mime]}($response);
        }

        return $response;
    }

    // Format XML for output
    private function _xml($string) {
        return (array) simplexml_load_string($string);
    }

    // Format HTML for output
    // This function is DODGY! Not perfect CSV support but works with my REST_Controller
    private function _csv($string) {
        $data = array();

// Splits
        $rows = explode("\n", trim($string));
        $headings = explode(',', array_shift($rows));
        foreach ($rows as $row) {
// The substr removes " from start and end
            $data_fields = explode('","', trim(substr($row, 1, -1)));

            if (count($data_fields) == count($headings)) {
                $data[] = array_combine($headings, $data_fields);
            }
        }

        return $data;
    }

    // Encode as JSON
    private function _json($string) {
        return json_decode(trim($string));
    }

    // Encode as Serialized array
    private function _serialize($string) {
        return unserialize(trim($string));
    }

    // Encode raw PHP
    private function _php($string) {
        $string = trim($string);
        $populated = array();
        eval("\$populated = \"$string\";");
        return $populated;
    }

}

// END Klout Class

/* End of file Klout.php */
/* Location: ./application/libraries/Klout.php */