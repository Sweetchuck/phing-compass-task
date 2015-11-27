<?php
/**
 * @file
 * ${FILE_DESCRIPTION}
 */

require_once __DIR__ . '/CompassBaseOption.php';
require_once __DIR__ . '/CompassBaseParam.php';

/**
 * Class CompassBaseTask.
 */
abstract class CompassBaseTask extends Task {

    /**
     * @var string
     */
    protected $commandPattern = '';

    /**
     * @var array
     */
    protected $commandArgs = array();

    /**
     * @var int
     */
    protected $commandExitCode = 0;

    /**
     * @var array
     */
    protected $commandOutput = array();

    /**
     * @var PhingFile
     */
    protected $dir = NULL;

    /**
     * @var bool
     */
    protected $bundleExec = NULL;

    /**
     * @var string
     */
    protected $executable = 'compass';

    /**
     * @var string
     */
    protected $command = '';

    /**
     * @var string
     */
    protected $returnProperty = '';

    /**
     * @var string
     */
    protected $outputProperty = '';

    /**
     * @var array
     */
    protected $options = array(
        'require' => array(
            'type' => 'multi',
            'value' => array(),
        ),
        'load' => array(
            'type' => 'multi',
            'value' => array(),
        ),
        'load-all' => array(
            'type' => 'multi',
            'value' => array(),
        ),
        'import-path' => array(
            'type' => 'multi',
            'value' => array(),
        ),
        'quiet' => array(
            'type' => 'flag-normal',
            'value' => NULL,
        ),
        'trace' => array(
            'type' => 'flag-normal',
            'value' => NULL,
        ),
        'force' => array(
            'type' => 'flag-normal',
            'value' => NULL,
        ),
        'boring' => array(
            'type' => 'flag-normal',
            'value' => NULL,
        ),
    );

    /**
     * @param PhingFile $value
     */
    public function setDir(PhingFile $value) {
        $this->dir = $value;
    }

    /**
     * @return PhingFile
     */
    public function getDir() {
        if ($this->dir) {
          return $this->dir;
        }

        return new PhingFile('.');
    }

    /**
     * @param bool $value
     */
    public function setBundleExec($value) {
        $this->bundleExec = ((is_null($value) || is_bool($value)) ? $value : (strtolower($value) === 'true'));
    }

    /**
     * @return bool
     */
    public function getBundleExec() {
      return $this->bundleExec;
    }

    /**
     * @param $value
     */
    public function setExecutable($value) {
        $this->executable = $value;
    }

    /**
     * @param string $value
     */
    public function setReturnProperty($value) {
        $this->returnProperty = $value;
    }

    /**
     * @param string $value
     */
    public function setOutputProperty($value) {
        $this->outputProperty = $value;
    }

    /**
     * @var boolean
     */
    protected $quiet = FALSE;

    /**
     * @param boolean $value
     */
    public function setQuiet($value) {
        $this->options['quiet']['value'] = (bool) $value;
    }

    /**
     * @param bool $value
     */
    public function setTrace($value) {
        $this->options['trace']['value'] = (bool) $value;
    }

    /**
     * @param bool $value
     */
    public function setForce($value) {
        $this->options['force']['value'] = (bool) $value;
    }

    /**
     * @param bool $value
     */
    public function setBoring($value) {
        $this->options['boring']['value'] = (bool) $value;
    }

    /**
     * @param CompassBaseOption $option
     */
    public function addOption(CompassBaseOption $option) {
        $name = $option->getName();
        if (!isset($this->options[$name])) {
            // @todo Throw an exception.
            return;
        }

        switch ($this->options[$name]['type']) {
            case 'multi':
                $this->options[$name]['value'][] = (string) $option->getValue();
                break;

            case 'singleton':
                $this->options[$name]['value'] = (string) $option->getValue();
                break;

            case 'flag-normal':
            case 'flag-invertible':
                $this->options[$name]['value'] = (bool) $option->getValue();
                break;

        }
    }

