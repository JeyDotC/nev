<?php

namespace Nev\Tests\SampleViews;

use Nev\View;

/**
 * Description of VerySimpleComponent
 *
 * @author Win 10
 */
class VerySimpleComponent extends View
{
    /**
     * @var string
     */
    protected $text = '';

    protected function render() {
        ?>
        <div class="border border-danger rounded">
            <p><?=$this->text?></p>
        </div>
        <?
    }

}
