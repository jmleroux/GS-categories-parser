<?php
namespace Jmleroux;

class CsvWriter implements WriterInterface
{
    /**
     * @param Category[] $categories
     * @param            $outputPath
     */
    public function write(array $categories, $outputPath)
    {
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
