<?php
namespace Simpleframework\RABC;
use Simpleframework\Applib\Database;
use PDO;


class Role extends Database
{
    private $permissions;
    
    public function __construct()
    {
        parent::__construct(DBCONFIG);
        
        $this->permissions = [];
    }
    
    public function getRoleperms($roleID)
    {
        $role = new Role();
        
        $query = "SELECT t2.permdesc FROM role_perm as t1
                  JOIN permissions as t2 ON t1.PermID = t2.PermID
                  WHERE t1.RoleID = :roleID";
        
        $this->query($query);
        $this->bind(':roleID',$roleID,PDO::PARAM_INT);
        $this->execute();
        
        $rows = $this->resultSet();
       
        foreach ($rows as $row) {
            $role->permissions[$row->permdesc] = true;
        }
 
        return $role;
    }
    
    public function hasPerm($permission)
    {
        return isset($this->permissions[$permission]);
    }
    
    
    public function addRole($roleName)
    {
        
    }
    
    public function updateRole($roleID)
    {
        
    }
    
    public function delete($roleID,$softDelete = true)
    {
        
    }
}