<?php

   if(!session_id()) {
      @session_start();
   }

   require '../vendor/autoload.php';

   use Aura\SqlQuery\QueryFactory;
   use DI\ContainerBuilder;
   use Delight\Auth\Auth;
   use League\Plates\Engine;

   $containerBuilder = new ContainerBuilder;

   $containerBuilder->addDefinitions([

      Engine::class => function(){
         return new Engine('../app/views');
      },

      QueryFactory::class => function(){
         return new QueryFactory('mysql');
      },

      PDO::class => function(){
         $driver = "mysql";
         $host = "localhost";
         $databaseName = "module_2";
         $username = "root";
         $password = "";

         return new PDO("$driver:host=$host;dbname=$databaseName", $username, $password);
      },

      Auth::class => function($container){
         return new Auth($container->get('PDO'));
      },

   ]);

   $container = $containerBuilder->build();

   $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {

      $r->addRoute('POST', '/register', ['App\Controllers\Register', 'register']);
      $r->addRoute('GET', '/register_form', ['App\Controllers\Register', 'registerForm']);

      $r->addRoute('GET', '/verification', ['App\Controllers\Register', 'emailVerification']);

      $r->addRoute('POST', '/login', ['App\Controllers\Login', 'login']);
      $r->addRoute('GET', '/login_form', ['App\Controllers\Login', 'loginForm']);

      $r->addRoute('GET', '/logout', ['App\Controllers\Login', 'logout']);

      $r->addRoute('GET', '/main', ['App\Controllers\Users', 'index']);

      $r->addRoute('GET', '/create_form', ['App\Controllers\Users', 'createForm']);
      $r->addRoute('POST', '/create', ['App\Controllers\Users', 'create']);

      $r->addRoute('GET', '/edit_form', ['App\Controllers\Users', 'editForm']);
      $r->addRoute('POST', '/edit', ['App\Controllers\Users', 'edit']);

      $r->addRoute('GET', '/profile', ['App\Controllers\Users', 'profile']);

      $r->addRoute('GET', '/security_form', ['App\Controllers\Users', 'securityForm']);
      $r->addRoute('POST', '/security', ['App\Controllers\Users', 'security']);

      $r->addRoute('GET', '/status_form', ['App\Controllers\Users', 'statusForm']);
      $r->addRoute('POST', '/status', ['App\Controllers\Users', 'status']);

      $r->addRoute('GET', '/media_form', ['App\Controllers\Users', 'mediaForm']);
      $r->addRoute('POST', '/mediator', ['App\Controllers\Users', 'mediator']);

      $r->addRoute('GET', '/delete', ['App\Controllers\Users', 'delete']);

   });
  
   $httpMethod = $_SERVER['REQUEST_METHOD'];
   $uri = $_SERVER['REQUEST_URI'];
  
   if (false !== $pos = strpos($uri, '?')) {

      $uri = substr($uri, 0, $pos);

   }

   $uri = rawurldecode($uri);
  
   $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
   
   switch ($routeInfo[0]) {

      case FastRoute\Dispatcher::NOT_FOUND:
         echo "Error 404! Not Found";
         break;

      case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
         $allowedMethods = $routeInfo[1];
         echo "Error 405! Method Not Allowed";
         break;

      case FastRoute\Dispatcher::FOUND:
         $handler = $routeInfo[1];
         $vars = $routeInfo[2];
         $container->call($handler, $vars);
         break;

   }








