<?php

namespace core;

interface IdentityInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /*
     * @return bool
     */
    public function getIsAdmin();

    /**
     * @param mixed $id
     * @return IdentityInterface
     */
    public static function findById($id);
}
