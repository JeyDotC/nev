<?php
require_once './vendor/autoload.php';

trait Bootstrap
{
    
    public function bootstrapMeta() {
        ?>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?
        return $this;
    }

    public function bootstrapCss() {
          ?>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
        <?php
        return $this;
    }

    public function bootstrapJs() {
          ?>
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.slim.min.js" ></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" ></script>
        <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" ></script>
        <?php
        return $this;
    }

}

class SampleView extends Nev\View
{
    use \Nev\Html;
    use Bootstrap;

    protected function render() {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <? $this->bootstrapMeta()->bootstrapCss(); ?>
                
                <title>Hello Nev</title>
            </head>
            <body>
                <h1>Hello world. I'm using the bootstrap helper!</h1>
                
                <? $this->bootstrapJs(); ?>
            </body>
        </html>
        <?
    }

}

(new SampleView())->display();

