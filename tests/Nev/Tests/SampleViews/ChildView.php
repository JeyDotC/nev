<?php

namespace Nev\Tests\SampleViews;

class ChildView extends ParentView
{

    /**
     *
     * @var SomeViewModel 
     */
    protected $model;

    protected function body() { ?>
        <h1>Hello, user Nº <?=$this->model->id?></h1>
        <p>
            Sorry to treat you in such a cold manner Mr <?=$this->model->name?>, my programmer just made me that way.
        </p>
        <? 
    }
}