<?php

namespace Psecio\GatekeeperCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('test');
	}
}
