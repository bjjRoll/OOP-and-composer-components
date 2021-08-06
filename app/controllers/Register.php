<?php

namespace App\Controllers;

use League\Plates\Engine;
use Delight\Auth\Auth;
use SimpleMail;

class Register{

   private $templates;
   private $auth;

   public function __construct(Engine $engine, Auth $auth)
   {
      $this->templates = $engine;
      $this->auth = $auth;
   }

   public function register(){

      try {

         $this->auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {

            SimpleMail::make()
               ->setTo($_POST['email'], $_POST['username'])
               ->setFrom('klimrzd@gmail.com', 'Admin')
               ->setSubject('Verify your email')
               ->setMessage('<a href="http://mysite/verification?selector=' . $selector . '&token=' . $token . '">Congratulations! You have successfully completed your registration. Follow this link to verify your email address</a>')
               ->setHtml()
               ->send();
         });

         flash()->success('New user successfully created! A verification email has been sent to your email address');
         header('Location: /login_form');
         exit();

      }
      catch (\Delight\Auth\InvalidEmailException $e) {
         flash()->error('Invalid email address');               
      }
      catch (\Delight\Auth\InvalidPasswordException $e) {
         flash()->error('Invalid password');        
      }
      catch (\Delight\Auth\UserAlreadyExistsException $e) {
         flash()->error('User already exists');      
      }
      catch (\Delight\Auth\TooManyRequestsException $e) {
         flash()->error('Too many requests');
      }

      header('Location: /register_form');

   }

   public function emailVerification(){

      try {

         $this->auth->confirmEmail($_GET['selector'], $_GET['token']);

         flash()->success('Email address has been verified. Please log in!');

      }
      catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
         flash()->error('Invalid token');        
      }
      catch (\Delight\Auth\TokenExpiredException $e) {
         flash()->error('Token expired');         
      }
      catch (\Delight\Auth\UserAlreadyExistsException $e) {
         flash()->error('Email address already exists');         
      }
      catch (\Delight\Auth\TooManyRequestsException $e) {
         flash()->error('Too many requests');
      }

      header('Location: /login_form');

   }

   public function registerForm(){

      echo $this->templates->render('registerForm', ['title' => 'Регистрация']);

   }

}