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
