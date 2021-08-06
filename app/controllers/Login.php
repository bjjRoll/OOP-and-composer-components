<?php

namespace App\Controllers;

use League\Plates\Engine;
use Delight\Auth\Auth;

class Login{

   private $templates;
   private $auth;

   public function __construct(Engine $engine, Auth $auth)
   {
      $this->templates = $engine;
      $this->auth = $auth;
   }


   public function login(){

      if ($_POST['remember'] == 'on') {
         $rememberDuration = (int) (60 * 60 * 24 * 365.25);
      }
      else {
         $rememberDuration = null;
      }

      try {
        
         $this->auth->login($_POST['email'], $_POST['password'], $rememberDuration);
         flash()->success('User is logged in!');
         header('Location: /main');
         exit();

      }
      catch (\Delight\Auth\InvalidEmailException $e) {
         flash()->error('Wrong email address');
      }
      catch (\Delight\Auth\InvalidPasswordException $e) {
         flash()->error('Wrong password');
      }
      catch (\Delight\Auth\EmailNotVerifiedException $e) {
         flash()->error('Email not verified');
      }
      catch (\Delight\Auth\TooManyRequestsException $e) {
         flash()->error('Too many requests');
      }

      header('Location: /login_form');

   }

   public function logout(){

      $this->auth->logOut();
      flash()->success('Logout carried out!');
      header('Location: /login_form');

   }

   public function loginForm(){

      echo $this->templates->render('loginForm', ['title' => 'Авторизация']);

   }

}