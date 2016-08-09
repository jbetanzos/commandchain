<?php
/**
 * Created by PhpStorm.
 * User: betanzos
 * Date: 8/9/16
 * Time: 01:25
 */

namespace ChainCommandBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ChainCommandAbstract extends Command
{
    const MAIN = "main";

    protected $childName;
    protected $parentName;
    protected $caller;

    private $logger;
    private $loggerLevel = 100;

    public function __construct($parentName = ChainCommandAbstract::MAIN, $childName = null, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->childName = $childName;
        $this->parentName = $parentName;
        parent::__construct();
    }

    protected function configure()
    {
        if ($this->parentName == ChainCommandAbstract::MAIN) {
            $this->logger->log($this->loggerLevel, $this->getName() . " is a master command of a command chain that has registered member commands");
        } else {
            $this->logger->log($this->loggerLevel, $this->getName() . " registered as a member of " . $this->parentName . " command chain");
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->parentName == ChainCommandAbstract::MAIN) {
            $this->logger->log($this->loggerLevel, "Executing " . $this->getName() . " command itself first:");
        } else {
            $this->logger->log($this->loggerLevel, "Executing " . $this->getName() . " chain members:");
        }


    }

    protected function executeChild(InputInterface $input, OutputInterface $output)
    {
        if ($this->childName) {
            $this->childName->setCaller($this);
            $this->childName->run($input, $output);
        } else {
            $this->logger->log($this->loggerLevel, "Execution of " . $this->parentName . " chain completed.");
        }
    }

    protected function validate()
    {
        if ((!$this->getCaller() && $this->parentName == ChainCommandAbstract::MAIN) ||
            ($this->getCaller() && $this->parentName == $this->getCaller()->getName()) ) {

            return true;
        }

        throw new \Exception("Error: " . $this->getName() . " command is a member of " . $this->parentName .
            " command chain and cannot be executed on its own.");
    }


    /**
     * @return string
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * @param string $caller
     */
    public function setCaller($caller)
    {
        $this->caller = $caller;
    }


}