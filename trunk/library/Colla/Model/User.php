<?php

class Colla_Model_User
{
    protected $_data = array(
        'id'       => null,
        'fullname' => 'anonymous user',
        'username' => null,
        );

    protected $_dbTableRow = null;

    public function getId()
    {
        return $this->_data['id'];
    }

    public function getUsername()
    {
        return $this->_data['username'];
    }

    public function getFullname()
    {
        return $this->_data['fullname'];
    }

    public function setFullname($fullname)
    {
        if (null === $this->_dbTableRow) {
            throw new Exception('Cannot set fullname for anonymous user');
        }
        $this->_dbTableRow->fullname = $fullname;
        $this->_dataSync('fullname');
    }

    public function setPassword($password)
    {
        if (null === $this->_dbTableRow) {
            throw new Exception('Cannot set password for anonymous user');
        }
        $this->_dbTableRow->password = md5($password);
    }

    public function __construct(stdClass $user = null)
    {
        if (null !== $user) {
            $userTable = new Colla_Db_Table_User(array('db' => Colla_App::getInstance()->getDb()));
            $userRowset = $userTable->find($user->id);
            if (count($userRowset) !== 1) {
                throw new Exception('User not found in database');
            }
            $this->_dbTableRow = $userRowset->current();
            $this->_dataSync();
        }
    }

    public function save()
    {
        if (null === $this->_dbTableRow) {
            throw new Exception('Cannot save for anonymous user');
        }
        $this->_dbTableRow->save();
    }

    protected function _dataSync($key = null)
    {
        if (null !== $key) {
            $this->_data[$key] = $this->_dbTableRow->$key;
        } else {
            foreach (array_keys($this->_data) as $key) {
                $this->_data[$key] = $this->_dbTableRow->$key;
            }
        }
    }
}
