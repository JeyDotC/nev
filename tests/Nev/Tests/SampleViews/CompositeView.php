<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

/**
 * Description of CompositeView
 *
 * @author Win 10
 */
class CompositeView extends View {

    use Html;

    /**
     *
     * @var SomeViewModel[]
     */
    protected $model;

    /**
     * CompositeView constructor.
     * @param SomeViewModel[] $model
     */
    public function __construct(array $model) {
        parent::__construct([]);
        $this->model = $model;
    }

    protected function render() { ?>
        <h1>Hello world.</h1>

        <div class="container-fluid">
            <div class="row">
                <?= self::drawEach($this->model, function (SomeViewModel $viewModel) { ?>
                    <div class="col-lg-3 col-sm-6">
                        <?= ModelDependentComponent::show(['model' => $viewModel]) ?>
                    </div>
                <? }) ?>
            </div>
        </div>

        <hr/>

        <?= GenericAlertComponent::show([
            'id' => 'my-cool-alert',
            'status' => 'danger',
            'className' => [
                'border',
                'border-primary',
                'rounded'
            ],
            // Child elements
            'title' => 'This is a <em>summary</em> alert!.',
            'body' => function () { ?>
                <p>There are <strong><?= count($this->model) ?></strong> view models.</p>
                <p>This modal has been created with some kind of generic component.</p>
            <? },
        ]) ?>

        <hr/>

        <?= GenericAlertComponent::show([
            // This will map to 'dismissible' => true,
            'dismissible',
            // Child elements
            'title' => 'The child elements can be component instances.',
            'body' => VerySimpleComponent::create(['text' => 'This is a very simple component!',]),
        ]) ?>

        <?
    }
}
