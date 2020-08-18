<?php

namespace Nev;

/**
 * Description of View
 *
 * @author jguevara
 */
abstract class View {
    private $__extraProperties = [];

    function __construct(array $props = []) {
        foreach ($props as $key => $value) {
            // This is to give support for attribute presence construct e.g. <input disabled />.
            if (is_numeric($key)){
                $key = $value;
                $value = true;
            }
            if (!property_exists(get_called_class(), $key)) {
                $this->__extraProperties[$key] = $value;
            }
            $this->{$key} = $value;
        }
    }

    public final static function create(array $props = []) {
        return new static($props);
    }

    public final static function show(array $props = []): string {
        return self::draw(new static($props));
    }

    public final function display(): string {
        return self::draw($this);
    }

    protected final static function draw($part): string {
        if (is_callable($part)) {
            ob_start();
            ($part)();
            return ob_get_clean();
        } else if ($part instanceof View) {
            ob_start();
            $part->render();
            return ob_get_clean();
        } /*else if (class_exists($part)) {
            call_user_func([$part, 'show'], $model);
        }*/ else {
            return $part;
        }
    }

    protected final static function drawIf(bool $condition, $part): string {
        return $condition ? self::draw($part) : '';
    }

    protected final static function drawEach(array $collection, callable $entryRenderer): string {
        return implode(PHP_EOL, array_map(function ($entry) use ($entryRenderer) {
            return self::draw(function () use ($entryRenderer, $entry) {
                $entryRenderer($entry);
            });
        }, $collection));
    }

    protected function extraProperties() {
        return $this->__extraProperties;
    }

    protected abstract function render();
}
