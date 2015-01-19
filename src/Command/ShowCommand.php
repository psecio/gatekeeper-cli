<?php

namespace Psecio\GatekeeperCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Psecio\Gatekeeper\Gatekeeper;

class ShowCommand extends Command
{
	protected function configure()
	{
		$this->setName('show')
			->setDescription('Show data based on the given type')
			->addArgument('type', null, InputArgument::REQUIRED, 'Type to show', [])
			->setHelp('Used to show information based on the given type');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$params = array();
		$type = $input->getArgument('type');

		$method = 'show'.$type;
		if (!method_exists($this, $method)) {
			throw new \InvalidArgumentException('Invalid show type: '.$type);
		}

		$this->$method($params, $output);
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
	 * @param array  $params [description]
	 * @param OutputInterface $output Output interface object
	 */
	public function showUsers(array $params = array(), $output)
	{
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
	 * Show the listing of groups
	 *
	 * @param array  $params [description]
	 * @param OutputInterface $output Output interface object
	 */
	public function showGroups(array $params = array(), $output)
	{
		$columns = array(
			'name' => 'Name',
			'description' => 'Description',
			'created' => 'Date Created',
			'updated' => 'Date Updated',
			'id' => 'ID');

		$groups = Gatekeeper::findGroups($params);
		$this->buildTable($columns, $groups->toArray(true), $output);
	}
}
