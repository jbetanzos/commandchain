<?php
/**
 * Created by PhpStorm.
 * User: betanzos
 * Date: 8/8/16
 * Time: 19:20
 */

namespace ChainCommandBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BarCommand extends ChainCommandAbstract
{
    public function __construct($parentName = ChainCommandAbstract::MAIN, $childName = null, $logger)
    {
        parent::__construct($parentName, $childName, $logger);
    }

    protected function configure()
    {
        $this
            ->setName('bar:hi')
            ->setDescription('Prints a Bar Welcome Message');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->validate();
            $output->writeln('Hi from Bar!');
            parent::executeChild($input, $output);

        } catch (\Exception $ex) {
            $output->writeln($ex->getMessage());
        }
    }
}