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

final class MyView extends View {

    protected $name = '';

    protected function render() { ?>
        <!DOCTYPE html>
        <html>
            <head>
                <title>Hello Nev</title>
            </head>
            <body>
                <h1>Hello <?=$this->name?>.</h1>
            </body>
        </html>
        <?
    }
}
```

## Rendering it

```php
<?= MyView::show(['name' => 'World']) ?>
```

## What is all this?

Nev is just the idea that, if you're disciplined enough, you can just use regular PHP classes to represent your views. All you need is a simple base class to derive from which hide most of the uggly stuff.

So, what can I do with this thing? Well, let me walk you through:

First, let's take a look at a simple view:

```php
<?php
namespace MyNamespace\Views;

use Nev\View;

// Just create a regular PHP class and inherit from Nev\View
final class BasicView extends View {
   
    // Optionally, use the Html trait to have a few helper methods
    use \Nev\Html;

    // Implement the abstract render method. (And yes, I stealed the syntax from react).
    protected function render() { ?>
        <!-- As a recommendation, do as much HTML as you can so your view remains clear -->
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <!-- This method from the HTML trait will render a link tag. -->
                <?= $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css') ?>
                <title>Hello Nev</title>
            </head>
            <body>
                <h1>Hello world.</h1>
                <!-- This method from the HTML trait will render as many script tags as arguments provided. -->
                <? $this->js(
                    "https://code.jquery.com/jquery-3.3.1.slim.min.js",
                    "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js",
                    "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
                ) ?>
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

## Compose Views.

Even if you could create a base class and inherit from there, the general agreement is that it is better to compose things (composition over inheritance) since that leads to more flexible and maintainable code.
   
So, as an example, let's create a Page component which receives the page parts as parameters:

```php
<?php
namespace MyNamespace\Views;
use Nev\Html;
use Nev\View;

final class Page extends View {
    use Html;
    
    /**
    * The page's title.
    * 
    * @var string 
    */
    protected $title = '';
    
    /**
    * The page's body. It can be a string, callable or View instance.
    * 
    * @var string|callable|View 
    */
    protected $body = '';
    
    /**
    * The list of script urls.
    * @var string[] 
    */
    protected $scripts = [];
    /**
    * The list of css urls. 
    * @var array 
    */
    protected $cssFiles = [];

    public function render() { ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
            <!-- Render the CSS files -->
            <?= $this->css(
                'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
                // In case you din't know, here we're using the spread operator, this allows to
                // take an array and send its values as parameters, this feature is available since PHP 7.2 
                ...$this->cssFiles
            ) ?>
            <!-- Render the page title.  -->
            <title><?= $this->title ?></title>
        </head>
        <body>

        <!-- Render the Page's body. The draw method will check if the value is a string, a callable 
        or a View instance and act accordinly, more info in a few moments. -->
        <?= self::draw($this->body) ?>

        <!-- Render the javascript files -->
        <?= $this->js(
            "https://code.jquery.com/jquery-3.3.1.slim.min.js",
            "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js",
            "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js",
            ...$this->scripts
        ) ?>

        </body>
        </html>
        <?
    }
}
```

We moved all the boilerplate things to a Page class. Now let's create a specific view:

```php
<?php
namespace MyNamespace\Views;
use Nev\View;

final class HelloWorldView extends View
{
    protected $name;
    
    protected function render() { ?>
        <h1>Hello, <?=$this->name?>!</h1>
        <p>
            It feels good to do things without dealing with boilerplate things.
        </p>
        <? 
    }
}
```

Then we can compose our page like this:

```php
<?= Page::show([
    'title' => 'My first page',
    'body' => new HelloWorldView(['name' => 'World']),
]);
```

**Pro Tip:** Since the `show` method will return a string, you can use it for a response body:

```php
<?php
// Example as you'd see in slim framework and similar:
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $html = Page::show([ 'body' => new HelloWorldView(['name' => $name]), ]);
    $response->getBody()->write($html);
    return $response;
});
```

Or even further, since View implements `__toString`, you could just send your view instance:

```php
<?php
// Example as you'd see in slim framework and similar:
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write(new Page([ 
        'body' => new HelloWorldView(['name' => $name]), 
    ]));
    return $response;
});
```

## Passing data to the view

As you might have noticed from the previous examples, the data you send to the constructor (or the show method) gets mapped into your view as properties, this allows you to have auto-completion support from your IDE, this is specially useful when dealing with complex objects or collections: 

```php
<?php
namespace Nev\Tests\SampleViews;

final class ModelDependentView extends View
{
    /**
     * Your IDE will surely have support for auto-completing this :D
     * 
     * @var SomeViewModel 
     */
    protected $model;

    protected function render() { ?>
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

$renderedResult = ChildView::show([ 'model' => $someViewModel ]);
```

### Boolean Values

Boolean values are a special case, of course you can set them as usual by providing a key with a value in the arguments array, but you can also set a boolean field as `true` by just adding the name as a value with no key.

Let's clear this with an example:

```php
<?php
namespace MyNamespace\Views;
use Nev\View;

final class HelloWorldView extends View
{
    /**
    * Let's have a boolean property declared.
    * @var bool 
    */
    protected $isAdmin = false;
    protected $name;
    
    protected function render() { ?>
        <h1>Hello, <?=$this->name?>!</h1>
        <? if($this->isAdmin): ?>
            <a href="....">Go to some special place reserved for admin guys</a>
        <? endif; ?>

        <p>
            It feels good to do things without dealing with boilerplate things.
        </p>
        <? 
    }
}
```

Now that we have a boolean property in our view, we can send a value in several ways:

```php
<!--We can send the value as a key as usual -->
<?= HelloWorldView::show(['isAdmin' => true, 'name' => 'World']) ?>

<!--Or We can send just the name without a key, which will set the value to true -->
<?= HelloWorldView::show(['isAdmin', 'name' => 'World']) ?>
```

> **Note:** Omitting the value will not set it to false, but set it to the default value declared in the class (usually false).

### Passing extra properties to the view

You can send non-declared values to your views. They will be mapped to properties, just like the others, but, since you haven't declared them, that won't be of much use. 

For that reason there is this method named `extraProperties` that will give you an associative array with the non-declared values which you can use for purposes such as adding extra attributes to some elements:

```php
<?php
namespace MyNamespace\Views;
use Nev\View;
use Nev\Html;

final class Div extends View
{
    use Html;
    
    protected $contents;
    
    protected function render() {
        // Get all the non-declared properties
        $attrs = $this->extraProperties();
        ?>
        <!-- Render the associative array as html attributes (more on attrs method at the 'Crating a Component' section) -->
        <div <?= $this->attrs($attrs) ?> >
            <!-- Draw the contents, (more on draw methods at the 'Crating a Component' section) -->
            <?= self::draw($this->contents) ?>
        </div>
        <? 
    }
}
```


## Creating a Component

Something pretty useful you can do with Nev is creating components that can be used by other views. 

Components have nothing special, they are just regular views with a different purpose.

Let's say we want to create a Bootstrap alert component, to do that, we just create a view:

```php
<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

final class AlertComponent extends View {
    use Html;
    
    protected function render() { ?>
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

final class HelloWorldView extends View
{
    protected function render() { ?>
        <!-- Let's display the alert. -->
        <?=AlertComponent::show()?>
        
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

final class HelloWorldView extends View
{
    protected function render() { ?>
        <!-- Let's display the alert. -->
        <?= AlertComponent::show(['title' => 'Notice', 'body' => 'This is a notice!']) ?>
        
        <p>Lots of content!.</p>
        <? 
    }
}
```

Ok, that's better, but, what if I wanted to add something more complex? Well, let me introduce you the `draw` method:

### The draw method

Sometimes your component needs to receive complex markup as a property, the `draw` static method allows you to render a property by handling different scenarios:

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
             <h4 class="alert-heading"><?=self::draw($this->title)?></h4>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <?=self::draw($this->title)?>
         </div>
         <?
     }
 }
 
 ``` 
 
Notice that the body and title are now rendered using the `draw` method, this allows us to handle scenarios like these:
 
 ```php
