<?php

namespace Jmleroux;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 */
class GsParseCommand extends Command
{
    /** @var string */
    private $workdir;

    /**
     * @param string $workdir
     */
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

        $loader = new Loader($workdir);
        $filepath = $loader->load('en-US');
        
        $reader = new Reader();
        $categories = $reader->read($filepath);

        $message = sprintf('<info>Read done.</info> Parsed %d categories', count($categories));
        $output->writeln($message);

        $writer = new CsvWriter();
        $writer->write($categories, $workdir);

        return $returnCode;
    }
}
