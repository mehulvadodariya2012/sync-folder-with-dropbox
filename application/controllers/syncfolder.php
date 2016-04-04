<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Syncfolder extends CI_Controller {

    var $dropboxPath = '/'; //If the dropbox folder is root then only slash otherwise e.g /mydocs/office/
    var $serverpath = 'test'; // give your server path where you want to save the dropboxfiles 

    public function __construct() {
        parent::__construct();
        ini_set('max_execution_time', 0);
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('dropbox');
    }

    function index($dropboxPath = false) {

        if ($dropboxPath)
            $metadeta = $this->dropbox->metadata($dropboxPath);
        else
            $metadeta = $this->dropbox->metadata($this->dropboxPath);
        foreach ($metadeta->contents as $key => $dirItem) {
            if ($dirItem->is_dir != '1') {
                $dpFilePath = $dirItem->path;
                //echo "<br/>LocalPath ->" . $this->serverpath . $dirItem->path;

                if (!file_exists(urldecode($this->serverpath . $dpFilePath))) {
                    $drop_media = $this->dropbox->media($dpFilePath);
                    if (!empty($drop_media->url)) {

                        $info = pathinfo($drop_media->url);
                        $file_name = $info['basename'];

                        $content = file_get_contents($drop_media->url);
                        file_put_contents(urldecode($this->serverpath . $dirItem->path), $content);
                        chmod($this->serverpath .$dirItem->path, 0755);
                        $this->dropbox->delete($dpFilePath);
                        //echo "<br/>so download from dropbox";
                    }
                } else {
                    //echo "<br/>File already downloaded->" . $dirItem->path;
                }
            } elseif ($dirItem->is_dir == '1') {
                // Comment this else part if you dont want to download sub-folders and files 
                $path = $this->serverpath . $dirItem->path;
                if (!file_exists($path) && !is_dir($path)) {
                    mkdir($this->serverpath . $dirItem->path);
                    chmod($this->serverpath . $dirItem->path, 0755);
                }
                $this->index($dirItem->path); 
                $this->dropbox->delete($dirItem->path);
            }
            //echo "<br/>---------------------";
        }
    }

}
