# Welcome to NEV (No Engine Views)

> Do you know why there are templating engines out there? because we're undisciplined people!

## Installing

```shell
composer require jeydotc/nev
```

## Creating a view

```php
<?php
namespace MyNamespace\Views;
use Nev\View;

class MyView extends View {

    protected function render() {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <title>Hello Nev</title>
            </head>
            <body>
                <h1>Hello world.</h1>
            </body>
        </html>
        <?
    }
}
```

## Rendering it directly

```php
MyView::show();
```

## Rendering it and getting the result as a string

```php
$myString = MyView::create()->displayAndGet();
```

## What is all this?

Nev is just the idea that, if you're disciplined enough, you can just use regular PHP classes to represent your views. All you need is a simple base class to derive from which hide most of the uggly stuff.

So, what can I do with this shing? Well, let me walk you through:

First, let's take a look at a simple view:

```php
<?php
namespace MyNamespace\Views;

use Nev\View;

// Just create a regular PHP class and inherit from Nev\View
class BasicView extends View {
   
    // Optionally, use the Html trait to have a few helper methods
    use \Nev\Html;

    // Implement the abstract render method. (And yes, I stealed the syntax from react).
    protected function render() {
        ?>
        <!-- As a recommendation, do as much HTML as you can so your view remains clear --!>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <!-- This method from the HTML trait will render a link tag. -->
                <? $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css') ?>
                
                <title>Hello Nev</title>
            </head>
            <body>
                <h1>Hello world.</h1>

                <!-- This method from the HTML trait will render a script tag. -->
                <? $this->js("https://code.jquery.com/jquery-3.3.1.slim.min.js")
                        ->js("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js")
                        ->js("https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js");
                ?>
            </body>
        </html>
        <?
    }
}
```

Ok, as you can see, create a view required you to just create a class and give it the responsibility of rendering HTML. Just a few words of wisdom, and this also applies if you just use plain PHP files:

* Use HTML as much as you can! don't do this `echo "<a href='$someVariable'>$someOtherVariable</a>"`, or this: `echo "$someCrazyStringContainingHTMLYouBuiltWithcomplexOperations`. I can swear, you'll regret it.
* View classes should be used only as **views**! don't make them call the database or deal with complex operations, do that at your business layer and give the view the info you want to render.
* These are plain classes, that means you can compose them and do all that classy stuff. Just keep in mind the item above.

Now, as we mentioned above, these are regular classes, so, you can:

## Inherit from other Views.

Let's create this sample base class that uses bootstrap:

```php
<?php
namespace MyNamespace\Views;
use Nev\Html;
use Nev\View;

// Yes, you can declare the class abstract, just that you won't be able to render a thing unless you derive a class from it.
abstract class ParentView extends View
{
    use Html;
    protected final function render() { ?>
        <!-- Render all the boilerplate code! -->
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <? $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css') ?>
                <!-- Put a few placeholders like this. -->
                <? $this->styles() ?>
                <title>Hello Nev</title>
            </head>
            <body>
                <!-- Child class should provide a body. -->
                <? $this->body() ?>
                <? $this->js("https://code.jquery.com/jquery-3.3.1.slim.min.js")
                        ->js("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js")
                        ->js("https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js");
                ?>
                <!-- Child class could render some extra scripts. -->
                <? $this->scripts() ?>
            </body>
        </html>
        <?
    }
    // Make the method abstract to enforce the child view to render a body.
    protected abstract function body();
    // And just provide empty methods for the optional place holders.
    protected function scripts() { }
    protected function styles() { }
}
```

And now that we moved all the boilerplate things to a parent class, all we need to do is inherit it:

```php
<?php
namespace MyNamespace\Views;

class ChildView extends ParentView
{
    protected function body() { ?>
        <h1>Hello, World!</h1>
        <p>
            It feels good to do things without dealing with boilerplate things.
        </p>
        <? 
    }
}
```

## Passing data to the view

Now, you probably wonder how to send data to the view?

Well, all you have to do is pass in a **model**. In Nev terms, the **model** is just the info you collected at your controller that you want to be rendered:

```php
<?php
namespace Nev\Tests\SampleViews;
class ChildView extends ParentView
{
    /**
     * Your IDE will surely have support for autocompleting this :D
     * 
     * @var SomeViewModel 
     */
    protected $model;

    protected function body() { ?>
        <h1>Hello, user Nº <?=$this->model->id?></h1>
        <p>
            Sorry to treat you in such a cold manner Mr <?=$this->model->name?>, my programmer just made me that way.
        </p>
        <? 
    }
}
```

And at your controller:

```php
// This can be anything, from scalar values to arrays, objects, whatever.
$someViewModel = new SomeViewModel();

// Render directly to browser.
ChildView::show($someViewModel);

// Or get the rendered value as string.
$renderedResult = ChildView::create()->displayAndGet($someViewModel);
```

## Passing props to the view

If you like to keep concerns separated, you'd probably like to keep view options away from your **model**.

> By view options we mean info like, breadcrumb settings, browser titles, css classes and so on.

Let's add some options to our parent view:

```php
<?php
namespace MyNamespace\Views;
use Nev\Html;
use Nev\View;

abstract class ParentView extends View
{
    use Html;

    // Override this method to provide default props
    protected function defaultProps(): array {
        return [
            'title' => '',
            'breadcrumb' => []
        ];
    }

    protected final function render() { ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <? $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css') ?>
                <? $this->styles() ?>
                <!-- Props is a stdClass object. -->
                <title>Nev: <?=$this->props->title?></title>
            </head>
            <body>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <? foreach ($this->props->breadcrumb as $breadcrumbItem): ?>
                      <li class="breadcrumb-item <?=$breadcrumbItem['active']? 'active' : '' ?>"
                          href="<?=$breadcrumbItem['url']?>" >
                        <?=$breadcrumbItem['text']?>
                      </li>
                      <? endforeach; ?>
                    </ol>
                </nav>
                <? $this->body() ?>
                <? $this->js("https://code.jquery.com/jquery-3.3.1.slim.min.js")
                        ->js("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js")
                        ->js("https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js");
                ?>
                <? $this->scripts() ?>
            </body>
        </html>
        <?
    }
    protected abstract function body();
    protected function scripts() { }
    protected function styles() { }
}
```

And also a few options for our child view:

```php
<?php
namespace Nev\Tests\SampleViews;
class ChildView extends ParentView
{
    /**
     * @var SomeViewModel 
     */
    protected $model;

    // Override this method to provide default props
    protected function defaultProps(): array {
        return parent::defaultProps() + [
            'status' => ''
        ];
    }

    protected function body() { ?>
        <h1 class="text-<?=$this->props->status?>">Hello, user Nº <?=$this->model->id?></h1>
        <p>
            Sorry to treat you in such a cold manner Mr <?=$this->model->name?>, my programmer just made me that way.
        </p>
        <? 
    }
}
```

And at your controller:

```php
// This can be anything, from scalar values to arrays, objects, whatever.
$someViewModel = new SomeViewModel();

// Create the props as an associative array.
$props = [
    'title' => 'Some title',
    'breadcrumb' => [
        ['text' => 'Home', 'url' => '/' ],
        ['text' => 'Library', 'url' => '/Library' ],
        ['text' => 'Data', 'url' => '/Library/Data', 'active' => true ],
    ],
    'status' => ''
];

// Render directly to browser.
ChildView::show($someViewModel, $props);

// Or get the rendered value as string.
$renderedResult = ChildView::create($props)->displayAndGet($someViewModel);
```

TODO: Document views composition, and utility traits.