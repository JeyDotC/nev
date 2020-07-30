<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

class GenericAlertComponent extends View
{
    use Html;

    protected $attrs = [];
    /**
     * @var array|string
     */
    protected $className = [];
    protected $status = 'info';
    protected $dismissible = true;
    protected $title = '';
    protected $body = '';

    protected function render()
    {
        $attrs = $this->extraProperties();
        ?>
        <div <? $this->attrs($attrs) ?>
                class="<? $this->classes($this->className, "alert alert-{$this->status}", [ 'alert-dismissible fade show' => $this->dismissible ] ) ?>"
                role="alert">

            <?
            if ($this->title): ?>
                <h4 class="alert-heading"><? $this->renderPart($this->title) ?></h4>
            <? endif; ?>

            <?
            if ($this->dismissible): ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
            <? endif; ?>

            <? $this->renderPart($this->body) ?>
        </div>
        <?
    }
}
