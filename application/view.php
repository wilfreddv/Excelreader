<?php
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

require 'model.php';


class View {
    private $TEMPLATE_DIR = '../application/templates/';
    private $loader, $twig, $data;
    private $flashes = array();


    public function __construct() {
        $this->loader = new FilesystemLoader($this->TEMPLATE_DIR);
        $this->twig = new Environment($this->loader);
        $this->data['title'] = "Excelreader";
    }

    /**
     * Return index view
     */
    function index() {
        $model = new Model();
        $this->data['files'] = $model->get_index();
        $template = $this->twig->load('index.html');
        return $template->render($this->data);
    }

    /**
     * Display a file
     * 
     * @param string $filename name of the file to display
     * 
     * @return string render of the file
     */
    function display_file($filename) {
        $this->data['title'] .= " - ".$filename;
        $template = $this->twig->load('view_file.html');
        $f_data = (new Model())->get_file($filename);
        
        if( !$f_data ) {
            $this->add_flash("Error reading file `".$filename."`");
        }
        else {
            $this->data['file_content'] = $f_data;
        }

        return $template->render($this->data); 
    }

    /**
     * Add a message to appear at the top of the page
     * 
     * @param string $flash message to appear
     */
    function add_flash($flash) {
        array_push($this->flashes, $flash);
        $this->data['flashes'] = $this->flashes;
    }
};

?>