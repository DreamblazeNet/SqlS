<?php
namespace DreamblazeNet\SqlS;

class SelectQueryTest extends \PHPUnit_Framework_TestCase {
    /***
     * @var SelectQuery
     */
    var $sqlobj;

    var $table;
    var $fields;
    var $fields_str;
    var $pk;

    function setUp() {
        $this->table = DummyDatabaseObject::$table;
        $this->fields = DummyDatabaseObject::$fields;
        $this->pk = DummyDatabaseObject::$primary_key;

        $this->fields_str = BaseQuery::fields_to_sql($this->fields, $this->table);
        $dbobj = new DummyDatabaseObject();
        $this->sqlobj = new SelectQuery($dbobj);
    }

    function testQuery() {
        $testString = "SELECT {$this->fields_str} FROM {$this->table}";

        list($sql, $values) = $this->sqlobj->give_sql_and_values();
        $this->assertEquals($testString, (string) $sql);
    }
    
    function testWhere(){
        $testString = "SELECT {$this->fields_str} FROM {$this->table} WHERE {$this->table}.id = :id";
        
        $testValues = array(':id' => 1);
        
        $this->sqlobj->where(array('id' => 1));

        list($sql, $values) = $this->sqlobj->give_sql_and_values();
        $this->assertEquals($testString, (string) $sql);
        $this->assertEquals($testValues,  $values);
    }
    
    function testWhereLike(){
        list($sql, $values) = $this->sqlobj->give_sql_and_values();
        
        $testString = "SELECT {$this->fields_str} FROM {$this->table} WHERE {$this->table}.id LIKE :id";
        
        $testValues = array(':id' => '1%');
        
        $this->sqlobj->where(array('id' => '1%'));

        list($sql, $values) = $this->sqlobj->give_sql_and_values();
        $this->assertEquals($testString, (string) $sql);
        $this->assertEquals($testValues,  $values);
    }
    
    function testDistinct(){
        $testString = "SELECT DISTINCT {$this->fields_str} FROM {$this->table}";
        
        $this->sqlobj->distinct();
        list($sql, $values) = $this->sqlobj->give_sql_and_values();
        
        $this->assertEquals($testString, (string) $sql);
    }

    function testCount(){
        $testString = "SELECT count(*) as c FROM {$this->table} WHERE testTable.id = :id";
        $testValues = array(':id' => '12');
        
        $this->sqlobj->where(array('id' => '12'))->counting();
        list($sql, $values) = $this->sqlobj->give_sql_and_values();
        
        $this->assertEquals($testString, (string) $sql);
        $this->assertEquals($testValues,  $values);
    }
    
}

