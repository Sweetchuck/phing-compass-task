<?php
/**
 * @file
 * ${FILE_DESCRIPTION}
 */

require_once __DIR__ . '/CompassCommonTask.php';

/**
 * Class CompassCompileTask.
 */
class CompassCompileTask extends CompassCommonTask
{
    /**
     * {@inheritdoc}
     */
    protected $command = 'compile';

    /**
     * {@inheritdoc}
     */
    public function __construct() {
        parent::__construct();

        $this->options += array(
            'sourcemap' => array(
                'type' => 'flag-invertible',
                'value' => NULL,
            ),
            'debug-info' => array(
                'type' => 'flag-invertible',
                'value' => NULL,
            ),
        );
    }

    /**
     * @param bool $value
     */
    public function setSourceMap($value)
    {
        $this->options['sourcemap']['value'] = $value;
    }

    /**
     * @param bool $value
     */
    public function setDebugInfo($value)
    {
        $this->options['debug-info']['value'] = $value;
    }

}
