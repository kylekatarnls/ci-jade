# ci-jade
ci-jade is a library for CodeIgniter to enable [Jade Template Engine](http://jade-lang.com/) to render
views with *.jade* extension.

- ci-jade is dependencies-free, so you have just to unzip and put the **Jade.php**
file and **Jade** folder into **application/libraries**
- but if you use [composer](http://getcomposer.org) you can also
add ```"kylekatarnls/jade-php": "~1.1"``` to your **composer.json** to keep
the Jade renderer engine up to date. The composer dependency will be used
instead of the embedded engine.
- all files in the view folder ending with .jade can be rendered from the
controllers but you can still use any other files as views. So you're free
to use Jade for all your views or just some of them.
- use the ```'cache' => true``` setting to render your views only once
and save rendered files the *cache* folder, then serve cached file with
no performance loss, views will be loaded as fast as the equivalent
php views. You can also use ```'cache' => '/your/cutom/path```
- ci-jade wait for you to load it. Until you call
```$this->load->library('jade')``` in your controller, no file from
the library or the template engine will be loaded, so performances
remains exactly the same for your other pages.

If you use PHP 5.4 and composer, consider using the
[package version](https://github.com/ci-jade/ci-jade) of ci-jade,
the easiest way to install it, keep it up to date and to use it
in your controllers.

## Easy installation

- Download [Jade library](https://github.com/kylekatarnls/ci-jade/archive/master.zip)
- Extract its content in the **application/libraries** folder in your
CodeIgniter project.

## Installation with Composer

Open a terminal in your CodeIgniter project

```bash
cd application
composer require kylekatarnls/jade-php
cd libraries
wget https://raw.githubusercontent.com/kylekatarnls/ci-jade/master/Jade.php
```

If **wget** is not available on your OS, download [Jade.php](https://raw.githubusercontent.com/kylekatarnls/ci-jade/master/Jade.php)
in your project **application/libraries** folder.

Be sure ```$config['composer_autoload'] = TRUE;``` in your
**application/config/config.php** file

## How to use it?

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  public function index()
  {
    $this->load->library('jade');
    $this->jade->view('myview');
  }
}

```

#### application/views/myview.jade
```jade
doctype html
html(lang='en')
  head
    title My Jade View
  body
    h1 Hello World!
```

## Pass variables

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  public function index()
  {
    $this->load->library('jade');
    $this->load->vars([
      'title' => 'My Jade View',
      'authors' => [
        'Luke',
        'Leia',
        'Lando'
      ]
    ]);
    $this->jade->view('myview');
  }
}

```

#### application/views/myview.jade
```jade
doctype html
html(lang='en')
  head
    title=title
  body
    ul
      each author in authors
        li=author
```

## Keep rendered views in cache

We recommend you to do it in production to serve the views faster.

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  public function index()
  {
    $this->load->library('jade', [
      'cache' => TRUE
    ]);
    $this->jade->view('myview');
  }
}

```

Or you can use a custom storage folder:
```php
$this->load->library('jade', [
  'cache' => '/tmp/my-cache-folder'
]);
```

If the folder does not exists, the library will try to create it.

## Return the view instead of displaying it

```php
$content = $this->jade->view('foo', TRUE);
echo str_replace('<hr>', '----------', $content);
// works also with vars
$content = $this->jade->view('foo', [
  'var1' => 1,
  'var2' => 2
], TRUE);
// and also with view auto-selection
$content = $this->jade->view(TRUE);
// see view auto-selection section below
```

## Controller settings

This feature requires PHP 5.6 and allow you to specify settings for the whole controller.

```php
class Welcome extends CI_Controller {
  
  const SETTINGS = [
    'cache' => true
    // here, you can add any option
  ];
  
  public foo() {
    $this->load->library('jade');
    $this->jade->view('welcome/foo'); // will use the SETTINGS class constant
  }
  
  public bar() {
    $this->load->library('jade', array(
      'cache' => false // will override the SETTINGS class constant
    ));
    $this->jade->view('welcome/bar');
  }
}
```

Tip: you can create and abstract controller with SETTINGS constant,
then extend this abstract class from several controllers.

## View auto-selection

If you do not specify the view file, the most logic one with the given
class and method will be taken:

```php
class Foo extends CI_Controller {
  public function index() {
    $this->load->library('jade');
    $this->jade->view(); // load application/views/foo/index.jade
    // or application/views/foo.jade if it does not exists
  }
  public function bar() {
    $this->load->library('jade');
    $this->jade->view(); // load application/views/foo/bar.jade
    // or application/views/foo/bar/index.jade if it does not exists
  }
}
class Yep extends CI_Controller {
  public function index() {
    $this->load->library('jade');
    $this->jade->view([
      'some' => 'var'
    ]); // load application/views/yep/index.jade
    // or application/views/yep.jade if it does not exists
  }
  public function nop() {
    $this->load->library('jade');
    $content = $this->jade->view(TRUE); // load application/views/yep/nop.jade
    // or application/views/yep/nop/index.jade if it does not exists
  }
  public function dontKnow() {
    $this->load->library('jade');
    $this->jade->view('yep/nop'); // load application/views/yep/nop.jade
    // or application/views/yep/nop/index.jade if it does not exists
  }
}
```

## Custom views folder

If you do no store your *.jade* files in **application/views**,
use the ```view_path``` setting:
```php
$this->load->library('jade', [
  'view_path' => APPPATH . 'jade-templates'
]);
```
