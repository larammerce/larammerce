<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 3/2/19
 * Time: 3:15 PM
 */

namespace App\Utils\DS;


use RuntimeException;

class Stack {

    protected $stack;
    protected $limit;

    public function __construct($limit = 50, $initial = []) {
        $this->stack = $initial;
        $this->limit = $limit;
    }

    public function push($item) {
        if (count(is_countable($this->stack)?$this->stack :[]) < $this->limit) {
            array_unshift($this->stack, $item);
        } else {
            throw new RunTimeException('Stack is full!');
        }
    }

    public function pop() {
        if ($this->isEmpty()) {
            throw new RunTimeException('Stack is empty!');
        } else {
            return array_shift($this->stack);
        }
    }

    public function top() {
        return current($this->stack);
    }

    public function isEmpty() {
        return empty($this->stack);
    }

}