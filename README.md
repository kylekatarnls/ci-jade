# ci-pug
ci-pug is a library for CodeIgniter to enable [Pug (renamed from Jade) Template Engine](http://jade-lang.com/) to render
views with *.pug* or *.jade* extension.

- ci-pug is dependencies-free, so you have just to unzip and put the **Pug.php** and **Jade.php**
files and **Jade** folder into **application/libraries**
- but if you use [composer](http://getcomposer.org) you can also
add ```"pug-php/pug": "~1.11"``` to your **composer.json** to keep
the Pug renderer engine up to date. The composer dependency will be used
instead of the embedded engine.
- all files in the view folder ending with .pug or .jade can be rendered from the
controllers but you can still use any other files as views. So you're free
to use Pug for all your views or just some of them.
- use the ```'cache' => true``` setting to render your views only once
and save rendered files the *cache* folder, then serve cached file with
no performance loss, views will be loaded as fast as the equivalent
php views. You can also use ```'cache' => '/your/cutom/path```
- ci-pug wait for you to load it. Until you call
```$this->load->library('pug')``` in your controller, no file from
the library or the template engine will be loaded, so performances
remains exactly the same for your other pages.

If you use PHP 5.4 and composer, consider using the
[package version](https://github.com/pug-php/ci-pug-engine) of ci-pug,
the easiest way to install it, keep it up to date and to use it
in your controllers.

## Easy installation

- Download [Pug library](https://github.com/pug-php/ci-pug/archive/master.zip)
- Extract its content in the **application/libraries** folder in your
CodeIgniter project.

## Installation with Composer

Open a terminal in your CodeIgniter project

```bash
cd application
composer require pug-php/ci-pug
cd libraries
wget https://raw.githubusercontent.com/pug-php/ci-pug/master/{Jade,Pug}.php
```

If **wget** is not available on your OS, download [Jade.php](https://raw.githubusercontent.com/pug-php/ci-pug/master/Jade.php)
and [Pug.php](https://raw.githubusercontent.com/pug-php/ci-pug/master/Pug.php)
in your project **application/libraries** folder.

Be sure ```$config['composer_autoload'] = true;``` in your
**application/config/config.php** file

## How to use it?

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  public function index()
  {
    $this->load->library('pug');
    $this->pug->view('myview');
  }
}

```

#### application/views/myview.pug
```pug
doctype html
html(lang='en')
  head
    title My Pug View
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
    $this->load->library('pug');
    $this->load->vars(array(
      'title' => 'My Pug View',
      'authors' => array(
        'Luke',
        'Leia',
        'Lando'
      )
    ));
    $this->pug->view('myview');
  }
}

```

#### application/views/myview.pug
```pug
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
    $this->load->library('pug', array(
      'cache' => true
    ));
    $this->pug->view('myview');
  }
}

```

Or you can use a custom storage folder:
```php
$this->load->library('pug', array(
  'cache' => '/tmp/my-cache-folder'
));
```

If the folder does not exists, the library will try to create it.

## Return the view instead of displaying it

```php
$content = $this->pug->view('foo', true);
echo str_replace('<hr>', '----------', $content);
// works also with vars
$content = $this->pug->view('foo', array(
  'var1' => 1,
  'var2' => 2
), true);
// and also with view auto-selection
$content = $this->pug->view(true);
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
    $this->load->library('pug');
    $this->pug->view('welcome/foo'); // will use the SETTINGS class constant
  }

  public bar() {
    $this->load->library('pug', [
      'cache' => false // will override the SETTINGS class constant
    ]);
    $this->pug->view('welcome/bar');
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
    $this->load->library('pug');
    $this->pug->view(); // load application/views/foo/index.pug
    // or application/views/foo.pug if it does not exists
  }
  public function bar() {
    $this->load->library('pug');
    $this->pug->view(); // load application/views/foo/bar.pug
    // or application/views/foo/bar/index.pug if it does not exists
  }
}
class Yep extends CI_Controller {
  public function index() {
    $this->load->library('pug');
    $this->pug->view(array(
      'some' => 'var'
    )); // load application/views/yep/index.pug
    // or application/views/yep.pug if it does not exists
  }
  public function nop() {
    $this->load->library('pug');
    $content = $this->pug->view(true); // load application/views/yep/nop.pug
    // or application/views/yep/nop/index.pug if it does not exists
  }
  public function dontKnow() {
    $this->load->library('pug');
    $this->pug->view('yep/nop'); // load application/views/yep/nop.pug
    // or application/views/yep/nop/index.pug if it does not exists
  }
}
```

## Custom views folder

If you do no store your *.pug* or *.jade* files in **application/views**,
use the ```view_path``` setting:
```php
$this->load->library('pug', array(
  'view_path' => APPPATH . 'pug-templates'
));
```
