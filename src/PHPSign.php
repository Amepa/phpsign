<?php

/*
 * This file is part of the PHPSign.
 *
 * (c) Daniel Rodrigues (geekcom)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PHPSign;

class PHPSign
{

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $executable;

    /**
     * @var string
     */
    protected $pathExecutable;

    /**
     * @var bool
     */
    protected $windows;

    /*
     * @var array
     */
  //  protected $formats = ['pdf', 'rtf', 'xls', 'xlsx', 'docx', 'odt', 'ods', 'pptx', 'csv', 'html', 'xhtml', 'xml', 'jrprint'];

    /**
     * PHPSign constructor
     */
    public function __construct()
    {
        $this->executable = 'signstarter';
        $this->pathExecutable = __DIR__ . '/../bin/signstarter/bin';
        $this->windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? true : false;
    }

    /**
     * @return string
     */
    private function checkServer()
    {
        return $this->command = $this->windows ? $this->executable : './' . $this->executable;
    }

    /**
     * @param string $input
     * @param string $output
     * @param array $options
     * @return $this
     * @throws Exception\InvalidInputFile
     * @throws Exception\InvalidFormat
     */
    public function process(string $input, string $output, string $property)
    {
       // $options = $this->parseProcessOptions($options);

        if (!$input) {
            throw new \PHPSign\Exception\InvalidInputFile();
        }

        //$this->validateFormat($options['format']);

        $this->command = $this->checkServer();

        $this->command .= "\"$input\"";
        $this->command .= "\"$output\"";
        $this->command .= "\"$property\"";

        return $this;
    }

    /*
     * @param array $options
     * @return array
     */
  /*  protected function parseProcessOptions(array $options)
    {
        $defaultOptions = [
            'format' => ['pdf'],
            'params' => [],
            'resources' => false,
            'locale' => false,
            'db_connection' => []
        ];

        return array_merge($defaultOptions, $options);
    }*/

    /*
     * @param $format
     * @throws Exception\InvalidFormat
     */
    /*protected function validateFormat($format)
    {
        if (!is_array($format)) {
            $format = [$format];
        }
        foreach ($format as $value) {
            if (!in_array($value, $this->formats)) {
                throw new \PHPSign\Exception\InvalidFormat();
            }
        }
    }*/


    /**
     * @param bool $user
     * @return mixed
     * @throws Exception\InvalidCommandExecutable
     * @throws Exception\InvalidResourceDirectory
     * @throws Exception\ErrorCommandExecutable
     */
    public function execute($user = false)
    {
        $this->validateExecute();
        $this->addUserToCommand($user);

        $output = [];
        $returnVar = 0;

        chdir($this->pathExecutable);
        exec($this->command, $output, $returnVar);
        if ($returnVar !== 0) {
            throw new \PHPSign\Exception\ErrorCommandExecutable();
        }

        return $output;
    }

    /**
     * @return string
     */
    public function output()
    {
        return $this->command;
    }

    /**
     * @param $user
     */
    protected function addUserToCommand($user)
    {
        if ($user && !$this->windows) {
            $this->command = 'su -u ' . $user . " -c \"" . $this->command . "\"";
        }
    }

    /**
     * @throws Exception\InvalidCommandExecutable
     * @throws Exception\InvalidResourceDirectory
     */
    protected function validateExecute()
    {
        if (!$this->command) {
            throw new \PHPSign\Exception\InvalidCommandExecutable();
        }
        if (!is_dir($this->pathExecutable)) {
            throw new \PHPSign\Exception\InvalidResourceDirectory();
        }
    }
}
