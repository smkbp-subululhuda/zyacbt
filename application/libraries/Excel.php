<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'vendor/autoload.php';

/**
 * Adapter kompatibilitas untuk kode lama yang masih memakai nama PHPExcel.
 */
if (!class_exists('PHPExcel')) {
    class PHPExcel extends \PhpOffice\PhpSpreadsheet\Spreadsheet {}
}

if (!class_exists('PHPExcel_IOFactory')) {
    class PHPExcel_IOFactory {
        public static function load($filename) {
            return \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
        }

        public static function createWriter($spreadsheet, $writerType) {
            $writerType = array(
                'Excel2007' => 'Xlsx',
                'Excel5' => 'Xls',
            )[$writerType] ?? $writerType;

            return \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $writerType);
        }
    }
}

class Excel extends PHPExcel {}
