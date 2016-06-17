<?php
namespace Jmleroux;

class Loader
{
    const TMP_FILE = 'GS-categories.txt';

    /** @var string */
    private $workDirectory;

    /**
     * @param string $workDirectory
     */
    public function __construct($workDirectory)
    {
        $this->workDirectory = $workDirectory;
    }

    /**
     * @param $locale
     *
     * @return string
     */
    public function load($locale)
    {
        $output = realpath($this->workDirectory) . '/' . self::TMP_FILE;
        $input = sprintf('http://www.google.com/basepages/producttype/taxonomy-with-ids.%s.txt', $locale);
        exec(sprintf('wget %s -O %s', $input, $output));
        
        return $output;
    }
}
