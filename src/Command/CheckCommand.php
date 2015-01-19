<?php

namespace Psecio\GatekeeperCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Psecio\Gatekeeper\Gatekeeper;

class CheckCommand extends Command
{
	protected function configure()
	{
		$this->setName('check')
			->setDescription('Show data based on the given type')
			->addOption('userid', null, InputOption::VALUE_OPTIONAL, 'User ID')
			->addOption('ingroup', null, InputOption::VALUE_OPTIONAL, 'Check if the user is in this group')
			->setHelp('Used to run checks against the current data');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$options = array(
			'userid' => $input->getOption('userid'),
			'ingroup' => $input->getOption('ingroup')
		);

		if (!empty($options['ingroup'])) {
			$result = $this->inGroup($options['userid'], $options['ingroup'], $output);
		}
		$output->writeln("");
	}

	/**
	 * Check to see if a user is in a group
	 *
	 * @param integer $userId User ID
	 * @param integer $groupId Group ID
	 * @param OutputInterface $output Output object
	 */
	public function inGroup($userId, $groupId, $output)
	{
		$group = Gatekeeper::findGroupById($groupId);
		$result = Gatekeeper::findUserById($userId)->inGroup($groupId);
		$output->writeln(
			($result === true)
				? "User in group '".$group->description."'."
				: "User <options=bold>not</options=bold> in group '".$group->description."'."
		);
		return $result;
	}
}