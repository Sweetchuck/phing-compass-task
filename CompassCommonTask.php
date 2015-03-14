<?php

/**
 * @file
 * Documentation missing.
 */

require_once __DIR__ . '/CompassBaseTask.php';

/**
 * Class CompassCompileTask.
 */
class CompassCommonTask extends CompassBaseTask {

    /**
     * @var CompassBaseParam[]
     */
    protected $parameters = array();

    /**
     * @var FileSet[]
     */
    protected $fileSets = array();

    /**
     * Create a CompassCompileTask instance.
     */
    public function __construct()
    {
        $this->options += array(
            'config' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'app' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'app-dir' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'sass-dir' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'css-dir' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'images-dir' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'javascripts-dir' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'fonts-dir' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'environment' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'output-style' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'relative-assets' => array(
                'type' => 'flag',
                'value' => NULL,
            ),
            'no-line-comments' => array(
                'type' => 'flag',
                'value' => NULL,
            ),
            'http-path' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
            'generated-images-path' => array(
                'type' => 'singleton',
                'value' => NULL,
            ),
        );
    }

    /**
     * @param string $value
     */
    public function setConfig($value)
    {
        $this->options['config']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setApp($value)
    {
        $this->options['app']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setAppDir($value)
    {
        $this->options['app-dir']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setSassDir($value)
    {
        $this->options['sass-dir']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setCssDir($value)
    {
        $this->options['css-dir']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setImagesDir($value)
    {
        $this->options['images-dir']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setJavaScriptsDir($value)
    {
        $this->options['javascripts-dir']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setFontsDir($value)
    {
        $this->options['fonts-dir']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setEnvironment($value)
    {
        $this->options['environment']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setOutputStyle($value)
    {
        $this->options['output-style']['value'] = $value;
    }

    /**
     * @param bool $value
     */
    public function setRelativeAssets($value)
    {
        $this->options['relative-assets']['value'] = $value;
    }

    /**
     * @param bool $value
     */
    public function setNoLineComments($value)
    {
        $this->options['no-line-comments']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setHttpPath($value)
    {
        $this->options['http-path']['value'] = $value;
    }

    /**
     * @param string $value
     */
    public function setGeneratedImagesPath($value)
    {
        $this->options['generated-images-path']['value'] = $value;
    }

    /**
     * @param CompassBaseParam $param
     */
    public function addParam(CompassBaseParam $param)
    {
        $this->parameters[$param->getText()] = $param;
    }

    /**
     * Nested adder, adds a set of files (nested fileset attribute).
     *
     * @param FileSet $file_set
     */
    public function addFileSet(FileSet $file_set)
    {
        $this->fileSets[] = $file_set;
    }

    /**
     * {@inheritdoc}
     */
    public function executePrepare()
    {
        parent::executePrepare();

        $this->commandPattern .= str_repeat(" \\\n  %s", count($this->parameters));
        foreach ($this->parameters as $parameter) {
            $this->commandArgs[] = escapeshellarg($parameter->getText());
        }

        $project = $this->getProject();
        foreach ($this->fileSets as $fileset) {
            $dir_scanner = $fileset->getDirectoryScanner($project);
            /** @var array $files */
            $files = $dir_scanner->getIncludedFiles();
            $dir = $fileset->getDir($project)->getPath();

            $this->commandPattern .= str_repeat(" \\\n  %s", count($files));
            foreach ($files as $file) {
                $this->commandArgs[] = escapeshellarg($dir . DIRECTORY_SEPARATOR . $file);
            }
        }

        return $this;
    }

}
