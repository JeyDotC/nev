<?php

namespace Nev;

/**
 *
 * @author jguevara
 */
trait Html
{

    public function js($script)
    {
        ?>
        <script type="text/javascript" src="<?= $script ?>"></script>
        <?php
        return $this;
    }

    public function css($css)
    {
        ?>
        <link type="text/css" rel="stylesheet" href="<?= $css ?>"/>
        <?php
        return $this;
    }

    public function attrs(array $attrs)
    {
        return $this->renderAttributes($attrs);
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

    private function renderAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            echo "$name=\"$value\" ";
        }
    }

}
