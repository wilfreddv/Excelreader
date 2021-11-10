<?php

use \Psr\Http\Message\UploadedFileInterface;
use \SimpleXLSX as XLSX;

include_once 'SimpleXLSX.php';


class Controller {
    private $EXCEL_FILE_DIRECTORY = __DIR__ . '/excelfiles/';

    /**
     * Return all filenames in EXCEL_FILE_DIRECTORY
     * 
     * @return array[str] all filenames in EXCEL_FILE_DIRECTORY
     */
    function get_files() {
        return array_slice(scandir($this->EXCEL_FILE_DIRECTORY), 2);
    }

    /** 
     * Extract Excel data to HTMl
     *
     * @param UploadedFileInterface $file name of the file
     *  
     * @return bool true on success, false on failure
     */
    function extract_excel_to_disk(UploadedFileInterface $file) {
        $filepath = __DIR__ . '/excelfiles/' . pathinfo($file->getClientFilename(), PATHINFO_FILENAME);
        $file->moveTo($filepath . '.tmp');

        $xlsx = new SimpleXLSX($filepath . '.tmp');
        if( $xlsx->success() ) {
            // Use custom HTML writer instead of SimpleXLSX::toHTML()
            // for more control over the layout
            $this->write_excel($xlsx, $filepath);
        }
        else {
            return false;
        }

        unlink($filepath . '.tmp'); // Don't forget to remove the tmp file
        return true;
    }

    /**
     * Read a file from the EXCEL_FILE_DIRECTORY directory
     * 
     * @param string $filename File to read
     * 
     * @return string Contents of the file, false on failure
     */
    function read_file($filename) {
        $file_path = $this->EXCEL_FILE_DIRECTORY . $filename;
        return file_get_contents($file_path);
    }

    /**
     * Delete a file from the EXCEL_FILE_DIRECTORY directory
     * 
     * @param string $filename File to delete
     * 
     * @return bool true on success, false on failure
     */
    function delete_file($filename) {
        return unlink($this->EXCEL_FILE_DIRECTORY . $filename);
    }

    /**
     * Write XLSX object as HTML
     * 
     * @param XLSX $xlsx XLSX object to write as HTML
     * @param string $path File to write to
     */
    private function write_excel(XLSX $xlsx, $path) {
        $file = fopen($path, "w");
        
        fwrite($file, "<pre><table>");
        foreach( $xlsx->rows() as $row ) {
            $row = array_map('htmlentities', $row);
            fwrite($file, "<tr><td>".implode("</td><td>",$row)."</td></tr>");
        }
        fwrite($file, "</table></pre>");
    }

};

?>