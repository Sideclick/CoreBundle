<?php
namespace Sc\CoreBundle\Service;

use Knp\Component\Pager\Paginator;

use Countable, Iterator, ArrayAccess;

/**
 * Rowan: I am not sure if there is a better implementation of this somewhere
 * out there - but this is my attempt.
 * 
 * This class provides a way to easily iterate through the results of a query
 * or query builder.  Or get the total results of the query/querybuilder
 * or retrieve a KNP_Pagination of the query/query builder
 * 
 * All of this is achieved with only the bare minumum DB queries being run.
 */
class DataContainer implements Countable, Iterator, ArrayAccess
{
    protected $_dataPosition = 0;
    
    protected $_data = null;
    
    protected $_queryBuilder = null;
    
    protected $_query = null;
    
    protected $_paginator = null;
    
    protected $_defaultPagination = null;
    
    
    /**
     * Pass the data source in, it should be a Query or a QueryBuilder
     * You will now be able to access this object as an array to fetch data
     * about the resultset.
     * 
     * @throws \Exception
     */
    public function __construct($dataSource)
    {
        if ($dataSource instanceof \Doctrine\ORM\Query) {
            
            $this->_query = $dataSource;
        } else if ($dataSource instanceof \Doctrine\ORM\QueryBuilder) {
            
            $this->_queryBuilder = $dataSource;
            $this->_query = $this->_queryBuilder->getQuery();
        } else {
            throw new \Exception('Invalid data source provided: ' . get_class($dataSource));
        }
        
        $this->_paginator = new Paginator();
        
        $this->_defaultPagination = $this->_paginator->paginate($dataSource);
        
        //if datasource is query then set local query if it is a builder then
        // populate the builder
        $this->_dataPosition = 0;
    }
    
    protected function _setData()
    {
        if ($this->_data == null) {
            $this->_data = $this->_query->execute();
        }
    }
    
    /**
     * Returns the QueryBuilder
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->_queryBuilder;
    }
    
    /**
     * Get a instance of a Knp_Pagination for the query
     * 
     * @param int $page
     * @param int $countPerPage
     * @return type
     */
    public function getPagination($page = 1, $countPerPage = 10 )
    {
        return $this->_paginator->paginate($this->_query);
    }
    
    public function count()
    {
        return $this->_defaultPagination->getTotalItemCount();
    }
    
    function rewind() {
        
        $this->_dataPosition = 0;
    }

    function current() {
        
        $this->_setData();
        return $this->_data[$this->_dataPosition];
    }

    function key() {
        
        return $this->_dataPosition;
    }

    function next() {
        
        ++$this->_dataPosition;
    }

    function valid() {
        $this->_setData();
        return isset($this->_data[$this->_dataPosition]);
    }
    
    public function offsetSet($offset, $value) {
        
        $this->_setData();
        
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        
        $this->_setData();
        
        return isset($this->_data[$offset]);
    }
    public function offsetUnset($offset) {
        
        $this->_setData();
        
        unset($this->_data[$offset]);
    }
    public function offsetGet($offset) {
        
        $this->_setData();
        
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
    
    /**
     * Returns the entire dataset as an array.
     * @todo I am not sure if this works.  It might return the data in the wrong
     * format.
     * 
     * @return type
     */
    public function toArray()
    {
        
        $this->_setData();
                
        return $this->_data;
    }
}