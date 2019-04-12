<?php
namespace Nev\Tests\SampleViews;

use Nev\View;


/**
 * Description of ModelDependentComponent
 *
 * @author Win 10
 */
class ModelDependentComponent extends View
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