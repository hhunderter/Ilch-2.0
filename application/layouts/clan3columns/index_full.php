<?php /** @var $this \Ilch\Layout\Frontend */ ?><!DOCTYPE html>
<html lang="de">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <?=$this->getHeader() ?>
        <link href="<?=$this->getVendorUrl('twbs/bootstrap/dist/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?=$this->getLayoutUrl('style.css') ?>" rel="stylesheet">
        <?=$this->getCustomCSS() ?>
        <script src="<?=$this->getVendorUrl('twbs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
    </head>
    <body>
      <header>
        <nav class="navbar navbar-expand-lg navbar-light navbar-gaming bg-light fixed-top d-sm-block d-md-none">
          <div class="container-fluid">
            <a class="navbar-brand" href="<?=$this->getUrl() ?>"><?=$this->getTrans('navigation') ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="<?=$this->getTrans('togglenavigation') ?>">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
              <?php
              echo $this->getMenu(
                  1,
                  '<div class="card card-gaming">
                       <div class="card-header">%s</div>
                          <div class="card-body">
                              %c
                          </div>
                   </div>'
              );
              ?>
            </div>
          </div>
        </nav>

        <div class="container">
            <div class="gaming">
                <div id="carousel-generic" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carousel-generic" data-bs-slide-to="0" class="active" aria-current="true" aria-label="<?=$this->getTrans('slide1') ?>"></button>
                        <button type="button" data-bs-target="#carousel-generic" data-bs-slide-to="1" aria-label="<?=$this->getTrans('slide2') ?>"></button>
                        <button type="button" data-bs-target="#carousel-generic" data-bs-slide-to="2" aria-label="<?=$this->getTrans('slide3') ?>"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="<?=$this->getBaseUrl($this->getLayoutSetting('slider1')) ?>" alt="<?=$this->getTrans('slider1') ?>">
                        </div>
                        <div class="carousel-item">
                            <img src="<?=$this->getBaseUrl($this->getLayoutSetting('slider2')) ?>" alt="<?=$this->getTrans('slider2') ?>">
                        </div>
                        <div class="carousel-item">
                            <img src="<?=$this->getBaseUrl($this->getLayoutSetting('slider3')) ?>" alt="<?=$this->getTrans('slider3') ?>">
                        </div>
                    </div>
                    <button class="carousel-control-prev carousel-control left" type="button" data-bs-target="#carousel-generic" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden"><?=$this->getTrans('previous') ?></span>
                    </button>
                    <button class="carousel-control-next carousel-control right" type="button" data-bs-target="#carousel-generic" data-bs-slide="next">
                       <span class="carousel-control-next-icon" aria-hidden="true"></span>
                       <span class="visually-hidden"><?=$this->getTrans('next') ?></span>
                   </button>
                </div>

                <div class="gaming-name">
                    <?=$this->getLayoutSetting('headertext')?>
                </div>
            </div>
        </div>
      </header>

        <div class="container">
            <div class="gaming">
                <div class="row">
                    <div class="d-none d-md-block col-lg-3 col-xl-2">
                      <?php
                      echo $this->getMenu(
                          1,
                          '<div class="card card-gaming">
                               <div class="card-header">%s</div>
                                  <div class="card-body">
                                      %c
                                  </div>
                           </div>'
                      );
                      ?>
                    </div>
                    <div class="col-12 col-lg-9 col-xl-10">
                      <?=$this->getHmenu() ?>
                      <div class="card card-default">
                          <div class="card-body">
                              <?=$this->getContent() ?>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="container">
                <div class="gaming">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            &copy; Ilch 2.0 Free Layout | CMS by <a href="https://www.ilch.de/">Ilch</a>
                        </div>
                        <div class="col-12 col-lg-6 nav">
                            <ul class="text-end">
                                <li><a href="<?=$this->getUrl() ?>"><?=$this->getTrans('home') ?></a></li>
                                <li><a href="<?=$this->getUrl(['module'=>'contact', 'controller'=>'index', 'action'=>'index']) ?>"><?=$this->getTrans('contact') ?></a></li>
                                <li><a href="<?=$this->getUrl(['module'=>'imprint', 'controller'=>'index', 'action'=>'index']) ?>"><?=$this->getTrans('imprint') ?></a></li>
                                <li><a href="<?=$this->getUrl(['module'=>'privacy', 'controller'=>'index', 'action'=>'index']) ?>"><?=$this->getTrans('privacy') ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?=$this->getFooter() ?>
    </body>
</html>
