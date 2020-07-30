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
<?php
MyView::show();
```

## Rendering it and getting the result as a string

```php
<?php
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
        <!-- As a recommendation, do as much HTML as you can so your view remains clear -->
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

Well, it is just a matter of sending that data as key-value array, and all the keys of that array will be mapped to view properties, allowing you to do things like this:

```php
<?php
namespace Nev\Tests\SampleViews;
class ChildView extends ParentView
{
    /**
     * Your IDE will surely have support for auto-completing this :D
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
<?php
// This can be anything, from scalar values to arrays, objects, whatever.
$someViewModel = new SomeViewModel();

// Render directly to browser.
ChildView::show([ 'model' => $someViewModel ]);

// Or get the rendered value as string.
$renderedResult = ChildView::create([ 'model' => $someViewModel ])->displayAndGet();
```

## Passing extra properties to the view

There is no strict rules on the attributes you can send to the view, e.g. they don't necessarily need to be declared (more on that at the components section).

```php
<?php
namespace MyNamespace\Views;
use Nev\Html;
use Nev\View;

abstract class ParentView extends View
{
    use Html;

    // These two properties are not declared in the child view,
    // yet, they will be available if provided.
    /**
    * @var string
    */
   protected $title = '';
   
    /**
    * @var array 
    */
   protected $breadcrumb = [];

    protected final function render() { ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <? $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css') ?>
                <? $this->styles() ?>
                <!-- Props is a stdClass object. -->
                <title>Nev: <?=$this->title?></title>
            </head>
            <body>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <? foreach ($this->breadcrumb as $breadcrumbItem): ?>
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
        <!-- Let's say we forgot to declare the 'status' property, it will be available anyway -->
        <h1 class="text-<?=$this->status?>">Hello, user Nº <?=$this->model->id?></h1>
        <p>
            Sorry to treat you in such a cold manner Mr <?=$this->model->name?>, my programmer just made me that way.
        </p>
        <? 
    }
}
```

And at your controller:

```php
<?php
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
    'status' => '',
    'model' => $someViewModel,
];

// Render directly to browser.
ChildView::show($props);

// Or get the rendered value as string.
$renderedResult = ChildView::create($props)->displayAndGet();
```

## Composing stuff

Something pretty useful you can do with Nev is creating components that can be used by other views. 

Components have nothing special, they are just regular views with a different purpose.

Let's say we want to create a Bootstrap alert component, to do that, we just create a view:

```php
<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

final class AlertComponent extends View
{
    use Html;
    
    protected function render()
    { ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Hello!</h4>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            This is a cool alert!
        </div>
        <?
    }
}

``` 

At your view:

```php
<?php
namespace Nev\Tests\SampleViews;
use Nev\Tests\SampleViews\AlertComponent;

class ChildView extends ParentView
{
    // Some content skipped...
    
    protected function body() { ?>
 
        <!-- Let's display the alert. -->
        <?AlertComponent::show()?>
        
        <h1>Hello, user Nº <?=$this->model->id?></h1>
        <p>Lots of content!.</p>
        <? 
    }
}
```

So far, so good, but that alert is pretty static, almost useless, so, let's add the ability to at least set the content and title:

```php
<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

final class AlertComponent extends View
{
    use Html;
    
    protected $title;
    
    protected $body;
    
    protected function render()
    { ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"><?=$this->title?></h4>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?=$this->body?>
        </div>
        <?
    }
}

``` 

At your view:

```php
<?php
namespace Nev\Tests\SampleViews;
use Nev\Tests\SampleViews\AlertComponent;

class ChildView extends ParentView
{
    // Some content skipped...
    
    protected function body() { ?>
 
        <!-- Let's display the alert. -->
        <?AlertComponent::show(['title' => 'Notice', 'body' => 'This is a notice!'])?>
        
        <h1>Hello, user Nº <?=$this->model->id?></h1>
        <p>Lots of content!.</p>
        <? 
    }
}
```

Ok, that's better, but, what if I wanted to add something more complex? Well, let me introduce you the `renderPart` method:

### Render Parts

