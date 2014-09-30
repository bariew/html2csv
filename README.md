
HTML to CSV helper.
===================
Converts html to csv.
Common case is when you already have all data for csv - rendered in html table.
With this helper you can take your html content and place it into csv file.
Default raw selector is 'thead, tr', and cell selector 'th, td' - but you may redefine them.

Usage:
------
```
    // Loading data
    $htmlContent = '<table>...some data </table>';
    $csv = new Html2Csv($htmlContent);
    $csv->toFile("report.csv");
    exit(0);

    // Advanced usage:
    $csv = new Html2Csv($htmlContent, [
        'cellDelimiter' => "\t",
        'cellEnclosure' => '"',
        'rowSelector' => 'div.row',
        'cellSelector' => 'div.cell'
    ]);
    return $csv->toArray();
```