<?= AlertComponent::show([
    // Sending a string:
  'body' => 'This string will be echoed!'
]) ?>

<?= AlertComponent::show([
   // Sending a function:
    'body' => function(){ 
        ?>
        <p>This function will be called upon component rendering</p>
        <blockquote>The sky is the limit!</blockquote>
        <?
    },
])?>

<?= AlertComponent::show([
   // Sending a view Instance:
  'body' => new SomeOtherComponent([/*...*/]), 
])?>

```

The `draw` method will take care of checking the type of value for your component and do the right thing depending on the value type:

* **Strings or any scalar value**: The value will be returned so it can be echoed.
* **callable**: It will be invoked and the output will be caught and returned. 
* **View instance**: It will be rendered by calling its `display` method.

### Dealing with CSS classes

Configure CSS classes could be a bit cumbersome. Fortunately, the Html trait provides a comfortable way to add them dynamically.

Following with our example, lets first change the way we add the classes:

```php
<?php
 
 namespace Nev\Tests\SampleViews;
 
 use Nev\Html;
 use Nev\View;
 
 final class AlertComponent extends View {
     use Html;
     
     // Some content omitted...
     
     protected function render() { 
         $classes = $this->classes("alert", "alert-info", "alert-dismissible", "fade", "show");
         ?>
         <div class="<?=$classes?>" role="alert">
             <h4 class="alert-heading"><?= self::draw($this->title)?></h4>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <?= self::draw($this->title)?>
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
     
     protected function render() {
         $classes = $this->classes("alert", "alert-{$this->status}", "alert-dismissible", "fade", "show");
         ?>
         <div class="<?=$classes?>" role="alert">
             <h4 class="alert-heading"><?= self::draw($this->title)?></h4>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <?= self::draw($this->title)?>
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
     
     protected $dismissible = false;
     
     protected function render() {
         $classes = $this->classes(
              "alert",
              "alert-{$this->status}", 
              // Look, a conditional class! These classes will only display if
              // $this->dismissible is true.
              [ "alert-dismissible fade show" => $this->dismissible ]
          );
         ?>
         <div class="<?=$classes?>" role="alert">
             <h4 class="alert-heading"><?= self::draw($this->title)?></h4>
             
             <!-- Only show this button if dismissible. -->
             <?if($this->dismissible):?>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
             <?endif;?>

             <?= self::draw($this->title)?>
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
     
     protected function render() {
         $classes = $this->classes(
            // Add the user provided classes.
            $this->className,    
            "alert",
            "alert-{$this->status}", 
            // Look, a conditional class! These classes will only display if
            // $this->dismissible is true.
            [ "alert-dismissible fade show" => $this->dismissible ]
          );
         ?>
         <div class="<?=$classes?>" role="alert">
             <h4 class="alert-heading"><?= self::draw($this->title)?></h4>
             
             <!-- Only show this button if dismissible. -->
             <?if($this->dismissible):?>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
             <?endif;?>

             <?= self::draw($this->title)?>
         </div>
         <?
     }
 }
 
```

This will allow the component user to add his own classes, see sample usages:

```php
<?= AlertComponent::show([
    'dismissible' => true,
    'status' => 'warning',
    // You can still send a string.
    'className' => 'my-custom-class',
    'body' => 'This string will be echoed!'
]) ?>

<?= AlertComponent::show([
    'dismissible',
    'status' => 'warning',
    // Or provide an array for more fun!
    'className' => [
        // Numerical index are just appended.
        'my-custom-class',
        'my-other-custom-class',
        // String keys are appended if the value evaluates to true.
        'this-class-will-be-added' => $someTruthyValue,
        'this-class-will-be-ignored' => $someFalsyValue,
    ],
    'body' => 'This string will be echoed!'
]) ?>
```

### Conditional drawing

As you can see above, the PHP template `if` statement can be tiresome to write, for those cases you can optionally call the `drawIf` static method instead:

```php
 <!-- Instead of using if, you can do this. -->
 <?= self::drawIf($this->dismissible, function() {?>
     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
      </button>
 <?}) ?> 
 
 <!-- Or send a string -->
 <?= self::drawIf($this->dismissible, "Yay, I'm dismissible!") ?>
 
 <!-- Or send a View instance! -->
  <?= self::drawIf($this->dismissible, new DismissButton()) ?> 
```

The `drawIf` method receives two parameters: a boolean indicating if the render will happen, and a string|callable|View that will be rendered using `draw` method if the first parameter evaluates to true. 

### Extra properties

Sometimes you want to allow the user to add custom HTML attributes to your component. As stated in a previous section, all you need to do is getting the non-declared properties using the `extraProperties` method and render them using the `attr` method:

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
     
     protected function render() {
         // Get the extra attributes as an associative array
         $attributes = $this->extraProperties();
         $classes = $this->classes(
            // Add the user provided classes.
            $this->className,    
            "alert",
            "alert-{$this->status}", 
            // Look, a conditional class! These classes will only display if
            // $this->dismissible is true.
            [ "alert-dismissible fade show" => $this->dismissible ]
          );
         ?>
         <div <?= $this->attrs($attributes) /*<-- Render the attributes */?> class="<?= $classes ?>" role="alert">
             <h4 class="alert-heading"><?= self::draw($this->title)?></h4>
             
             <!-- Only show this button if dismissible. -->
             <?if($this->dismissible):?>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
             <?endif;?>

             <?= self::draw($this->title)?>
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

Note that we need to use the `attrs` method from the `Html` trait in order to display the associative array as a set of HTML attributes.

### Finishing things with Style

Finally, to complete the personalization options, there is a helper method that allows you to render a key/value pair array as a CSS string.

```php
<?php

AlertComponent::show([
    // These will be rendered as attributes.
    'id' => 'my-id',
    'data-some-custom-attr' => "Some cool value",
    'title' => "Some cool title for this element!",
    // The `style` method from `Html` trait will convert an associative array into a CSS string. 
    'style' => $this->style([ 'float' => 'right' ]),
    
    // Since this attribute is declared in the class, extraProperties() won't return it.
    'body' => 'This string will be echoed!',
]);
```