<?php
namespace Jmleroux;

use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Common\Type;
use Box\Spout\Reader\CSV\Reader as CsvReader;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Reader\ReaderInterface;

class Reader
{
    /** @var Category[] */
    private $categories = [];

    /**
     * @param string $filepath
     *
     * @return Category[]
     */
    public function read($filepath)
    {
        $reader = $this->getReader();
        $reader->setFieldDelimiter('>');
        $reader->setEndOfLineCharacter("\r");

        $reader->open($filepath);

        $sheet = $reader->getSheetIterator()->current();
        foreach ($sheet->getRowIterator() as $row) {
            $this->parseRow($row);
        }
        $reader->close();

        return $this->categories;

    }

    /**
     * @param string[] $data
     */
    private function parseRow(array $data)
    {
        list($id, $rootLabel) = explode(' - ', $data[0]);

        if (null === $rootLabel) {
            return;
        }

        $category = new Category();
        $category->setId((int)$id);

        if (count($data) === 1) {
            $label = trim($rootLabel);
            $parentId = null;
        } elseif (count($data) === 2) {
            $label = trim(array_pop($data));
            $parentId = $this->findParentId(trim($rootLabel));
        } else {
            $label = trim(array_pop($data));
            $parentId = $this->findParentId(trim(array_pop($data)));
        }

        $category->setParentId($parentId);
        $category->setLabel($label);

        $this->categories[$category->getId()] = $category;
    }

    /**
     * @param string $parentLabel
     *
     * @return int
     * @throws \RuntimeException
     */
    private function findParentId($parentLabel)
    {
        foreach ($this->categories as $id => $category) {
            if ($parentLabel == $category->getLabel()) {
                return (int)$id;
            }
        }
        throw new \RuntimeException('Parent not found for' . $parentLabel);
    }

    /**
     * @return ReaderInterface|CsvReader
     * @throws UnsupportedTypeException
     */
    protected function getReader()
    {
        return ReaderFactory::create(Type::CSV);
    }
}
