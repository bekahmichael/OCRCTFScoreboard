<?php

namespace app\components\media;

class Array2Xsls
{
    public $data;
    public $default;
    public $tpl_range = "{StartColumn}{StartRow}:{EndColumn}{EndRow}";

    public function __construct($data, $default = [])
    {
        $this->data = $data;
        $this->default = $default;
    }

    public function toFile($path)
    {
        $objPHPExcel = new \PHPExcel();
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        if (isset($this->default['style'])) {
            $styleArray = [];
            if (isset($this->default['style']['border-color'])) {
                $styleArray['borders'] = [
                    'allborders' => [
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['rgb' => ltrim($this->default['style']['border-color'], '#')],
                    ],
                ];
            }

            if (isset($this->default['style']['font-family'])) {
                $styleArray['font'] = [
                    'name' => $this->default['style']['font-family'],
                ];
            }

            if (isset($this->default['style']['vertical-align'])) {
                if ($this->default['style']['vertical-align'] === 'middle') {
                    $styleArray['alignment'] = [
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ];
                }
            }

            $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
        }

        $num_sheet = 1;
        foreach ($this->data as $sheetTitle => $rows) {
            if ($num_sheet === 1) {
                $objWorkSheet = $objPHPExcel->getActiveSheet();
                $objWorkSheet->setTitle($sheetTitle);
            } else {
                $objWorkSheet = $objPHPExcel->createSheet($num_sheet);
                $objWorkSheet->setTitle($sheetTitle);
            }

            foreach ($rows as $row_key => $row) {
                $column_key = 0;
                foreach ($row as $real_column_key => $column) {
                    $cell_name = \PHPExcel_Cell::stringFromColumnIndex($column_key) . ($row_key + 1);

                    if (isset($column['wrap'])) {
                        $objWorkSheet->getStyle($cell_name)->getAlignment()->setWrapText((bool)$column['wrap']);
                    }

                    if (isset($column['colspan'])) {
                        $cell_name_megre = $cell_name . ':' . \PHPExcel_Cell::stringFromColumnIndex($column_key - 1 + $column['colspan']) . ($row_key + 1);
                    } else {
                        if (isset($column['rowspan'])) {
                            $cell_name_megre = $cell_name . ':' . \PHPExcel_Cell::stringFromColumnIndex($column_key) . ($row_key + $column['rowspan']);
                        } else {
                            $cell_name_megre = $cell_name;
                        }
                    }

                    $objWorkSheet->setCellValue($cell_name, $column['value']);

                    if (isset($column['indent'])) {
                        $objWorkSheet->getStyle($cell_name)->getAlignment()->setIndent($column['indent']);
                    }

                    if (isset($column['style'])) {
                        if (isset($column['style']['font-size'])) {
                            $objWorkSheet->getStyle($cell_name)->getFont()->setSize($column['style']['font-size']);
                        }
                        if (isset($column['style']['font-family'])) {
                            $objWorkSheet->getStyle($cell_name)->getFont()->setName($column['style']['font-family']);
                        }
                        if (isset($column['style']['background'])) {
                            $objWorkSheet
                            ->getStyle($cell_name)
                            ->getFill()
                            ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB(ltrim($column['style']['background'], '#'));
                        }
                        if (isset($column['style']['vertical-align'])) {
                            if ($column['style']['vertical-align'] === 'middle') {
                                $objWorkSheet->getStyle($cell_name)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            }
                        }
                        if (isset($column['style']['color'])) {
                            $phpColor = new \PHPExcel_Style_Color();
                            $phpColor->setRGB(ltrim($column['style']['color'], '#'));
                            $objWorkSheet
                                ->getStyle($cell_name)
                                ->getFont()
                                ->setColor($phpColor);
                        }
                        if (isset($column['style']['font-weight'])) {
                            if ($column['style']['font-weight'] == 'bold') {
                                $objWorkSheet->getStyle($cell_name)->getFont()->setBold(true);
                            }
                        }

                        if (isset($column['style']['width'])) {
                            $objWorkSheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($column_key))->setWidth($column['style']['width']);
                        }
                        if (isset($column['style']['height'])) {
                            $objWorkSheet->getRowDimension($row_key + 1)->setRowHeight($column['style']['height']);
                        }

                        if (isset($column['style']['text-align'])) {
                            if ($column['style']['text-align'] === 'center') {
                                $objWorkSheet->getStyle($cell_name)->applyFromArray([
                                    'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
                                ]);
                            } else if ($column['style']['text-align'] === 'right') {
                                $objWorkSheet->getStyle($cell_name)->applyFromArray([
                                    'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT],
                                ]);
                            } else if ($column['style']['text-align'] === 'left') {
                                $objWorkSheet->getStyle($cell_name)->applyFromArray([
                                    'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
                                ]);
                            }
                        }

                        if (isset($column['style']['border-color'])) {
                            $objWorkSheet->getStyle($cell_name_megre)->applyFromArray(
                                array(
                                    'borders' => array(
                                        'allborders' => array(
                                            'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array('rgb' => ltrim($column['style']['border-color'], '#'))
                                        )
                                    )
                                )
                            );
                        }

                        if (isset($column['style']['border-botton-color'])) {
                            $objWorkSheet->getStyle($cell_name_megre)->applyFromArray(
                                array(
                                    'borders' => array(
                                        'bottom' => array(
                                            'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array('rgb' => ltrim($column['style']['border-botton-color'], '#'))
                                        )
                                    )
                                )
                            );
                        }

                        if (isset($column['style']['border-top-color'])) {
                            $objWorkSheet->getStyle($cell_name_megre)->applyFromArray(
                                array(
                                    'borders' => array(
                                        'top' => array(
                                            'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array('rgb' => ltrim($column['style']['border-top-color'], '#'))
                                        )
                                    )
                                )
                            );
                        }

                        if (isset($column['style']['border-left-color'])) {
                            $objWorkSheet->getStyle($cell_name_megre)->applyFromArray(
                                array(
                                    'borders' => array(
                                        'left' => array(
                                            'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array('rgb' => ltrim($column['style']['border-left-color'], '#'))
                                        )
                                    )
                                )
                            );
                        }
                    }

                    if (isset($column['colspan'])) {
                        $objWorkSheet->mergeCells(strtr($this->tpl_range, [
                            '{StartColumn}' => \PHPExcel_Cell::stringFromColumnIndex($column_key),
                            '{StartRow}'    => $row_key + 1,
                            '{EndColumn}'   => \PHPExcel_Cell::stringFromColumnIndex($column_key + $column['colspan'] - 1),
                            '{EndRow}'      => $row_key + 1,
                        ]));
                        $column_key += $column['colspan'] - 1;
                    }



                    if (isset($column['rowspan'])) {
                        $objWorkSheet->mergeCells(strtr($this->tpl_range, [
                            '{StartColumn}' => \PHPExcel_Cell::stringFromColumnIndex($column_key),
                            '{StartRow}'    => $row_key + 1,
                            '{EndColumn}'   => \PHPExcel_Cell::stringFromColumnIndex($column_key),
                            '{EndRow}'      => $row_key + $column['rowspan'],
                        ]));
                    }

                    if (isset($column['format'])) {
                        if ($column['format'] === 'text') {
                            $objWorkSheet
                                ->getStyle($cell_name)
                                ->getNumberFormat()
                                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        } else if ($column['format'] === 'number') {
                            $objWorkSheet
                                ->getStyle($cell_name)
                                ->getNumberFormat()
                                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
                        } else if ($column['format'] === 'percent') {
                            $objWorkSheet
                                ->getStyle($cell_name)
                                ->getNumberFormat()
                                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                        } else if ($column['format'] === 'date') {
                            $objWorkSheet
                                ->getStyle($cell_name)
                                ->getNumberFormat()
                                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
                        } else {
                            $objWorkSheet
                                ->getStyle($cell_name)
                                ->getNumberFormat()
                                ->setFormatCode($column['format']);
                        }
                    }

                    if (isset($column['freeze'])) {
                        if ($column['freeze'] === true) {
                            $objWorkSheet->freezePane(\PHPExcel_Cell::stringFromColumnIndex($column_key) . ($row_key + 2));
                        }
                    }

                    $column_key++;
                }
            }

            $num_sheet++;
        }

        $objWriter->setPreCalculateFormulas(true);

        $objWriter->save($path);
    }

    public function toDownload($file_name)
    {
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $this->toFile('php://output');
        exit;
    }
}
