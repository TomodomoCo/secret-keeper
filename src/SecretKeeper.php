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
			throw new \Exception('This path does not exist.');
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
		foreach ($secrets as $config) {

			// Work from a config array
			$filename  = isset($config['filename']) ? $config['filename'] : '';
			$extension = isset($config['extension']) ? $config['extension'] : '';
			$prefix    = isset($config['prefix']) ? $config['prefix'] : $filename;

			// Parse the files (all yaml for now)
			$parsedSecrets = $this->parseSecretFile($filename, $extension);

			// Define the constants
			$this->defineConstants($prefix, $parsedSecrets);
		}

		return;
	}

	/**
	 * Parse the secret file
	 *
	 * @param string $filename
	 * @param string $extension
	 * @return array
	 */
	public function parseSecretFile($filename, $extension)
	{
		// Fetch the secrets file
		$file = $this->path . "{$filename}.{$extension}";
		$file = file_exists($file) ? file_get_contents($file) : false;

		// Return an empty array if necessary
		if ($file === false) {
			return [];
		}

		// Parse YAML
		if ($extension === 'yml' || $extension === 'yaml') {
			$parsed = $this->yaml->parse($file);
		}

		// Parse JSON
		if ($extension === 'json') {
			$parsed = json_decode($file, true);
		}

		// Return the parsed file as an array
		return $parsed;
	}

	/**
	 * Define the constants from the parsed secrets
	 *
	 * @param string $prefix
	 * @param array $parsedSecrets
	 * @return void
	 */
	private function defineConstants($prefix, $parsedSecrets = [])
	{
		// Grab a specific stage if it exists, or get the whole thing
		$items = isset($parsedSecrets[$this->stage]) ? $parsedSecrets[$this->stage] : $parsedSecrets;

		// Define the constatns with a specific prefix
		foreach ($items as $name => $value) {
			define(strtoupper("{$prefix}_{$name}"), $value);
		}

		return;
	}
}
