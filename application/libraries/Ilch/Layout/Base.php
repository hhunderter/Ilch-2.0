<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Ilch\Layout;

abstract class Base extends \Ilch\Design\Base
{
    /**
     * Defines if layout is disabled.
     *
     * @var bool
     */
    protected $disabled = false;

    /**
     * Holds the view output.
     *
     * @var string
     */
    protected $content = '';

    /**
     * File of the layout.
     *
     * @var string
     */
    protected $file = '';

    /**
     * Loads layout helper.
     *
     * @param string $name
     * @param array $args
     * @return mixed|null
     */
    public function __call(string $name, array $args)
    {
        $layout = $this->getHelper($name, 'layout');

        if (!empty($layout)) {
            return call_user_func_array([$layout, $name], $args);
        }

        return null;
    }

    /**
     * Set layout disabled flag.
     *
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled(bool $disabled): Base
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * Get layout disabled flag.
     *
     * @return bool
     */
    public function getDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Sets the view output.
     *
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): Base
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Gets the view output.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->getErrors() . $this->content;
    }

    /**
     * Loads a view script.
     *
     * @param string $loadScript
     */
    public function loadScript(string $loadScript)
    {
        if (file_exists($loadScript)) {
            include $loadScript;
        }
    }

    /**
     * Sets the file of the layout.
     *
     * @param string $file
     * @param string $layoutKey
     * @return $this
     */
    public function setFile(string $file, string $layoutKey = ''): Base
    {
        $this->setLayoutKey($layoutKey);
        $this->file = $file;
        return $this;
    }

    /**
     * Gets the file of the layout.
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}