    /**
     *  {@inheritdoc}
     */
    public function main() {
        $this
            ->validate()
            ->executePrepare()
            ->execute()
            ->executePost();
    }

    /**
     * @return $this
     */
    protected function validate() {
        return $this;
    }

    /**
     * @return $this
     */
    public function executePrepare() {
        $this->commandPattern = '';
        $this->commandArgs = array();

        $dir = $this->getDir();
        if (file_exists("$dir/Gemfile") || $this->getBundleExec()) {
            chdir($dir);

            $bundle_unsafe = $this->executableGet('bundle');
            $bundle = escapeshellcmd($bundle_unsafe);

            $error_code = 0;
            $output = array();
            exec("$bundle check", $output, $error_code);
            $this->log(implode("\n", $output));

            if ($error_code == 1) {
                $error_code = 0;
                $output = array();
                exec("$bundle install", $output, $error_code);
                $this->log(implode("\n", $output));

                if ($error_code != 0) {
                    throw new BuildException('Run "bundle install" failed.');
                }
            }
            elseif ($error_code != 0) {
                throw new BuildException('Run "bundle check" failed.');
            }

            $this->commandPattern = '%s exec ';
            $this->commandArgs = array($bundle);
        }

        $executable = $this->executable ?: $this->executableGet('compass');
        $this->commandPattern .= '%s';
        $this->commandArgs[] = escapeshellcmd($executable);

        if ($this->command) {
            $this->commandPattern .= ' ' . $this->command;
        }

        foreach ($this->options as $name => $option) {
            switch ($option['type']) {
                case 'multi':
                    foreach ($option['value'] as $value) {
                        $this->commandPattern .= ' --' . $name . '=%s';
                        $this->commandArgs[] = escapeshellarg($value);
                    }
                    break;

                case 'singleton':
                    if ($option['value'] !== NULL) {
                        $this->commandPattern .= ' --' . $name . '=%s';
                        $this->commandArgs[] = escapeshellarg($option['value']);
                    }
                    break;

                case 'flag-normal':
                    if (!empty($option['value'])) {
                        $this->commandPattern .= ' --' . $name;
                    }
                    break;

                case 'flag-invertible':
                    if ($option['value'] !== NULL) {
                        if (!$option['value']) {
                            $name = "no-$name";
                        }

                        $this->commandPattern .= ' --' . $name;
                    }
                    break;

            }
        }

        return $this;
    }

    /**
     * @return $this
     *
     * @throws BuildException
     */
    protected function execute() {
        $command = vsprintf($this->commandPattern, $this->commandArgs);
        $this->log($command);
        exec($command, $this->commandOutput, $this->commandExitCode);

        if ($this->dir) {
            chdir($this->project->getBasedir());
        }

        return $this;
    }

    /**
     * @return $this
     *
     * @throws BuildException
     */
    protected function executePost()
    {
        if ($this->returnProperty) {
            $this->project->setProperty($this->returnProperty, $this->commandExitCode);
        }

        if ($this->outputProperty) {
            $this->project->setProperty($this->outputProperty, implode("\n", $this->commandOutput));
        }

        foreach ($this->commandOutput as $line) {
            $this->log($line);
        }

        switch ($this->commandExitCode) {
            case 0:
                $this->log('Successful', Project::MSG_INFO);
                break;

            default:
                throw new BuildException('Failed');

        }

        return $this;
    }

    /**
     * @param string $command
     *
     * @return string
     *
     * @throws BuildException
     */
    protected function executableGet($command) {
        $executable = $this->getProject()->getProperty("compass.$command.executable");
        if ($executable) {
            return $executable;
        }

        $output = array();
        $error_code = 0;
        exec(sprintf('which %s', escapeshellarg($command)), $output, $error_code);
        if ($error_code == 0) {
            return trim($output[0]);
        }

        throw new BuildException("Command not found: $command");
    }

}
