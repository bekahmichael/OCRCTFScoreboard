<?php

namespace app\components\media;

class Xslsx2Array
{
    private $path;
    private $objReader;

    public function __construct($path)
    {
        $this->path = $path;
        $this->objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $this->objReader->setReadDataOnly(true);
    }

    public function canRead()
    {
        return $this->objReader->canRead($this->path);
    }

    public function toArray()
    {
        $output = [];

        $objPHPExcel = $this->objReader->load($this->path);

        for ($i = 0; $i < $objPHPExcel->getSheetCount(); $i++) {
            $objWorksheet = $objPHPExcel->setActiveSheetIndex($i);
            $sheetName = $objPHPExcel->getActiveSheet()->getTitle();
            $output[$sheetName] = [];

            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

            for ($row = 1; $row <= $highestRow; ++$row) {
                $output[$sheetName][$row] = [];
                for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                    $output[$sheetName][$row][$col] = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                }
            }
        }

        return $output;
    }
}