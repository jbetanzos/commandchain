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

/**
 *
 * This class extends the functionality of the Command class to allow a chain logic
 * main method is the executeChild which is
 *
 * Class ChainCommandAbstract
 * @package ChainCommandBundle\Command
 */
abstract class ChainCommandAbstract extends Command
{
    /**
     * @const MAIN indicates the default value for a main node
     */
    const MAIN = "main";

    /**
     * @var ChainCommandAbstract $child Child node in a chain
     */
    protected $childName;

    /**
     * @var string Command Name of the main node
     */
    protected $parentName;

    /**
     * @var ChainCommandAbstract $caller Node that called this node
     */
    protected $caller;

    private $logger;
    private $loggerLevel = 100;

    /**
     * ChainCommandAbstract constructor.
     * @param string $parentName
     * @param null $childName
     * @param LoggerInterface $logger
     */
    public function __construct(
        $parentName = ChainCommandAbstract::MAIN,
        $childName = null,
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger;
        $this->childName = $childName;
        $this->parentName = $parentName;
        parent::__construct();
    }

    /**
     * Inherited classes can call this by parent::configure() to enable some logs details
     */
    protected function configure()
    {
        if ($this->parentName == ChainCommandAbstract::MAIN) {
            $this->logger->log(
                $this->loggerLevel,
                $this->getName() .
                " is a master command of a command chain that has registered member commands"
            );
        } else {
            $this->logger->log(
                $this->loggerLevel,
                $this->getName() .
                " registered as a member of " . $this->parentName . " command chain"
            );
        }
    }

    /**
     *
     * Inherited classes can call this by parent::configure() to enable some logs details
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->parentName == ChainCommandAbstract::MAIN) {
            $this->logger->log($this->loggerLevel, "Executing " . $this->getName() . " command itself first:");
        } else {
            $this->logger->log($this->loggerLevel, "Executing " . $this->getName() . " chain members:");
        }


    }

    /**
     *
     * If the chain has a child node this command is executed placing the caller who called the child run function
     * If there are no more children then the logs shows the chain is completed
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function executeChild(InputInterface $input, OutputInterface $output)
    {
        if ($this->childName) {
            $this->childName->setCaller($this);
            $this->childName->run($input, $output);
        } else {
            $this->logger->log($this->loggerLevel, "Execution of " . $this->parentName . " chain completed.");
        }
    }

    /**
     *
     * Validates if this node can be executed by verifying that its a main node or it is a child node called from
     * another node already in the chain. If this is an invalid executing it will throw an Exception
     *
     * @return bool
     * @throws \Exception
     */
    protected function validate()
    {
        if ((!$this->getCaller() && $this->parentName == ChainCommandAbstract::MAIN) ||
            ($this->getCaller() && $this->parentName == $this->getCaller()->getName()) ) {

            return true;
        }

        throw new \Exception(
            "Error: " . $this->getName() . " command is a member of " . $this->parentName .
            " command chain and cannot be executed on its own."
        );
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
