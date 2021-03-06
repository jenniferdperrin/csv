csv
===

This library is designed to help you read and write csv files.

Usage
---

### Reading CSV files

Instanciating the reader is really easy :

```php

use Csv\Reader;

$reader = new Reader($file, array(
    'hasHeader' => true,
    'inputEncoding' => 'ISO-8859-15',
    'outputEncoding' => 'UTF-8'
));

foreach ($reader as $line) {
    /*
     * $line is an array.
     * If the CSV file has an header,
     * the array keys are the header fields
     */
}
```

In this example, `$file` can be the path to an existing file, or a pointer to an already open file.
Available options are :

- `'hasHeader'` : A boolean determining if the first line of the CSV file is a head with fields names
- `'header'` : If the CSV file doesn't have a header, you can provide one here.
  If both `'header'` and `'hasHeader'` are provided, the `'header'` option takes precedence
- `'inputEncoding'` : The encoding of the CSV file. Defaults to UTF-8
- `'outputEncoding'` : The encoding of the data that will be returned when reading the file.
  If `'inputEncoding'` and `'outputEncoding'` are different, the reader automatically uses mbstring to convert
- `'delimiter'` : The CSV delimiter
- `'enclosure'` : The CSV enclosure
