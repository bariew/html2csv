<?php
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 9/30/14
 * Time: 9:55 AM
 */

namespace bariew\html2csv;


class Html2Csv
{
    /**
     * @var \phpQueryObject
     */
    public $dom;
    public $data = [];
    public $result = '';

    public $cellDelimiter = ';';
    public $cellEnclosure = '"';
    public $rowSelector = 'thead, tr';
    public $cellSelector = 'th, td';


    public function __construct($content, $params = [])
    {
        $this->dom = \phpQuery::newDocumentHTML($content);
        foreach ($params as $attribute => $value) {
            $this->$attribute = $value;
        }
    }

    public function toArray()
    {
        foreach($this->dom->find($this->rowSelector) as $key => $row) {
            foreach (pq($row)->find($this->cellSelector) as $cell) {
                $this->addEl(pq($cell), $key);
            }
        }
        return $this;
    }

    public function toFile($fileName)
    {
        if (!$this->data) {
            $this->toArray();
        }
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Pragma: no-cache");
        header("Expires: 0");
        $output = fopen("php://output", "w");
        foreach ($this->data as $row) {
            fputcsv($output, $row, $this->cellDelimiter, $this->cellEnclosure);
        }
        fclose($output);
    }

    protected function addEl($el, $key)
    {
        /**
         * @var \phpQueryObject $el
         */
        $rowspan = ($el->attr('rowspan') > 1) ? $el->attr('rowspan') : 1;
        $colspan = ($el->attr('colspan') > 1) ? $el->attr('colspan') : 1;
        for ($i = 0; $i < $rowspan; $i++) {
            $colKey = isset($this->data[$key+$i]) ? count($this->data[$key+$i]) : 0;
            for ($j = 0; $j < $colspan; ++$j) {
                $this->data[$key+$i][$colKey + $j] = (($j == floor($colspan/2)) && ($i == 0)) ? trim($el->text()) : '';
            }
        }
    }
} 