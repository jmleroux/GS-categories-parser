<?php

namespace Jmleroux\Command;

use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Common\Type;
use Box\Spout\Reader\CSV\Reader;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Reader\ReaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 */
class GsParseCommand extends Command
{
    const TMP_FILE = 'GS-categories.txt';
    const OUTPUT_FILE = 'categories.csv';

    private $categories = [];
    
    /**
     * @var string
     */
    private $workdir;

    public function __construct($workdir)
    {
        parent::__construct();
        $this->workdir = $workdir;
    }

    protected function configure()
    {
        $this
            ->setName('jmleroux:google-shopping:parse-categories')
            ->setDescription('Parse Google Shopping categories')
            ->addArgument(
                'workdir',
                InputArgument::OPTIONAL,
                'work directory'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $returnCode = 0;

        $workdir = $input->getArgument('workdir') ? : $this->workdir;
        $filepath = realpath($workdir) . '/' . self::TMP_FILE;
        
        exec(sprintf('wget http://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt -O %s', $filepath));

        $this->readXls($filepath);

        $message = sprintf('<info>Done.</info> Parsed %d categories', count($this->categories));
        $output->writeln($message);

        return $returnCode;
    }

    private function readXls($filepath)
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
    }

    private function parseRow(array $data)
    {
        list($id, $root) = explode(' - ', $data[0]);

        if (null === $root) {
            return;
        }

        if (count($data) === 1) {
            $label = trim($root);
            $parentId = null;
        } elseif (count($data) === 2) {
            $label = trim(array_pop($data));
            $parentId = $this->findParentId(trim($root));
        } else {
            $label = trim(array_pop($data));
            $parentId = $this->findParentId(trim(array_pop($data)));
        }

        $this->categories[$id]['parent'] = $parentId;
        $this->categories[$id]['label'] = $label;

    }

    private function findParentId($parentLabel)
    {
        foreach ($this->categories as $id => $category) {
            if ($parentLabel == $category['label']) {
                return $id;
            }
        }
        throw new \RuntimeException('Parent not found for' . $parentLabel);
    }

    /**
     * @return ReaderInterface|Reader
     * @throws UnsupportedTypeException
     */
    private function getReader()
    {
        return ReaderFactory::create(Type::CSV);
    }
}
