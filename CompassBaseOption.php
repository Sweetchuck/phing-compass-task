<?php
/**
 * @file
 * ${FILE_DESCRIPTION}
 */

/**
 * Class CompassBaseRequire.
 */
class CompassBaseOption {

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value) {
        $this->name = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

}
