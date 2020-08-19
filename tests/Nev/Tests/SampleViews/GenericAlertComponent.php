<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

class GenericAlertComponent extends View {
    use Html;

    protected $attrs = [];
    /**
     * @var array|string
     */
    protected $className = [];
    protected $status = 'info';
    protected $dismissible = false;
    protected $title = '';
    protected $body = '';

    public function render() {
        $attrs = $this->extraProperties();
        $classes = $this->classes($this->className, "alert alert-{$this->status}", ['alert-dismissible fade show' => $this->dismissible]);
        ?>
        <div <?= $this->attrs($attrs) ?> class="<?= $classes ?>" role="alert">

            <?= self::drawIf($this->title, function () { ?>
                <h4 class="alert-heading"><?= self::draw($this->title) ?></h4>
            <? }) ?>

            <?= self::drawIf($this->dismissible, function () { ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
            <? }) ?>

            <?= self::draw($this->body) ?>
        </div>
        <?
    }
}
