<?php

class Task {

    public $description;

    public $completed = false;

    public function __construct($description) //method
    {
        $this->description = $description;

    }

    public function complete()
    {
        $this->completed = true;
    }

    public function isComplete()
    {
        return $this->completed;
    }

}