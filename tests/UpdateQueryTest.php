<?php
namespace DreamblazeNet\SqlS;

class UpdateQueryTest extends \PHPUnit_Framework_TestCase {
    var $table = 'testTable';
    var $fields = array('id', 'testid', 'tField1', 'testField2');
    var $fields_str;
    var $pk = 'testid';
    var $testString;
    var $testValues;
    var $testResultValues;

    /***
     * @var UpdateQuery
     */
    var $sqlobj;

    function setUp() {
        $this->table = DummyDatabaseObject::$table;
        $this->fields = DummyDatabaseObject::$fields;
        $this->pk = DummyDatabaseObject::$primary_key;

        $this->fields_str = BaseQuery::fields_to_sql($this->fields, $this->table);
        $this->testString = "UPDATE {$this->table} SET testfield2 = :testField2";
        $this->testValues = array('tfield1' => 'Blub', 'testField2' => 'Bluu');
        $this->testResultValues = array(':testField2' => 'Bluu');

        $this->sqlobj = new UpdateQuery(new DummyDatabaseObject());
    }

    function testQuery() {

        $this->sqlobj->set($this->testValues);

        list($sql_text, $values) = $this->sqlobj->give_sql_and_values();
        $this->assertEquals($this->testString, $sql_text);
        $this->assertEquals($values, $this->testResultValues);
    }
}
