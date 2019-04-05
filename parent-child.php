<?php
require_once './vendor/autoload.php';

abstract class ParentView extends Nev\View
{

    use \Nev\Html;

    protected function render() { ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <? $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css') ?>
                <? $this->styles() ?>
                <title>Hello Nev</title>
            </head>
            <body>
                <? $this->body() ?>
                <? $this->js("https://code.jquery.com/jquery-3.3.1.slim.min.js")
                        ->js("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js")
                        ->js("https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js");
                ?>
                <? $this->scripts() ?>
            </body>
        </html>
        <?
    }

    protected abstract function body();

    protected function scripts() { }

    protected function styles() { }

}

class SomeViewModel
{

    public $id;
    public $name;

    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

}

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

(new ChildView())->display(new SomeViewModel(56645, "John Doe"));

