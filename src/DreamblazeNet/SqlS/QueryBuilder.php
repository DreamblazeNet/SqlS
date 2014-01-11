<?php

namespace DreamblazeNet\SqlS;

class QueryBuilder {
    public static function select($dbobject){
        return new SelectQuery($dbobject);
    }
    
    public static function insert($dbobject){
        return new InsertQuery($dbobject);
    }
    
    public static function update($dbobject){
        return new UpdateQuery($dbobject);
    }
}

