<?php
namespace DreamblazeNet\SqlS;

class InsertQueryTest extends \PHPUnit_Framework_TestCase {
    /***
     * @var InsertQuery
     */
    var $sqlobj;

    function setUp() {
        $dbobj = new DummyDatabaseObject();
        $this->sqlobj = new InsertQuery($dbobj);
    }

    function testQuery(){        
        $data = array(
           'testField1' => 'test',
           'testField2' => 'test2',
           'name' => 'testRecord'
        );
        $this->sqlobj->values($data);
        
        list($query,$values) = $this->sqlobj->give_sql_and_values();
        $this->assertEquals("INSERT INTO testTable (testTable.testField1, testTable.testField2, testTable.name) VALUES (:testField1,:testField2,:name)", $query);
        
        $test_data = array();
        foreach($data as $key=>$val){
            $test_data[':' . $key] = $val;
        }
        $this->assertEquals($test_data, $values);
    }
    
    function testCommands(){
        $data = array(
           'testField1' => 'test',
           'testField2' => '#NOW',
        );
        $this->sqlobj->values($data);
        
        list($query,$values) = $this->sqlobj->give_sql_and_values();
        $this->assertEquals("INSERT INTO testTable (testTable.testField1, testTable.testField2) VALUES (:testField1,NOW())", $query);
        
        $test_data = array(
            ':testField1' => 'test'
        );
        $this->assertEquals($test_data, $values);
    }
    
    function testWrongCommandFallback(){
        $data = array(
           'testField1' => 'test',
           'testField2' => '#NOWE',
        );
        $this->sqlobj->values($data);
        
        list($query,$values) = $this->sqlobj->give_sql_and_values();
        $this->assertEquals("INSERT INTO testTable (testTable.testField1, testTable.testField2) VALUES (:testField1,:testField2)", $query);
        
        $test_data = array(
            ':testField1' => 'test',
            ':testField2' => '#NOWE'
        );
        $this->assertEquals($test_data, $values);
    }
}