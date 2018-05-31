<?php

namespace Tomodomo;

use Symfony\Component\Yaml;

class SecretKeeper
{

	/**
	 * Set up the parser(s) and path to secrets
	 *
	 * @return void
	 */
	public function __construct($path, $stage = '')
	{
		if (file_exists($path) === false) {
			throw new Exception('This path does not exist.');
		}

		// Set stage variables
		$this->path  = $path;
		$this->stage = $stage;

		// Set up parsers
		$this->setupParsers();

		return;
	}

	/**
	 * Set up any necessary file parsers
	 *
	 * @return void
	 */
	private function setupParsers()
	{
		$this->yaml = new Yaml\Parser();

		return;
	}

	/**
	 * Parse the YAML and define constants
	 *
	 * @param array $secrets
	 * @return void
	 */
	public function load($secrets = [])
	{
		// Loop through the secrets
		foreach ($secrets as $filename) {

			// Parse the files (all yaml for now)
			$parsed_secrets = $this->parseSecretFile($filename, 'yml');

			// Define the constants
			$this->defineConstants($filename, $parsed_secrets);
		}

		return;
	}

	/**
	 * Parse the secret file
	 *
	 * @param string $service
	 * @param string $extension
	 * @return array
	 */
	private function parseSecretFile($filename = '', $extension = '')
	{
		$file = $this->path . "{$filename}.{$extension}";

		if (file_exists($file) === false) {
			return false;
		}

		// Parse/decode different file types (JSON, YML, etc)
		if ($extension === 'yml' || $extension === 'yaml') {
			$parsed = $this->yaml->parse(file_get_contents($file));
		}

		return $parsed;
	}

	/**
	 * Define the constants from the parsed secrets
	 *
	 * @param string $filename
	 * @param array $service
	 * @return void
	 */
	private function defineConstants($filename = '', $parsed_secrets = [])
	{
		$items = $parsed_secrets[$this->stage] ?? $parsed_secrets;

		foreach ($items as $name => $value) {
			define(strtoupper("{$filename}_{$name}"), $value);
		}

		return;
	}
}
