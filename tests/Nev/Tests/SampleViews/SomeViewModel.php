<?php

namespace Nev\Tests\SampleViews;

/**
 * Description of SomeViewModel
 *
 * @author Win 10
 */
class SomeViewModel {

    public $id;
    public $name;
    public $description;

    function __construct($id, $name, $description = '') {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

}
