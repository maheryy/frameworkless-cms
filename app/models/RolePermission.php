<?php

namespace App\Models;

use App\Core\Model;

class RolePermission extends Model
{
    private $id;
    protected $role_id;
    protected $permission_id;

    public function __construct()
    {
        parent::__construct();
    }

    public function setId($id)
    {
        $this->id = $id;
        $this->hydrate();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * @param mixed $role_id
     */
    public function setRoleId($role_id): void
    {
        $this->role_id = $role_id;
    }

    /**
     * @return mixed
     */
    public function getPermissionId()
    {
        return $this->permission_id;
    }

    /**
     * @param mixed $permission_id
     */
    public function setPermissionId($permission_id): void
    {
        $this->permission_id = $permission_id;
    }

}
