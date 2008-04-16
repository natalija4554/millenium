<?php

/**
* This class extends a standard Zend_ACL for use with a database.
* Written by Michael MistaGee Ziegler
* License: LGPL v2 or above
* The constructor expects a Zend_DB object as parameter.
*
*
* Database structure:
*
* +------------------+       +----------------+       +-------------+      +--------------+
* |    Resources     |       |    Access      |       |   Roles     |      |  Inheritance |
* +------------------+       +----------------+       +-------------+      +--------------+
* | *id              |<--.   | *role_id       |------>| *id         |<-----| *child_id    |
* | parent_id        |---'`--| *resource_id  N|       +-------------+   `--| *parent_id   |
* | *privilege      N|<------| *privilege    N|                            | order        |
* +------------------+       | allow          |                            +--------------+
*                            +----------------+
*
*
*   *field = PRIMARY KEY( field )
*   -----> = foreign key constraint
*
*   The actual table names should be: acl_resources, acl_access, acl_roles, acl_inheritance.
*
* access.allow is a boolean field, that specifies whether the respective rule is an allow rule or a deny rule (important for inherited access).
*
* The inheritance table stores which Role is to inherit rights from which parent rules. There can
* be multiple parent rules. If a rule inherits rights from more than one parent, the first rule applicable
* will be used to determine whether to allow or deny the access rights in question.
* The order field stores in which order the parents are to be introduced to Zend_ACL, effectively setting
* the order the parent rights are evaluated in.
* Using a relational database for this is strongly advised, as it guarantees data integrity.
*
* If you intend to give each resource a specific name or collect other data about it, you should create
* an extra table storing this data and put a foreign key referencing this into the resources table. Same
* goes for the privileges.
*
*/


require_once 'Zend/Db.php';

require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Role.php';
require_once 'Zend/Acl/Resource.php';


class Colla_Acl extends Zend_ACL {
    
    private $dbase;
    
    public function hasAllRolesOf( array &$searchRoles ){
        foreach( $searchRoles as $theRole )
            if( !$this->hasRole( $theRole ) )
                return false;
        return true;
        }
    
    /**
     * Modified to default adapter
     */
    public function __construct(){
        
    	$this->dbase = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        // I chose to write the field names into these SQL statements, so that the tables can actually contain more
        // fields than just the ones I need here without producing heavier DB load as neccessary.
        $db = $this->dbase;
    	
        /// First: Create all the resources we have.
        $resources = $db->fetchAll( $db->select()->distinct()->from( 'acl_resources', array( 'id', 'parent_id' ) ) );
        
        $resCount  = count( $resources );
        $addCount  = 0;
        
        $allResources = array();
        foreach( $resources as $theRes ){
            $allResources[] = $theRes['id'];
            }
        foreach( $resources as $theRes ){
            if( $theRes['parent_id'] !== null && !in_array( $theRes['parent_id'], $allResources ) ){
                require_once 'Zend/Acl/Exception.php';
                throw new Zend_Acl_Exception(
                    "Resource id '".$theRes['parent_id']."' does not exist"
                    );
                }
            }
        
        while( $resCount > $addCount ){
            foreach( $resources as $theRes ){
                // Check if parent resource (if any) exists
                // Only add if this resource hasn't yet been added and its parent is known, if any
                if( !$this->has( $theRes['id'] ) &&
                    ( $theRes['parent_id'] === null || $this->has( $theRes['parent_id'] ) )
                  ){
                    $this->add( new Zend_Acl_Resource( $theRes['id'] ), $theRes['parent_id'] );
                    $addCount++;
                    }
                }
            }
        
        /// Now create all roles
        $roles = $db->fetchAll(
            $db->select()
            ->from(     array( 'r' => 'acl_roles' ),       array( 'r.id', 'i.parent_id' ) )
            ->joinLeft( array( 'i' => 'acl_inheritance' ), 'r.id=i.child_id'              )
            ->order(    array( 'child_id', 'order' ) )
            );
        
        // Create an array that stores all roles and their parents
        $dbElements = array();
        foreach( $roles as $theRole ){
            if( !isset( $dbElements[ $theRole['id'] ] ) )
                $dbElements[ $theRole['id'] ] = array();
            if( $theRole['parent_id'] !== null )
                $dbElements[ $theRole['id'] ][] = $theRole['parent_id'];
            }
        
        // Now add to the ACL
        $dbElemCount  = count( $dbElements );
        $aclElemCount = 0;
        
        // while there are still elements left to be added
        while( $dbElemCount > $aclElemCount ){
            // Check every element in the db
            foreach( $dbElements as $theDbElem => $theDbElemParents ){
                // Check if a parent is invalid to prevent an infinite loop
                // if the relational DBase works, this shouldn't happen
                foreach( $theDbElemParents as $theParent ){
                    if( !array_key_exists( $theParent, $dbElements ) ){
                        require_once 'Zend/Acl/Exception.php';
                        throw new Zend_Acl_Exception(
                            "Role id '$theParent' does not exist"
                            );
                        }
                    }
                if( !$this->hasRole( $theDbElem ) &&            // if it has not yet been added to the ACL
                    ( empty( $theDbElemParents )  ||            // and no parents exist or
                      $this->hasAllRolesOf( $theDbElemParents ) // we know them all
                    )
                  ){
                    // we can add to ACL
                    $this->addRole( new Zend_Acl_Role( $theDbElem ), $theDbElemParents );
                    $aclElemCount++;
                    }
                }
            }
        
        
        /// Now create all access rules
        $access = $db->fetchAll( $db->select()->from( 'acl_access', array( 'role_id','resource_id','privilege','allow' ) ) );
        
        foreach( $access as $theRule ){
            if( $theRule['allow'] == true )
                $this->allow( $theRule['role_id'], $theRule['resource_id'], $theRule['privilege'] );
            else    $this->deny(  $theRule['role_id'], $theRule['resource_id'], $theRule['privilege'] );
            }
        }
    
    } 