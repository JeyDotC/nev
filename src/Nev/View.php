<?php

namespace Nev;

/**
 * Description of View
 *
 * @author jguevara
 */
abstract class View
{

    protected $model;

    /**
     *
     * @var \stdClass
     */
    protected $props;

    function __construct(array $props = []) {
        $viewProps = $props + $this->defaultProps();

        $this->props = new \stdClass();

        foreach ($viewProps as $key => $value) {
            $this->props->{$key} = $value;
        }
    }

    public static function create(array $props = []) {
        return new static($props);
    }

    public static function show($model = null, array $props = []) {
        (new static($props))->display($model);
    }

    public final function display($model = null) {
        echo $this->displayAndGet($model);
    }

    public final function displayAndGet($model = null): string {
        $this->model = $model;

        return $this->doDisplay();
    }

    private function doDisplay(): string {
        ob_start();
        $this->render();
        return ob_get_clean();
    }

    protected function renderPart($part, $model) {
        if (is_callable($part)) {
            ($part)($model);
        } else if ($part instanceof \Nev\View) {
            $part->display($model);
        } /*else if (class_exists($part)) {
            call_user_func([$part, 'show'], $model);
        }*/ else {
            echo $part;
        }
    }

    protected function defaultProps(): array {
        return [];
    }

    protected abstract function render();
}
