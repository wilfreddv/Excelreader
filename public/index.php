<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../application/view.php';
require_once __DIR__ . '/../application/controller.php';


$app = AppFactory::create();


$app->get('/', function (Request $request, Response $response, $args) {
    // Check if argument `d` is passed
    $view = new View();

    if( ($to_delete = $request->getQueryParams()['d'] ) ) {
        if( !(new Controller())->delete_file($to_delete) ) { // Error deleting file
            $view->add_flash("Error deleting file `" .$to_delete. "`.");
        }
    }

    $response->getBody()->write($view->index());
    return $response;
});

/**
 * Route for uploading files
 */
$app->post('/', function (Request $request, Response $response, $args) {
    $view = new View();

    $uploadedFile = $request->getUploadedFiles()['fileToUpload'];
    if( $uploadedFile->getError() !== UPLOAD_ERR_OK ) {
        // Upload failed
        $view->add_flash("Error uploading file.");
    }
    else {
        if( !(new Controller())->extract_excel_to_disk($uploadedFile) ) {
            $view->add_flash("Something went wrong processing the file.");
        }
        else {
            $view->add_flash("File uploaded successfully.");
        }
    }

    $response->getBody()->write($view->index());
    return $response;
});

/**
 * Route to display a file
 */
$app->get('/{filename}', function (Request $request, Response $response, $args) {
    $view = new View();
    $response->getBody()->write($view->display_file($args['filename']));
    return $response;
});


$app->run();
?>