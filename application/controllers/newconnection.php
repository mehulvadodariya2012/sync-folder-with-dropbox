<?php
/**
 * @author MJV
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Newconnection extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
    }
    function index(){
        redirect('newconnection/request_dropbox');
    }

    public function request_dropbox() {
        $this->load->library('dropbox');
        $data = $this->dropbox->get_request_token(site_url("newconnection/access_dropbox"));
        $this->session->set_userdata('token_secret', $data['token_secret']);
        redirect($data['redirect']);
    }

    public function access_dropbox() {
        $this->load->library('dropbox');
        $oauth = $this->dropbox->get_access_token($this->session->userdata('token_secret'));
        $this->session->set_userdata('oauth_token', $oauth['oauth_token']);
        $this->session->set_userdata('oauth_token_secret', $oauth['oauth_token_secret']);
        redirect('newconnection/test_dropbox');
    }

    public function test_dropbox() {
        echo "<b>Set this two variable in config file.</b>";
        echo "<hr/><br/>oauth_token => ".$_SESSION['oauth_token'];
        echo "<br/>oauth_token_secret => ".$_SESSION['oauth_token_secret'];
        $params['access'] = array('oauth_token' => urlencode($this->session->userdata('oauth_token')),
            'oauth_token_secret' => urlencode($this->session->userdata('oauth_token_secret')));

        echo "<br/><pre>";
        $this->load->library('dropbox',$params);
        $dbobj = $this->dropbox->account();
        $dbobj = $this->dropbox->metadata('/');
        print_r($dbobj);
    }

}

/* End of file newconnection.php */
/* Location: ./application/controllers/welcome.php */