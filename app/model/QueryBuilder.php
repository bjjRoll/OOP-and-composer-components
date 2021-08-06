<?php

namespace App\model;
use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder
{
   private $pdo;
   private $queryFactory;

   public function __construct(PDO $pdo, QueryFactory $queryFactory)
   {
      $this->pdo = $pdo;
      $this->queryFactory = $queryFactory;
   }

   public function getAll($table){

      $select = $this->queryFactory->newSelect();
      $select
         ->cols(['*'])
         ->from($table);

      $sth = $this->pdo->prepare($select->getStatement());
      $sth->execute($select->getBindValues());
      return $sth->fetchAll(PDO::FETCH_ASSOC);

   }

   public function getOne($id, $table){

      $select = $this->queryFactory->newSelect();
      $select
         ->cols(['*'])
         ->from($table)
         ->where('id = :id')
         ->bindValue('id', $id);

      $sth = $this->pdo->prepare($select->getStatement());
      $sth->execute($select->getBindValues());
      return $sth->fetch(PDO::FETCH_ASSOC);

   }

   public function checkEmail($email, $table){

      $select = $this->queryFactory->newSelect();
      $select
         ->cols(['*'])
         ->from($table)
         ->where('email = :email')
         ->bindValue('email', $email);

      $sth = $this->pdo->prepare($select->getStatement());
      $sth->execute($select->getBindValues());
      return $sth->fetch();

   }

   public function insert($data, $table){

      $insert = $this->queryFactory->newInsert();

      $insert
         ->into($table)                   
         ->cols($data);

      $sth = $this->pdo->prepare($insert->getStatement());
      $sth->execute($insert->getBindValues());

   }

   public function getAvatar(){

      $fileName = $_FILES['avatar']['name'];
      $ext = pathinfo($fileName, PATHINFO_EXTENSION);
      $fileName = uniqid('img_') . '.' . $ext;
      $saveTo = 'images/' . $fileName;

      move_uploaded_file($_FILES['avatar']['tmp_name'], $saveTo);

      return $fileName;

   }
   
   public function update($data, $id, $table){

      $update = $this->queryFactory->newUpdate();

      $update
         ->table($table)                
         ->cols($data)
         ->where('id = :id')
         ->bindValue('id', $id);
      
      $sth = $this->pdo->prepare($update->getStatement());

      $sth->execute($update->getBindValues());

   }

   public function delete($id, $table){

      $delete = $this->queryFactory->newDelete();

      $delete
         ->from($table)                   
         ->where('id = :id')          
         ->bindValue('id', $id);  

      $sth = $this->pdo->prepare($delete->getStatement());

      $sth->execute($delete->getBindValues());

   }

}

