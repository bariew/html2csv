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
    public $rowSelector = 'tr';
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
        foreach($this->dom->find($this->rowSelector) as $rowKey => $row) {
            foreach (pq($row)->find($this->cellSelector) as $colKey => $cell) {
                $this->addEl(pq($cell), $rowKey, $colKey);
            }
        }
        foreach ($this->data as $key => $values) {
            ksort($values);
            $this->data[$key] = $values;
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
        echo "\xEF\xBB\xBF";
        $output = fopen("php://output", "w");
        foreach ($this->data as $row) {
            fputcsv($output, $row, $this->cellDelimiter, $this->cellEnclosure);
        }
        fclose($output);
    }

    protected function addEl($el, $rowKey, $colKey)
    {
        /**
         * @var \phpQueryObject $el
         */
        $rowspan = ($el->attr('rowspan') > 1) ? $el->attr('rowspan') : 1;
        $colspan = ($el->attr('colspan') > 1) ? $el->attr('colspan') : 1;
        for ($row = 0; $row < $rowspan; $row++) {
            while (isset($this->data[$rowKey+$row][$colKey])) {
                $colKey++;
            }
            for ($col = 0; $col < $colspan; $col++) {
                $this->data[$rowKey+$row][$colKey + $col] = (($col == 0) && ($row == 0))
                    ? $this->prepareText($el->text()) : '';
            }
        }
    }

    protected function prepareText($string)
    {
        return trim($string);
    }
} 