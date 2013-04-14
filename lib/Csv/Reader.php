<?php
namespace Csv;

use Csv\Reader\Error;

/**
 * A CSV file reader
 * 
 * @author Maxime Mérian
 *
 */
class Reader implements \Iterator
{
    /**
     * Encoding of the file that will be read
     *
     * @var string
     */
    protected $inputEncoding = 'UTF-8';

    /**
     * Encoding of the data that will be returned by the reader
     *
     * @var string
     */
    protected $outputEncoding = 'UTF-8';

    /**
     * CSV delimiter
     *
     * @var string
     */
    protected $delimiter = ',';

    /**
     * CSV enclosure
     *
     * @var string
     */
    protected $enclosure = '"';

    /**
     * Does the CSV file hae a header with column names ?
     *
     * @var string
     */
    protected $hasHeader = false;

    /**
     * CSV file header
     *
     * @var array
     */
    protected $header = null;

    /**
     * Current line number in the CSV file that is being processed
     *
     * @var int
     */
    protected $curLine = 0;

    /**
     * Data the is currently being read
     *
     * @var array
     */
    protected $currentData = null;

    /**
     * Path to the open file, if the reader was instanciated with a file path
     *
     * @var string
     */
    protected $filePath = null;

    /**
     * Pointer to the file that is being read
     *
     * @var resource
     */
    protected $fp = null;

    /**
     * List of valid options
     *
     * @var array
     */
    protected $validOptions = array(
        'hasHeader',
        'inputEncoding',
        'outputEncoding',
        'delimiter',
        'enclosure'
    );

    /**
     * Constructor
     *
     * @param string|resource $file The file to read. Can be provided as the path to the file or as a resource
     * @param array $options
     *
     * @throws Csv\Error
     */
    public function __construct($file, array $options = array())
    {
        if (is_resource($file)) {
            $this->fp = $file;
        } elseif (is_string($file)) {
            if (! file_exists($file)) {
                throw new Error($file . ' does not exist');
            } elseif (! is_readable($file)) {
                throw new Error($file . ' is not readable');
            }
            $this->file = $file;
        } else {
            throw new Error('File must be a valid path or resource');
        }

        $this->setOptions($options);

        $this->init();
    }

    protected function init()
    {
        $this->openFile();
        $this->readHeader();
    }

    /**
     * Destructor.
     * 
     * Closes the open CSV file if necessary
     */
    public function __destruct()
    {
        /*
         * Only close the resource if we opened it
         */
        if ($this->file && $this->fp) {
            fclose($this->fp);
        }
    }

    /**
     * Sets the reader options.
     *
     * @param array $options
     *
     * @return Csv\Reader
     */
    public function setOptions(array $options)
    {
        foreach ($options as $opt => $val) {
            $this->setOption($opt, $val);
        }

        return $this;
    }

    /**
     * Sets an option
     *
     * @param string $name
     * @param mixed $value
     *
     * @return Csv\Reader
     *
     * @throws Csv\Error
     */
    public function setOption($name, $value)
    {
        if (! in_array($name, $this->validOptions)) {
            throw new Error('Invalid option ' . $name . '. Valid options are : ' . join(', ', $this->validOptions));
        }
        $this->$name = $value;

        return $this;
    }

    /**
     * Opens the CSV file for read
     *
     * @return \Csv\Reader
     */
    protected function openFile()
    {
        if (is_null($this->fp)) {
            $this->fp = fopen($this->file, 'r');
        }

        return $this;
    }

    /**
     * Read the next line.
     *
     * If no new line can be read, this
     * method returns false
     *
     * @return array
     *
     * @throws Csv\Error if no line can be read
     */
    protected function readLine()
    {
        if (! $this->valid()) {
            throw new Error('End of stream reached, no data to read');
        }
        $this->currentData = fgetcsv($this->fp, null, $this->delimiter, $this->enclosure);
        if (! $this->valid()) {
            return false;
        }

        $this->curLine++;

        return $this->currentData;
    }

    /**
     * Reads the CSV header
     *
     * @return \Csv\Reader
     */
    protected function readHeader()
    {
        if ($this->hasHeader && is_null($this->header)) {
            $this->rewind();
            $this->header = $this->readLine();
        }
    
        return $this;
    }

    /**
     * Returns an HTML table preview of the csv data
     *
     * @return string
     */
    public function getHtmlPreview()
    {
        
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current()
    {
        if ($this->header) {
            return array_combine($this->header, $this->currentData);
        }
        return $this->currentData;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        rewind($this->fp);
        $this->curLine = 0;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid()
    {
        return (! feof($this->fp));
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next()
    {
        $this->readLine();
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->curLine;
    }
}