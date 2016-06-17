<?php
namespace Jmleroux;

class CsvWriter implements WriterInterface
{
    const OUTPUT_FILE = 'categories.csv';

    /**
     * @param Category[] $categories
     * @param string     $workDirectory
     */
    public function write(array $categories, $workDirectory)
    {
        $outputPath = realpath($workDirectory) . '/' . self::OUTPUT_FILE;

        foreach ($categories as $category) {
            $csvLine = $this->normalize($category);
            file_put_contents($outputPath, $csvLine, FILE_APPEND);
        }
    }

    /**
     * @param $category
     *
     * @return string
     */
    public function normalize(Category $category)
    {
        $pattern = '%d;%s;%s' . PHP_EOL;

        return sprintf($pattern, $category->getId(), $category->getParentId(), $category->getLabel());
    }
}
