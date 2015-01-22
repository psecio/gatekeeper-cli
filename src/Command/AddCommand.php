<?php

namespace Psecio\GatekeeperCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Psecio\Gatekeeper\Gatekeeper;

class AddCommand extends Command
{
	protected function configure()
	{
		$this->setName('add')
			->setDescription('Show data based on the given type')
			->addArgument('type', null, InputArgument::REQUIRED)
			->addOption('userid', null, InputOption::VALUE_OPTIONAL, 'User ID')
			->addOption('group', null, InputOption::VALUE_OPTIONAL, 'Group ID')
			->addOption('permission', null, InputOption::VALUE_OPTIONAL, 'Permission ID')
			->setHelp('Used to show information based on the given type');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$options = array(
			'group' => $input->getOption('group'),
			'permission' => $input->getOption('permission'),
			'userid' => $input->getOption('userid'),
		);
		$type = $input->getArgument('type');
		if (empty($type)) {
			throw new \InvalidArgumentException('Type must be specified!');
		}

		switch(strtolower($type)) {
			case 'user':
				$this->addUser($options, $output);
				break;
		}
	}

	public function addUser(array $options, $output)
	{
		$user = Gatekeeper::findUserById($options['userid']);
		$ds = Gatekeeper::getDatasource();

		if (isset($options['permission'])) {
			// If it's a permission link it to the user
			$perm = new \Psecio\Gatekeeper\UserPermissionModel($ds, array(
				'userId' => $user->id,
				'permissionId' => $options['permission']
			));
			if ($ds->save($perm) === true) {
				$output->writeln('Permission linked to user successfully');
			}
		} elseif (isset($options['group'])) {
			// If it's a group link it to the user
			$group = new \Psecio\Gatekeeper\UserGroupModel($ds, array(
				'userId' => $user->id,
				'groupId' => $options['group']
			));
			if ($ds->save($group) === true) {
				$output->writeln('Group linked to user successfully');
			}
		}
	}
}