<?php

namespace Jmleroux;

interface WriterInterface
{
    /**
     * @param Category[] $categories
     * @param            $outputPath
     */
    public function write(array $categories, $outputPath);

    /**
     * @param $category
     *
     * @return string
     */
    public function normalize(Category $category);
}
