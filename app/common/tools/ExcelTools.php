<?php

namespace app\common\tools;

use Closure;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExcelTools
{

    //读取excel, 支持一个excel中多张工作表, 要求格式为第一行名称,第二行开始数据
    static  public function getExcel($files)
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($files);

        $arr = [];
        //一个excel多个工作表,循环读取
        foreach ($spreadsheet->getWorksheetIterator() as $k => $worksheet) {
            //工作表的名称
            $worksheetTitle = $worksheet->getTitle();
            $arr[$k]['title'] = $worksheetTitle;
            //获取行数,返回的是数字
            $highestRow = $worksheet->getHighestRow();
            //获取列数,返回的是字母
            $highestColumn = $worksheet->getHighestColumn();
            //列数字母索引转数字
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            $data = [];
            for ($i = 2; $i <= $highestRow; $i++) {
                for ($j = 1; $j <= $highestColumnIndex; $j++) {
                    $data[$i][$j] = $worksheet->getCellByColumnAndRow($j, $i)->getValue();
                }
            }

            $arr[$k]['data'] = $data;
        }

        return $arr;
    }

    /**
     * 导出excel表头模板
     *   $title = [
     *       ['column' => 'B', 'title' => '姓名',],
     *       ['column' => 'c', 'title' => '性别', 'func' => function ($value) {
     *           return ($value == '1') ? '男' : '女';
     *       }],
     *       ['column' => 'd', 'title' => '年龄'],
     *       ['column' => 'e', 'title' => '身份证'],
     *   ];
     *   $title = [
     *       ['title' => '姓名',],
     *       ['title' => '性别', 'func' => function ($value) {
     *           return ($value == '1') ? '男' : '女';
     *       }],
     *       ['title' => '年龄'],
     *       ['title' => '身份证'],
     *   ];
     */
    static public  $options = [
        'titles' => [],
        'file_name' => '导出数据',
        'is_order' => true,
        'start_line' => 2,
        //设定样式
        //所有sheet的表头样式 加粗
        'font' => [
            'bold' => true,
            'size' => 20,
        ],
        //所有sheet的内容样式-加黑色边框
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
            'inside' => [
                'borderStyle' => Border::BORDER_THIN,
            ]
        ],

        //样式设置 - 水平、垂直居中
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],

    ];
    //信息导出excel
    static public function outputProjectExcel($datas, $options = [])
    {
        // $options
        self::$options = array_merge(self::$options, $options);

        if (is_object($datas)) {
            $datas = $datas->toArray();
        }
        $newExcel = new Spreadsheet(); //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet(); //获取当前操作sheet的对象
        $date = date('Ymd', time());
        $file_name = self::$options['file_name'] . $date;

        $objSheet->setTitle($file_name); //设置当前sheet的标题


        $objSheet->setCellValue('A1', $file_name); //设置表格标题

        $titls = [];
        if (!empty(self::$options['titles'])) {

            $key = 0;
            if (self::$options['is_order'] == true) {
                $objSheet->setCellValue('A2', '序号');
                $titls['id'] = ['column' => 'A', 'title' => '序号', 'order' => $key, 'name' => 'id'];
                $key = $key + 1;
            }
            //设置第一栏的中文标题
            foreach (self::$options['titles'] as $value) {
                if (is_array($value)) {
                    if (isset($value['column'])) {
                        $Letter = strtoupper($value['column']);
                    } else if (isset($value['column'])) {
                        $key = !self::$options['is_order'] ? $key : $key + 1;
                        $Letter = self::numToExcelLetter($key);
                    } else {
                        $key = $key + 1;
                        $Letter = self::numToExcelLetter($key);
                    }
                } else {
                    $key = $key + 1;
                    $Letter = self::numToExcelLetter($key);
                }
                // dump($Letter . '2', $value);

                $objSheet->setCellValue($Letter . '2', $value['title']);
                $objSheet->getColumnDimension($Letter)->setWidth(strlen($value['title']) + 5);

                // dump($value);

                $titls[$value['name']] = ['column' => $Letter, 'func' => isset($value['func']) ? $value['func'] : '', 'title' => $value['title'], 'order' => $key, 'name' => $value['name']];
            }
        }



        if ($titls) {
            $Letter = self::numToExcelLetter(count($titls));

            //样式设置 - 合并和拆分
            $objSheet->mergeCells("A1:{$Letter}1"); //合并单元格
        }


        //写入数据
        $cell_length = [];
        $dataCountRow = count($datas);
        $start_line = self::$options['start_line'];

        if ($dataCountRow == 0) {
            exit;
        } else {
            $order = 0;
            foreach ($datas as $id => $data) {
                # code...
                $start_line = $start_line + 1;
                $order++;
                if (self::$options['is_order'] == true) {
                    $Letter = self::numToExcelLetter(1);

                    $objSheet->setCellValue($Letter . $start_line, $order);
                    $objSheet->getColumnDimension($Letter)->setWidth(5);
                }

                foreach ($data as $key => $value) {
                    if (!isset($titls[$key])) {
                        continue;
                    }
                    $title =    $titls[$key];
                    if ($key == 'id') {
                        $value = $id;
                    }

                    if (isset($title['func']) && $title['func'] instanceof Closure) {
                        $value = call_user_func_array($title['func'], [$value, $data, $title]);
                    }

                    if (isset($title['func']) &&  is_array($title['func']) && !empty($title['func'])) {
                        if (isset($title['func'][$value])) {
                            $value = $title['func'][$value];
                        }
                    }

                    if (strlen((string)$value) > 10) {
                        $objSheet->setCellValueExplicit($title['column'] . $start_line, $value, DataType::TYPE_STRING);
                    } else {
                        $objSheet->setCellValue($title['column'] . $start_line, $value);
                    }

                    if (isset($cell_length[$title['column']])) {
                        $cell_length[$title['column']] = ($cell_length[$title['column']] > strlen((string)$value)) ? $cell_length[$title['column']] : strlen((string)$value);
                    } else {
                        $cell_length[$title['column']] = 1;
                    }
                    $objSheet->getColumnDimension(self::str_del_number($title['column']))->setWidth($cell_length[self::str_del_number($title['column'])] + 5);
                }
            }
        }

        $cell = self::numToExcelLetter(count($titls));
        if ($cell) {
            $objSheet->getStyle("A1:{$cell}1")->applyFromArray(['font' => self::$options['font']]);

            $objSheet->getStyle("A1:" . $cell . $dataCountRow + self::$options['start_line'])->applyFromArray(['alignment' => self::$options['alignment']]);


            $objSheet->getStyle('A1:' . $cell . $dataCountRow + self::$options['start_line'])->applyFromArray(['borders' => self::$options['borders']]);
        }

        return $newExcel;


        // $objWriter = IOFactory::createWriter($newExcel, 'Xls');

        // //通过php保存在本地的时候需要用到
        // $file_name = PATH_RUNTIME . "/{$file_name}.xls";
        // $objWriter->save($file_name);

        // return file_exists($file_name) ? $file_name : false;
    }


    //下载
    static function downloadExcel($newExcel, $filename, $format = 'Xls')
    {
        // $format只能为 Xlsx 或 Xls
        if ($format == 'Xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif ($format == 'Xls') {
            header('Content-Type: application/vnd.ms-excel');
        }

        header("Content-Disposition: attachment;filename=" . $filename . date('Ymdhis') . '.' . strtolower($format));
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($newExcel, $format);

        $objWriter->save('php://output');

        //通过php保存在本地的时候需要用到
        //$objWriter->save($dir.'/demo.xlsx');

        //以下为需要用到IE时候设置
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        //header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        //header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        //header('Pragma: public'); // HTTP/1.0
        exit;
    }

    //下载
    static function saveExcel($newExcel, $filename = 'test', $type = 'Xlsx')
    {
        $objWriter = IOFactory::createWriter($newExcel, ucfirst($type));

        // if ($is_path == true) {
        //     $filename = PATH_RUNTIME .  $filename . date('YmdHm') . ".xls";
        //     // dump($filename);
        //     if (file_exists($filename)) {
        //         @unlink($filename);
        //     }
        // }

        $filename = $filename . '.' . strtolower($type);
        if (!is_dir(dirname($filename))) {
            @mkdir(dirname($filename));
        }


        if (file_exists($filename)) {
            @unlink($filename);
        }

        // dump('写入文件：' . $filename);
        $objWriter->save($filename);


        return file_exists($filename) ? $filename : false;
    }

    //下载
    static function saveCsv($newExcel, $filename = '数据.csv')
    {
        $objWriter = IOFactory::createWriter($newExcel, 'Csv');

        $filename = PATH_RUNTIME .  $filename . date('YmdHm') . ".csv";
        // dump($filename);
        if (file_exists($filename)) {
            @unlink($filename);
        }

        $objWriter->save($filename);

        return file_exists($filename) ? $filename : false;
    }
    //////////////////////////////////以下是工具方法//////////////////////////////////////////

    /**
     * 根据数字生成excel列名称数组
     */
    static private function numToExcelLettArray($num, $is_strtolower = false)
    {
        $array = [];
        for ($i = 1; $i <= $num; $i++) {
            if ($is_strtolower) {
                $array[] = strtolower(self::numToExcelLetter($i));
            } else {
                $array[] = self::numToExcelLetter($i);
            }
        }
        return  $array;
    }
    /**
     * 根据数字转换excel列名称
     */
    static private  function numToExcelLetter($num)
    {
        //由于大写字母只有26个，所以基数为26
        $base = 26;
        $result = '';
        $array = [];
        while ($num > 0) {
            $mod = (int)($num % $base);
            $num = (int)($num / $base);

            if ($mod == 0) {
                $num -= 1;
                $temp = self::numToLetter($base) . $result;
            } elseif ($num == 0) {
                $temp = self::numToLetter($mod) . $result;
            } else {
                $temp = self::numToLetter($mod) . $result;
            }
            $array[] = $result = $temp;
        }
        return $result;
    }
    /**
     * 获取对应值
     */
    static private  function numToLetter($num)
    {
        if ($num == 0) {
            return '';
        }

        $num = (int)$num - 1;
        //获取A的ascii码
        $ordA = ord('A');
        return chr($ordA + $num);
    }
    static private function str_del_number($str)
    {
        return preg_replace("/\\d+/", '', $str);
    }
    static  public function split($data, $num = 1)
    {
        $arrRet = array();
        if (!isset($data) || empty($data)) {
            return $arrRet;
        }
        $iCount = count($data) / $num;
        if (!is_int($iCount)) {
            $iCount = ceil($iCount);
        } else {
            $iCount = $iCount + 1;
        }
        for ($i = 0; $i < $iCount; ++$i) {
            $arrInfos = array_slice($data, $i * $num, $num);
            if (empty($arrInfos)) {
                continue;
            }
            $arrRet[] = $arrInfos;
            unset($arrInfos);
        }
        return $arrRet;
    }
}
