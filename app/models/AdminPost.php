<?php

namespace app\models;

/**
 * Class AdminPost
 * @package app\models
 */
class AdminPost extends Post
{
    /**
     * @param bool $runValidation
     * @return false
     */
    public function toggle($runValidation = true)
    {
        $this->is_hidden = $this->is_hidden ? 0 : 1;
        return parent::save($runValidation);
    }

    /**
     * {@inheritDoc}
     */
    public function save($runValidation = true)
    {
        $this->is_updated = 1;
        return parent::save($runValidation);
    }
}
