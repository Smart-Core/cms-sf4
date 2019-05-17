<?php

namespace Smart\CoreBundle\Utils;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait OutputWritelnTrait
{
    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var int */
    protected $startTime;

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @param OutputInterface $output
     *
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param string $messages
     */
    protected function outputWriteln($messages = '')
    {
        if ($this->input instanceof InputInterface and $this->output instanceof OutputInterface) {
            $this->input->getOption('verbose') ? $this->output->writeln($messages) : null;
        }
    }

    protected function writeProfileInfo()
    {
        $this->outputWriteln("
    End at: ".date('H:i:s')."
    Memory: ".(memory_get_usage(true) / 1024 / 1024) .'MB (peak: '.(memory_get_peak_usage(true) / 1024 / 1024) ."MB)
    Time:   <comment>".sprintf('%.4f', microtime(true) - $this->startTime) . "</comment> seconds\n"
        );
    }
}
