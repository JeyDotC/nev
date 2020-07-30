<?php

namespace Nev;

/**
 * Description of View
 *
 * @author jguevara
 */
abstract class View
{
    private $__extraProperties = [];

    function __construct(array $props = []) {
        foreach ($props as $key => $value) {
            if(!property_exists(get_called_class(), $key)){
                $this->__extraProperties[$key] = $value;
            }
            $this->{$key} = $value;
        }
    }

    public static function create(array $props = []) {
        return new static($props);
    }

    public static function show(array $props = []) {
        (new static($props))->display();
    }

    public final function display() {
        echo $this->displayAndGet();
    }

    public final function displayAndGet(): string {
        return $this->doDisplay();
    }

    private function doDisplay(): string {
        ob_start();
        $this->render();
        return ob_get_clean();
    }

    protected function renderPart($part) {
        if (is_callable($part)) {
            ($part)();
        } else if ($part instanceof \Nev\View) {
            $part->display();
        } /*else if (class_exists($part)) {
            call_user_func([$part, 'show'], $model);
        }*/ else {
            echo $part;
        }
    }

    protected function extraProperties(){
        return $this->__extraProperties;
    }

    protected abstract function render();
}
