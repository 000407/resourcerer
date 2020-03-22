<?php
/**
 * Created by IntelliJ IDEA.
 * User: Asus - PC
 * Date: 6/15/2019
 * Time: 3:08 PM
 */

abstract class AbstractModel {
    protected $con;
    protected $primaryKey = 'id';

    /**
     * BaseModel constructor.
     */
    public function __construct()
    {
        //TODO: Obtain the connection from a connection pool
        $this->con = new mysqli("127.0.0.1", "root", "", "db_project2019");
    }

    public function save($entity = null) {
        if(!isset($entity)){
            $entity = $this;
        }

        $properties = $this->getFieldValueMap($this);

        $fields = array_keys($properties);
        $values = array_map(array($this, 'flattenForSql'), array_values($properties));
        $keyValuePairs = array();

        foreach ($fields as $k => $f) {
            $keyValuePairs[] = "$f = ${values[$k]}";
        }

        $fieldList = implode(', ', $fields);
        $valueList = implode(', ', $values);
        $updateList = implode(', ', $keyValuePairs);

        $tableName = get_class($entity);

        $query = "INSERT INTO $tableName($fieldList) VALUES($valueList) ON DUPLICATE KEY UPDATE $updateList;";

        echo $query;
        /*$res = $this->con->query($query);

        if(!$res){
            $err = $this->con->error_list;
            //TODO:Error to be logged
            $res = null;
        }
        else {
            $res = $this->con->insert_id; //Get the ID of the record inserted last.
        }

        return $res;*/
    }

    /*public function update($updatedEntity = null, $idField = 'id') {
        $tableName = get_class($this);

        if(!isset($updatedEntity)){
            $updatedEntity = $this;
        }

        $fieldValueMap = array_map(array($this, 'toFlatSqlData'), $this->getFieldValueMap($updatedEntity));

        $generateSqlKeyValuePairs = function($k,$v){ //Function to generate key value pairs
            return "$k=$v";
        };

        $valueString = implode(',', $this->array_map_assoc($generateSqlKeyValuePairs, $fieldValueMap));

        $query = "UPDATE $tableName SET $valueString WHERE $idField='" . $this->$idField . "'";

        $res = $this->con->query($query);

        if(!$res){
            $err = $this->con->error_list;
            //TODO:Error to be logged
        }

        return $res;
    }*/

    /*public function toArray(){
        return call_user_func('get_object_vars', $this);
    }*/

    public function getInsertFields() {
        $reflect = new ReflectionClass($this);
        $properties   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        $fields = array();

        foreach ($properties as $p){
            $val = $p->getValue($this);

            if($val == null) {
                continue;
            }

            $fields[] = $p->getName();
        }

        return $fields;
    }

    public function getRelationFields($res) {
        $relationalFields = array();

        while($field = $res->fetch_field()) {
            $relationalFields[$field->name] = $field->type;
        }

        return $relationalFields;
    }

    public function getFieldValueMap($entity) {
        $reflect = new ReflectionClass($entity);
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        $values = array();

        foreach ($properties as $p){
            $name = $p->getName();
            $value = $p->getValue($entity);
            $values[$name] = $value;
        }

        return $values;
    }

    protected function select($query){
        $res = $this->con->query($query);

        if($res->num_rows < 0) {
            return null;
        }

        $record = $res->fetch_assoc();

        $fields = $this->getRelationFields($res);;

        foreach ($fields as $name => $type) {
            switch($type) {
                case MYSQL_TYPES['DATETIME']:
                    try {
                        $this->$name = new DateTime($record[$name]);
                    } catch (Exception $e) {
                        //TODO: Log error
                    }
                    break;
                    //TODO: Add other types as necessary
                default:
                    $this->$name = $record[$name];
                    break;
            }
        }

        return $this;
    }

    protected function exists($query) {
        $res = $this->con->query($query);
        return $res->num_rows > 0;
    }

    private function array_map_assoc( $callback , $array ) {
        $r = array();
        foreach ($array as $key=>$value)
            $r[$key] = $callback($key,$value);
        return $r;
    }

    private function flattenForSql($value) {
        switch (gettype($value)){
            case 'object':
                switch(get_class($value)) {
                    case 'DateTime':
                        return "'" . $value->format(MYSQL_DATETIME_FORMAT) . "'";
                    default:
                        //TODO: Serialize?
                }
                break;
            case 'string':
            case 'boolean':
                return "'$value'";
                break;
            case 'integer':
                return $value;
            default:
                return 'DEFAULT';
        }
    }
}