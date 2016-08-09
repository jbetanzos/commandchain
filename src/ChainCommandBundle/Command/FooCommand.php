<?php
/**
 * Created by PhpStorm.
 * User: betanzos
 * Date: 8/8/16
 * Time: 19:08
 */

namespace ChainCommandBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FooCommand extends ChainCommandAbstract
{

    public function __construct($parentName = ChainCommandAbstract::MAIN, $childName = null, $logger)
    {
        parent::__construct($parentName, $childName, $logger);
    }

    protected function configure()
    {
        $this
            ->setName("foo:hello")
            ->setDescription('Prints a Foo Welcome Message');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $this->validate();
            parent::execute($input, $output);
            $output->writeln('Hello from Foo!');
            parent::executeChild($input, $output);

        } catch (\Exception $ex) {
            $output->writeln($ex->getMessage());
        }
    }
}
