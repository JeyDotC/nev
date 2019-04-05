<?php

namespace Nev;

/**
 *
 * @author jguevara
 */
trait Html
{

    public function js($script) {
        ?>
        <script type="text/javascript" src="<?= $script ?>" ></script>
        <?php
        return $this;
    }

    public function css($css) {
        ?>
        <link type="text/css" rel="stylesheet" href="<?= $css ?>" />
        <?php
        return $this;
    }

    public function attrs(array $attrs) {
        $this->renderAttributes($attrs);
    }

    private function renderAttributes(array $attributes) {
        foreach ($attributes as $name => $value) {
            echo "$name=\"$value\" ";
        }
    }

}
