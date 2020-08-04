<?php
require_once("./vendor/autoload.php");
use Ramsey\Uuid\Uuid;
use LucidFrame\Console\ConsoleTable;

class Router {
  private $route;
  private $history;

  function __construct() {
    $this->route = array();
    $this->history = [];
  }

  function register($path, $ctrl) {
    $this->route[$path] = $ctrl;
  }

  function navigateTo($path, $data = null) {
    system('clear');
    array_push($this->history, [
      'path' => $path,
      'data' => $data
    ]);
    call_user_func($this->route[$path], $this, $data);
  }
}

function wait() {
  echo "Press any key to continue";
  fgets(STDIN);
}

$db = [
  "books" => []
];

$home = function($router) {
  echo <<<END
  1. Show books
  2. Create a book
  Your choice: 
  END;
  $choice = rtrim(fgets(STDIN));
  switch ($choice) {
    case '1':
      $router->navigateTo('/books');
      break;
    case '2':
      $router->navigateTo('/books/create');
      break;
    default:
      echo "Bad choice\n";
      wait();
      $router->navigateTo('/');
  }
};

$showBooks = function($router) {
  global $db;
  $books = $db['books'];
  if (is_array($books) || is_object($books)) {
    if (count($books) == 0) {
      echo "No books";
      return;
    }
    $table = new ConsoleTable();
    $table->setHeaders(['id', 'title']);
    foreach ($books as $book) {
      $table->addRow(array_values($book));
    }
    $table->display();
    wait();
    $router->navigateTo('/');
  } else {
    echo "\$books is not an array";
  }
};

$createBook = function($router) {
  global $db;
  $id = Uuid::uuid4();
  echo <<<END
  Create book
  Book title: 
  END;
  $title = fgets(STDIN);
  array_push($db['books'], [
    'id' => $id->toString(),
    'title' => $title
  ]);
  $router->navigateTo('/books');
};

function main() {
  global $home, $showBooks, $createBook;
  $router = new Router();
  $router->register('/', $home);
  $router->register('/books', $showBooks);
  $router->register('/books/create', $createBook);
  $router->navigateTo('/');
}

main();