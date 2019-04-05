<?php
require_once './vendor/autoload.php';

class SomeViewModel
{

    public $id;
    public $name;
    public $description;

    function __construct($id, $name, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

}

class VerySimpleComponent extends \Nev\View
{
    protected function defaultProps(): array {
        return [
            'text' => '',
        ];
    }

    protected function render() {
        $props = $this->props;
        ?>
        <div class="border border-danger rounded">
            <p><?=$props->text?></p>
        </div>
        <?
    }

}

class ModelDependentComponent extends Nev\View
{

    /**
     *
     * @var SomeViewModel 
     */
    protected $model;

    protected function render() {
        $model = $this->model;
        ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= $model->id ?>: <?= $model->name ?></h5>
                <p class="card-text">
                    <?= $model->description ?>
                </p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        <?
    }

}

class GenericAlertComponent extends Nev\View
{
    use \Nev\Html;
    
    private static $defaultProps = [
        'attrs' => [],
        'classes' => [],
        'status' => 'info',
        'dismissable' => true,
        // Child items
        'title' => '',
        'body' => '',
    ];
    
    protected function defaultProps(): array {
        return self::$defaultProps;
    }

    protected function render() {
        $model = $this->model;
        $props = $this->props;
        $attrs = is_array($props->attrs) ? $props->attrs : [];
        $extraClasses = is_array($this->props->classes) ? $this->props->classes : [];
        ?>
        <div <? $this->attrs($attrs) ?> 
            class="<?=implode(' ', $extraClasses)?> alert alert-<?= $props->status ?> <?=$props->dismissable ? 'alert-dismissible fade show' : '' ?> "
            role="alert">
            
            <?if($props->title):?>
            <h4 class="alert-heading"><? $this->renderPart($props->title, $model) ?></h4>
            <?endif;?>
            
            <?if($props->dismissable):?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?endif;?>
            
            <? $this->renderPart($props->body, $model) ?>
        </div>
        <?
    }

}

class SampleView extends Nev\View
{

    use \Nev\Html;

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

SampleView::show([
    new SomeViewModel(1, 'Johny', 'Be good'),
    new SomeViewModel(2, 'Jenny', 'Be cool!'),
    new SomeViewModel(3, 'Jinny', 'Be smooth'),
]);

