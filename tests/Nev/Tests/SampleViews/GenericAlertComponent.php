<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

class GenericAlertComponent extends View
{
    use Html;
    
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
        $extraClassesArray = is_array($this->props->classes) ? $this->props->classes : [];
        $extraClasses = implode(' ', $extraClassesArray);
        $dismissableClasses = $props->dismissable ? 'alert-dismissible fade show' : '';
        ?>
        <div <? $this->attrs($attrs) ?> 
            class="<?=$extraClasses?> alert alert-<?= $props->status ?> <?=$dismissableClasses?>"
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
