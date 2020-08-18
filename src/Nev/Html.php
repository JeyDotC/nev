<?php

namespace Nev;

/**
 *
 * @author jguevara
 */
trait Html
{

    public function js(...$scripts)
    {
        return implode(PHP_EOL, array_map(function ($script){
            return "<script type=\"text/javascript\" src=\"$script\"></script>";
        }, $scripts));
    }

    public function css(...$cssFiles)
    {
        return implode(PHP_EOL, array_map(function ($css){
            return "<link type=\"text/css\" rel=\"stylesheet\" href=\"$css\"/>";
        }, $cssFiles));
    }

    public function attrs(array $attrs)
    {
        return implode(' ', array_map(function ($value, $name){
            return "$name=\"$value\"";
        }, $attrs, array_keys($attrs)));
    }

    public function style(array $style)
    {
        $styleParts = [];
        foreach ($style as $key => $value) {
            $styleParts [] = "$key: $value;";
        }

        return implode(' ', $styleParts);
    }

    public function classes(...$classes)
    {
        $resultingClasses = [];
        foreach ($classes as $classSpec) {
            if (is_string($classSpec)) {
                $resultingClasses[] = $classSpec;
            } else if (is_array($classSpec)) {
                foreach ($classSpec as $candidateOrIndex => $shouldRenderOrClassName) {
                    if (is_numeric($candidateOrIndex)) {
                        $resultingClasses[] = $shouldRenderOrClassName;
                    } else if ($shouldRenderOrClassName) {
                        $resultingClasses[] = $candidateOrIndex;
                    }
                }
            }
        }

        return implode(' ', $resultingClasses);
    }
}
