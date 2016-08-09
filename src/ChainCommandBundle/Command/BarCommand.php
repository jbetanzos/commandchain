<?php
/**
 * Created by PhpStorm.
 * User: betanzos
 * Date: 8/8/16
 * Time: 19:20
 */

namespace ChainCommandBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BarCommand extends ChainCommandAbstract
{
    public function __construct($parentName = ChainCommandAbstract::MAIN, $childName = null, $logger = null)
    {
        parent::__construct($parentName, $childName, $logger);
    }

    protected function configure()
    {
        $this
            ->setName('bar:hi')
            ->setDescription('Prints a Bar Welcome Message');
    }

    /**
     *
     * Executes the logic inside the command in order to make it part of the chain it validates the executing
     * executes its logic and finally execute the child nodes.
     *
     * Make sure the validate function called is enclosed by a try & catch since if the validate does not pass
     * it will get an Exception breaking the command execution.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
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
