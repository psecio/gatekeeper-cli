<?php

namespace Psecio\GatekeeperCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Psecio\Gatekeeper\Gatekeeper;

class ShowCommand extends Command
{
	protected function configure()
	{
		$this->setName('show')
			->setDescription('Show data based on the given type')
			->addArgument('type', null, InputArgument::REQUIRED, 'Type to show', [])
			->addOption('permissions', null, InputOption::VALUE_NONE, 'Show permissions results')
			->addOption('groups', null, InputOption::VALUE_NONE, 'Show group results')
			->addOption('id', null, InputOption::VALUE_OPTIONAL, 'ID to search for')
			->setHelp('Used to show information based on the given type');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$params = array();
		$options = array(
			'permissions' => $input->getOption('permissions'),
			'groups' => $input->getOption('groups'),
			'id' => $input->getOption('id')
		);
		$type = $input->getArgument('type');

		$method = 'show'.$type;
		if (!method_exists($this, $method)) {
			throw new \InvalidArgumentException('Invalid show type: '.$type);
		}

		$this->$method($options, $output);
	}

	private function buildTable(array $columns, array $data, $output)
	{
		$results = array();
		$keys = array_keys($columns);
		foreach ($data as $record) {
			$tmp = array();
			foreach ($keys as $key) {
				$tmp[] = $record[$key];
			}
			$results[] = $tmp;
		}

		$table = $this->getHelper('table');
		$table->setHeaders(array_values($columns))
			->setRows($results);
		$table->render($output);
	}

	/**
	 * Show the listing of users
	 *
	 * @param array $options Command line options
	 * @param OutputInterface $output Output interface object
	 */
	public function showUsers(array $options = array(), $output)
	{
		if (!empty($options['permissions'])) {
			$this->showUserPermissions($options, $output);
		} elseif (!empty($options['groups'])) {
			$this->showUserGroups($options, $output);
		} else {
			$this->showUserGeneral($options, $output);
		}
	}

	/**
	 * Output the general information about the user
	 *
	 * @param array $options Command line options
	 * @param OutputInterface $output Output interface object
	 */
	public function showUserGeneral(array $options = array(), $output)
	{
		$params = array();
		if (!empty($options['id'])) {
			$params['id'] = $options['id'];
		}

		$columns = array(
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'firstName' => 'First Name',
			'lastName' => 'Last Name',
			'created' => 'Date Created',
			'updated' => 'Date Updated',
			'status' => 'Status',
			'id' => 'ID'
		);

		$users = Gatekeeper::findUsers($params);
		$this->buildTable($columns, $users->toArray(true), $output);
	}

	/**
	 * Show the permissions for a user
	 *
	 * @param array $options Command line options
	 * @param OutputInterface $output Output interface object
	 */
	public function showUserPermissions(array $options = array(), $output)
	{
		if (empty($options['id'])) {
			throw new \InvalidArgumentException('You must specify a user ID!');
		}

		$user = Gatekeeper::findUserById($options['id']);
		$output->writeln("\n".'Showing permissions for <options=bold>'.$user->username.'</options=bold>');

		$params = array('userId' => $options['id']);
		$columns = array(
			'name' => 'Name',
			'description' => 'Description',
			'created' => 'Date Created',
			'updated' => 'Date Updated',
			'id' => 'ID'
		);
		$data = array();
		$ds = Gatekeeper::getDatasource();

		$permissions = Gatekeeper::findUserPermissions($params);
		foreach ($permissions->toArray(true) as $permission) {
			$perm = new \Psecio\Gatekeeper\PermissionModel($ds);
			$perm = $ds->find($perm, array('id' => $permission['permissionId']));
			$data[] = $perm->toArray();
		}
		$this->buildTable($columns, $data, $output);
	}

	/**
	 * Show the permissions for a user
	 *
	 * @param array $options Command line options
	 * @param OutputInterface $output Output interface object
	 */
	public function showUserGroups(array $options = array(), $output)
	{
		if (empty($options['id'])) {
			throw new \InvalidArgumentException('You must specify a user ID!');
		}

		$user = Gatekeeper::findUserById($options['id']);
		$output->writeln("\n".'Showing groups for <options=bold>'.$user->username.'</options=bold>');

		$params = array('userId' => $options['id']);
		$columns = array(
			'name' => 'Name',
			'description' => 'Description',
			'created' => 'Date Created',
			'updated' => 'Date Updated',
			'id' => 'ID'
		);
		$data = array();
		$ds = Gatekeeper::getDatasource();

		$groups = Gatekeeper::findUserGroups($params);
		foreach ($groups->toArray(true) as $group) {
			$groupModel = new \Psecio\Gatekeeper\GroupModel($ds);
			$groupModel = $ds->find($groupModel, array('id' => $group['groupId']));
			$data[] = $groupModel->toArray();
		}
		$this->buildTable($columns, $data, $output);
	}

	/**
	 * Show the listing of groups
	 *
	 * @param array $options Command line options
	 * @param OutputInterface $output Output interface object
	 */
	public function showGroups(array $options = array(), $output)
	{
		$params = array();
		if (!empty($options['id'])) {
			$params['id'] = $options['id'];
		}

		$columns = array(
			'name' => 'Name',
			'description' => 'Description',
			'created' => 'Date Created',
			'updated' => 'Date Updated',
			'id' => 'ID');

		$groups = Gatekeeper::findGroups($params);
		$this->buildTable($columns, $groups->toArray(true), $output);
	}

	/**
	 * Show the listing of permission
	 *
	 * @param array $options Command line options
	 * @param OutputInterface $output Output interface object
	 */
	public function showPermissions(array $options = array(), $output)
	{
		$params = array();
		if (!empty($options['id'])) {
			$params['id'] = $options['id'];
		}

		$columns = array(
			'name' => 'Name',
			'description' => 'Description',
			'created' => 'Date Created',
			'updated' => 'Date Updated',
			'id' => 'ID');

		$groups = Gatekeeper::findPermissions($params);
		$this->buildTable($columns, $groups->toArray(true), $output);
	}
}
