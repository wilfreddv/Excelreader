<?php

require_once 'controller.php';

class Model {
    private $controller;

    public function __construct() {
        $this->controller = new Controller();
    }

    /** 
     * Return an array of all files in the excel file folder
     */
    function get_index() {
        return $this->controller->get_files();
    }

    /**
     * Load a file
     */
    function get_file($filename) {
        return (new Controller())->read_file($filename);
    }
};

?>