Sometimes your component needs to receive complex markup as a property, the `renderPart` method allows you to render a property by handling different scenarios. Let's see an example:

 ```php
<?php
 
 namespace Nev\Tests\SampleViews;
 
 use Nev\Html;
 use Nev\View;
 
 final class AlertComponent extends View
 {
     use Html;
     
     protected $title;
     
     protected $body;
     
     protected function render()
     { ?>
         <div class="alert alert-info alert-dismissible fade show" role="alert">
             <h4 class="alert-heading"><?$this->renderPart($this->title)?></h4>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <?$this->renderPart($this->title)?>
         </div>
         <?
     }
 }
 
 ``` 
 
 Notice that the body and title are now rendered using the `renderPart` method, this allows us to do things like this:
 
 ```php
<?php
 namespace Nev\Tests\SampleViews;
 use Nev\Tests\SampleViews\AlertComponent;
 
 class ChildView extends ParentView
 {
     // Some content skipped...
     
     protected function body() { ?>
  
         <!-- Let's display the alert. -->
         <?AlertComponent::show([
             'title' => 'Notice', 
             'body' => function(){?>
               <p>This is a paragraph!</p>
                <blockquote>The sky is the limit!</blockquote>
             <?}])?>
         
         <h1>Hello, user Nº <?=$this->model->id?></h1>
         <p>Lots of content!.</p>
         <? 
     }
 }
```
 
 Notice that you can still send a string as value, but also a function, or a component instance!
 
```php
<?php

// Sending a string:
AlertComponent::show([
  'body' => 'This string will be echoed!'
]);

// Sending a function:
AlertComponent::show([
  'body' => function(){?>
    <p>This function will be called upon component rendering</p>
     <blockquote>The sky is the limit!</blockquote>
<?}]);

// Sending a view Instance:
AlertComponent::show([
  'body' => new SomeOtherComponent([/*...*/]) ]);

```

The `renderPart` method will take care of checking the type of value for your component and do the right thing depending on the value type.

### Dealing with CSS classes

Configure CSS classes could be a bit cumbersome. Fortunately, the Html trait provides a comfortable way to add them dynamically.

Following with our example, lets first change the way we add the classes:

```php
<?php
 
 namespace Nev\Tests\SampleViews;
 
 use Nev\Html;
 use Nev\View;
 
 final class AlertComponent extends View
 {
     use Html;
     
     // Some content omitted...
     
     protected function render()
     { ?>
         <div class="<?=$this->classes("alert", "alert-info", "alert-dismissible", "fade", "show")?>" role="alert">
             <h4 class="alert-heading"><?$this->renderPart($this->title)?></h4>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <?$this->renderPart($this->title)?>
         </div>
         <?
     }
 }
 
``` 

As you can see, all I do is to call `$this->classes(...)` method and send each class as an individual string. Not much of an improvement, but bear with me, things will get better.

Now, let's add a status attribute:

```php
<?php
 
 namespace Nev\Tests\SampleViews;
 
 use Nev\Html;
 use Nev\View;
 
 final class AlertComponent extends View
 {
     use Html;
     
     // Some content omitted...
     protected $status = 'info';
     
     protected function render()
     { ?>
         <div class="<?=$this->classes("alert", "alert-{$this->status}", "alert-dismissible", "fade", "show")?>" role="alert">
             <h4 class="alert-heading"><?$this->renderPart($this->title)?></h4>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <?$this->renderPart($this->title)?>
         </div>
         <?
     }
 }
 
``` 

Ok, now we can set the alert's status to any of the bootstrap's supported options (info, warning, danger...). But that still not sell the need for that method, so, what about adding the ability to decide if the component is dismissible:

```php
<?php
 
 namespace Nev\Tests\SampleViews;
 
 use Nev\Html;
 use Nev\View;
 
 final class AlertComponent extends View
 {
     use Html;
     
     // Some content omitted...
     protected $status = 'info';
     
     protected $dismissible = true;
     
     protected function render()
     { ?>
         <div class="<?=$this->classes(
                  "alert",
                  "alert-{$this->status}", 
                  // Look, a conditional class!
                  [ "alert-dismissible fade show" => $this->dismissible ]
              )?>" 
              role="alert">
             <h4 class="alert-heading"><?$this->renderPart($this->title)?></h4>
             
             // Only show this button if dismissible.
             <?if($this->dismissible):?>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
             <?endif;?>

             <?$this->renderPart($this->title)?>
         </div>
         <?
     }
 }
 
``` 

