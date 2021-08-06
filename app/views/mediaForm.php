<?php 

   $this->layout('layout', ['title' => $this->e($title)]);

?>

<body>
   <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-primary-gradient">
      <a class="navbar-brand d-flex align-items-center fw-500" href="https://university.marlindev.ru/pl/teach/control/lesson/view?id=170753894"><img alt="logo" class="d-inline-block align-top mr-2" src="img/logo.png"> Учебный проект</a> <button aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler" data-target="#navbarColor02" data-toggle="collapse" type="button"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarColor02">
         <ul class="navbar-nav mr-auto">
            <li class="nav-item">
               <a class="nav-link" href="/main">Главная <span class="sr-only">(current)</span></a>
            </li>
         </ul>
         <ul class="navbar-nav ml-auto">
            <li class="nav-item">
               <a class="nav-link" href="/login_form">Войти</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" href="/logout">Выйти</a>
            </li>
         </ul>
      </div>
   </nav>
   <main id="js-page-content" role="main" class="page-content mt-3">
      <div class="subheader">
         <h1 class="subheader-title">
            <i class='subheader-icon fal fa-image'></i> Загрузить аватар
         </h1>
      </div>
      <form action="/mediator?id=<?=$_GET['id'];?>" method="POST" enctype="multipart/form-data">
         <div class="row">
            <div class="col-xl-6">
               <div id="panel-1" class="panel">
                  <div class="panel-container">
                     <div class="panel-hdr">
                        <h2>Текущий аватар</h2>
                     </div>
                     <div class="panel-content">
                        <div class="form-group">

                           <?php if(empty($user['avatar'])): ?>                      
                              <img src="../../public/images/jovanni.png" class="img-responsive" width="200">         
                           <? else: ?>
                              <img src="../../public/images/<?=$user['avatar'];?>" class="rounded-circle img-responsive" style="height: 13rem; width: 13rem">
                           <? endif;?>

                        </div>
                        <div class="form-group">
                           <label class="form-label" for="example-fileinput">Выберите аватар</label>
                           <input type="file" name="avatar" id="example-fileinput" class="form-control-file">
                        </div>
                        <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                           <button type="submit" class="btn btn-warning">Загрузить</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </main>

   <script src="js/vendors.bundle.js"></script>
   <script src="js/app.bundle.js"></script>
   <script>

      $(document).ready(function()
      {

         $('input[type=radio][name=contactview]').change(function()
            {
               if (this.value == 'grid')
               {
                  $('#js-contacts .card').removeClassPrefix('mb-').addClass('mb-g');
                  $('#js-contacts .col-xl-12').removeClassPrefix('col-xl-').addClass('col-xl-4');
                  $('#js-contacts .js-expand-btn').addClass('d-none');
                  $('#js-contacts .card-body + .card-body').addClass('show');

               }
               else if (this.value == 'table')
               {
                  $('#js-contacts .card').removeClassPrefix('mb-').addClass('mb-1');
                  $('#js-contacts .col-xl-4').removeClassPrefix('col-xl-').addClass('col-xl-12');
                  $('#js-contacts .js-expand-btn').removeClass('d-none');
                  $('#js-contacts .card-body + .card-body').removeClass('show');
               }

               });

            //initialize filter
            initApp.listFilter($('#js-contacts'), $('#js-filter-contacts'));
      });

   </script>
</body>