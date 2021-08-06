<?php

namespace App\Controllers;

use App\model\QueryBuilder;
use League\Plates\Engine;
use Delight\Auth\Auth;

class Users{

   private $templates;
   private $auth;
   private $db;

   public function __construct(QueryBuilder $db, Engine $engine, Auth $auth)
   {
      $this->db = $db;
      $this->templates = $engine;
      $this->auth = $auth;
   }

   public function index(){

      if($this->auth->isLoggedIn() || $this->auth->isRemembered()){

         echo $this->templates->render('main', [
            'title' => 'Список пользователей', 
            'id' => $this->auth->getUserId(),
            'admin' => $this->auth->hasRole(\Delight\Auth\Role::ADMIN),
            'users' => $this->db->getAll('users')
         ]);
        
      }
      else{
         flash()->error('Please log in!');
         header('Location: /login_form');
      }

   }

   public function createForm(){

      if($this->auth->isLoggedIn() || $this->auth->isRemembered()){

         if($this->auth->hasRole(\Delight\Auth\Role::ADMIN)){
            echo $this->templates->render('createForm', ['title' => 'Создать пользователя']);
         }
         else{
            flash()->error('This function is only available to the admin!');
            header('Location: /main');
         }

      }
      else{
         flash()->error('Please log in!');
         header('Location: /login_form');
      }
      
   }

   public function create(){
      
      $data = [
         'email' => trim($_POST['email']),
         'state' => trim($_POST['state']),
         'workplace' => trim($_POST['workplace']),
         'phone' => trim($_POST['phone']),
         'address' => trim($_POST['address']),
         'avatar' => $this->db->getAvatar(),
         'vkontakte' => trim($_POST['vkontakte']),
         'telegram' => trim($_POST['telegram']),
         'instagram' => trim($_POST['instagram'])
      ];
   
      if($this->db->checkEmail($data['email'], 'users')){
   
         flash()->error('Email already exists!');
         header('Location: /create_form');
   
      }else{
   
         $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username']);
         $this->db->update($data, $userId, 'users');

         flash()->success('User successfully created!');
         header('Location: /main');
   
      }
         
   }

   public function editForm(){

      if($this->auth->isLoggedIn() || $this->auth->isRemembered()){

         if($this->auth->hasRole(\Delight\Auth\Role::ADMIN) || $this->auth->getUserId() == $_GET['id']){
            echo $this->templates->render('editForm', ['title' => 'Редактировать пользователя', 'user' => $this->db->getOne($_GET['id'], 'users')]);
         }
         else{
            flash()->error('You do not have permission to edit this profile!');
            header('Location: /main');
         }

      }
      else{
         flash()->error('Please log in!');
         header('Location: /login_form');
      }

   }

   public function edit(){

      $data = [
         'username' => trim($_POST['username']),
         'workplace' => trim($_POST['workplace']),
         'phone' => trim($_POST['phone']),
         'address' => trim($_POST['address']), 
      ];

      $this->db->update($data, $_GET['id'], 'users');

      flash()->success('User profile successfully updated!');
      header('Location: /profile?id=' . $_GET['id']);

   }

   public function profile(){

      if($this->auth->isLoggedIn() || $this->auth->isRemembered()){

         echo $this->templates->render('profile', ['title' => 'Профиль пользователя', 'user' => $this->db->getOne($_GET['id'], 'users')]);

      }
      else{
         flash()->error('Please log in!');
         header('Location: /login_form');
      }

   }

   public function securityForm(){

      if($this->auth->isLoggedIn() || $this->auth->isRemembered()){

         if($this->auth->hasRole(\Delight\Auth\Role::ADMIN) || $this->auth->getUserId() == $_GET['id']){
            echo $this->templates->render('securityForm', ['title' => 'Редактировать данные пользователя', 'user' => $this->db->getOne($_GET['id'], 'users')]);
         }
         else{
            flash()->error('You do not have permission to edit this profile!');
            header('Location: /main');
            exit;
         }

      }
      else{
         flash()->error('Please log in!');
         header('Location: /login_form');
      }

   }

