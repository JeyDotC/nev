<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

/**
 * Description of CompositeView
 *
 * @author Win 10
 */
class CompositeView extends View
{

    use Html;

    /**
     *
     * @var SomeViewModel[] 
     */
    protected $model;

    protected function render() {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <? $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css') ?>

                <title>Hello Nev</title>
            </head>
            <body>
                <h1>Hello world.</h1>

                <div class="container-fluid">
                    <div class="row">
                        <? foreach ($this->model as $viewModel): ?>
                            <div class="col-lg-3 col-sm-6">
                                <? ModelDependentComponent::show($viewModel) ?>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
                
                <hr />

                <?
                GenericAlertComponent::show($this->model, [
                    'status' => 'danger',
                    'dismissable' => false,
                    'attrs' => [
                        'id' => 'my-cool-alert'
                    ],
                    'classes' => [
                        'border', 
                        'border-primary',
                        'rounded'
                    ],
                    // Child elements
                    'title' => 'This is a <em>summary</em> alert!.',
                    'body' => function ($model) {
                        ?>
                        <p>There are <strong><?= count($model) ?></strong> view models.</p>
                        <p>This modal has been created with some kind of generic component.</p>
                        <?
                    }]);
                ?>
                
                <hr/>
                
                <?
                GenericAlertComponent::show($this->model, [
                    // Child elements
                    'title' => 'The child elements can be component instances.',
                    'body' => VerySimpleComponent::create([
                        'text'=> 'This is a very simple component!',
                    ])]);
                ?>
                        

                <?
                $this->js("https://code.jquery.com/jquery-3.3.1.slim.min.js")
                        ->js("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js")
                        ->js("https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js");
                ?>
            </body>
        </html>
        <?
    }

}