Pay attention to the `[ "alert-dismissible fade show" => $this->dismissible ]` array parameter, this is a special case supported by the `->class()` method, it basically adds the key as a class if the value evaluates to true. The array can have as many key value pairs as you like.

Now, to finish this part, let's give the component user the capability to add his own classes:

```php
<?php
 
 namespace Nev\Tests\SampleViews;
 
 use Nev\Html;
 use Nev\View;
 
 final class AlertComponent extends View
 {
     use Html;
     
     // Some content omitted...
     protected $status = 'info';
     
     protected $dismissible = true;
     
    /**
    * @var array|string 
    */
     protected $className = [];
     
     protected function render()
     { ?>
         <div class="<?=$this->classes(
                $this->className,    
                "alert",
                "alert-{$this->status}", 
                // Look, a conditional class!
                [ "alert-dismissible fade show" => $this->dismissible ]
              )?>" 
              role="alert">
             <h4 class="alert-heading"><?$this->renderPart($this->title)?></h4>
             
             // Only show this button if dismissible.
             <?if($this->dismissible):?>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
             <?endif;?>

             <?$this->renderPart($this->title)?>
         </div>
         <?
     }
 }
 
```

This will allow the component user to add his own classes, see sample usages:

```php
<?php

AlertComponent::show([
    'dismissible' => false,
    'status' => 'warning',
    'className' => 'my-custom-class',
    'body' => 'This string will be echoed!'
]);

// Or provide an array for more fun!
AlertComponent::show([
    'dismissible' => false,
    'status' => 'warning',
    'className' => [
        // Numerical index are just appended.
        'my-custom-class',
        'my-other-custom-class',
        // String keys are appended if the value evaluates to true.
        'this-class-will-be-added' => $someTruthyValue,
        'this-class-will-be-ignored' => $someFalsyValue,
    ],
    'body' => 'This string will be echoed!'
]);

```

### Extra properties

Sometimes you want to allow the user to add custom HTML attributes to your component. You could, of course, add an `attrs` attribute to the class. But here's an alternative:

```php
<?php
 
 namespace Nev\Tests\SampleViews;
 
 use Nev\Html;
 use Nev\View;
 
 final class AlertComponent extends View
 {
    use Html;
     
    protected $title;
      
    protected $body;
      
    protected $status = 'info';
    
    protected $dismissible = true;
     
    /**
    * @var array|string 
    */
     protected $className = [];
     
     protected function render()
     { 
         // Get the non-declared attributes received at the constructor as a key/value array.
         $attrs = $this->extraProperties();
         ?>
         <!-- Render the attributes. -->
         <div <?=$this->attrs($attrs)?> 
            class="<?=$this->classes(
                $this->className,    
                "alert",
                "alert-{$this->status}", 
                // Look, a conditional class!
                [ "alert-dismissible fade show" => $this->dismissible ]
              )?>" 
              role="alert">
             <h4 class="alert-heading"><?$this->renderPart($this->title)?></h4>
             
             // Only show this button if dismissible.
             <?if($this->dismissible):?>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
             <?endif;?>

             <?$this->renderPart($this->title)?>
         </div>
         <?
     }
 }
```

The `extraProperties()` method will return all the attributes sent to the constructor that are not declared in the component class. That will allow to do things like this:

```php
<?php

AlertComponent::show([
    // These will be rendered as attributes.
    'id' => 'my-id',
    'data-some-custom-attr' => "Some cool value",
    'title' => "Some cool title for this element!",
    
    // Since this attribute is declared in the class, extraProperties() won't return it.
    'body' => 'This string will be echoed!',
]);
```

### Finishing things with Style

Finally, to complete the personalization options, there is a helper method that allows you to render a key/value pair array as style-valid CSS string.

```php
<?php

AlertComponent::show([
    // These will be rendered as attributes.
    'id' => 'my-id',
    'data-some-custom-attr' => "Some cool value",
    'title' => "Some cool title for this element!",
    'style' => $this->style([ 'float' => 'right' ]),
    
    // Since this attribute is declared in the class, extraProperties() won't return it.
    'body' => 'This string will be echoed!',
]);
```