   public function security(){

      $data = [
         'email' => trim($_POST['email']),
         'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
      ];

      if($this->auth->getEmail() == $_POST['email']){

         $this->db->update($data, $_GET['id'], 'users');

         flash()->success('User profile successfully updated!');
         header('Location: /profile?id=' . $_GET['id']);

      }
      elseif($this->db->checkEmail($data['email'], 'users')){
   
         flash()->error('Email already exists!');
         header('Location: /security_form?id=' . $_GET['id']);
   
      }
      elseif(empty($_POST['password'])){

         flash()->error('The password field must be filled in!');
         header('Location: /security_form?id=' . $_GET['id']);

      }
      else{
   
         $this->db->update($data, $_GET['id'], 'users');

         flash()->success('User profile successfully updated!');
         header('Location: /profile?id=' . $_GET['id']);
   
      }

   }

   public function statusForm(){

      if($this->auth->isLoggedIn() || $this->auth->isRemembered()){

         if($this->auth->hasRole(\Delight\Auth\Role::ADMIN) || $this->auth->getUserId() == $_GET['id']){
            echo $this->templates->render('statusForm', [
               'title' => 'Редактировать статус пользователя', 
               'user' => $this->db->getOne($_GET['id'], 'users'), 
               'statuses' => ['Онлайн', 'Отошел', 'Не беспокоить']
            ]);
         }
         else{
            flash()->error('You do not have permission to edit this profile!');
            header('Location: /main');
            exit;
         }

      }
      else{
         flash()->error('Please log in!');
         header('Location: /login_form');
      }

   }

   public function status(){

      $data = ['state' => $_POST['state']];

      $this->db->update($data, $_GET['id'], 'users');

      flash()->success('User status successfully updated!');
      header('Location: /profile?id=' . $_GET['id']);

   }

   public function mediaForm(){

      if($this->auth->isLoggedIn() || $this->auth->isRemembered()){

         if($this->auth->hasRole(\Delight\Auth\Role::ADMIN) || $this->auth->getUserId() == $_GET['id']){
            echo $this->templates->render('mediaForm', [
               'title' => 'Редактировать аватар пользователя', 
               'user' => $this->db->getOne($_GET['id'], 'users'),
            ]);
         }
         else{
            flash()->error('You do not have permission to edit this profile!');
            header('Location: /main');
            exit;
         }

      }
      else{
         flash()->error('Please log in!');
         header('Location: /login_form');
      }

   }

   public function mediator(){

      $user = $this->db->getOne($_GET['id'], 'users');
      unlink('images/' . $user['avatar']);

      $this->db->update(['avatar' => $this->db->getAvatar()], $_GET['id'], 'users');

      flash()->success('User avatar successfully updated!');
      header('Location: /profile?id=' . $_GET['id']);

   }

   public function delete(){

      if($this->auth->isLoggedIn() || $this->auth->isRemembered()){

         if($this->auth->hasRole(\Delight\Auth\Role::ADMIN) || $this->auth->getUserId() == $_GET['id']){

            $user = $this->db->getOne($_GET['id'], 'users');
            unlink('images/' . $user['avatar']);
            $this->db->delete($_GET['id'], 'users');  

            if($this->auth->hasRole(\Delight\Auth\Role::ADMIN)){
               flash()->success('User successfully deleted!');
               header('Location: /main');
               exit;
            }else{
               $this->auth->logOut();
               flash()->success('User successfully deleted!');
               header('Location: /register_form');
               exit;
            }
            
         }
         else{
            flash()->error('You do not have permission to edit this profile!');
            header('Location: /main');
            exit;
         }

      }
      else{
         flash()->error('Please log in!');
         header('Location: /login_form');
      }

   }

}


