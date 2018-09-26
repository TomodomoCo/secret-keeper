<?php

namespace Tomodomo;

use Symfony\Component\Yaml;

class SecretKeeper
{
    /**
     * The base path for the parser to check
     *
     * @var string
     */
    private $path;

    /**
     * The desired stage
     *
     * @var string
     */
    private $stage;

    /**
     * Set up the parser(s) and path to secrets
     *
     * @return void
     */
    public function __construct(string $path)
    {
        if (file_exists($path) === false) {
            throw new \Exception("Secret Keeper could not find the provided path: {$path}");
        }

        // Set stage variables
        $this->path = $path;

        // Set up parsers
        $this->setupParsers();

        return;
    }

    /**
     * Set the stage for multi-stage secrets files
     *
     * @param string $stage The desired stage
     *
     * @return void
     */
    public function setStage(string $stage)
    {
        $this->stage = $stage;

        return;
    }

    /**
     * Set up any necessary file parsers. In the future, this could
     * be extendable to allow people to define their own parsers
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
     * @param array $secrets The secrets to load
     *
     * @return void
     */
    public function load(array $secrets = [])
    {
        // Loop through the secrets
        foreach ($secrets as $config) {
            // Check for a valid file name
            if (empty($config['file'] ?? '')) {
                throw new \Exception('Secret Keeper could not get a file name for a defined secret.');
            }

            // Check for a valid type
            if (empty($config['type'] ?? '')) {
                throw new \Exception('Secret Keeper could not get a file type for a defined secret.');
            }

            // Check for a valid prefix
            if (empty($config['prefix'] ?? '')) {
                throw new \Exception('Secret Keeper could not get a prefix for a defined secret.');
            }

            // Parse the files (all yaml for now)
            $parsedSecrets = $this->parseSecretFile($config['file'], strtolower($config['type']));

            // Define the constants
            $this->defineConstants($parsedSecrets, $config['prefix']);
        }

        return;
    }

    /**
     * Parse the secret file
     *
     * @param string $file File name to parse
     * @param string $type Type of parser to use
     *
     * @return array
     */
    public function parseSecretFile(string $file, string $type)
    {
        // Fetch the secrets file
        $filePath = $this->path . $file;
        $contents = file_exists($filePath) ? file_get_contents($filePath) : false;

        // Return an empty array if necessary
        if ($contents === false) {
            throw new \Exception("Secret Keeper could not find a file: {$filePath}");
        }

        // Return an empty array if we couldn't parse the provided type
        if (in_array($type, ['yml', 'yaml', 'json']) === false) {
            throw new \Exception("Secret Keeper could not parse type `{$type}`");
        }

        // Parse YAML/YML
        if (in_array($type, ['yml', 'yaml'])) {
            $parsed = $this->yaml->parse($contents);
        }

        // Parse JSON
        if (in_array($type, ['json'])) {
            $parsed = json_decode($contents, true);
        }

        // Return the parsed file as an array
        return $parsed ?? [];
    }

    /**
     * Define the constants from the parsed secrets
     *
     * @param array  $parsedSecrets The parsed secrets
     * @param string $prefix        A prefix to apply to the constant
     *
     * @return void
     */
    private function defineConstants(array $parsedSecrets, string $prefix)
    {
        // Grab a specific stage if it exists, or get the whole thing
        $items = $parsedSecrets[$this->stage] ?? $parsedSecrets;

        // Define the constatns with a specific prefix
        foreach ($items as $name => $value) {
            define(strtoupper("{$prefix}_{$name}"), $value);
        }

        return;
    }
}
