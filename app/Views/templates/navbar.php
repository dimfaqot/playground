 <!-- navbar md-->
 <?php if (menu()): ?>
     <div class="d-none d-md-block fixed-top">

         <div class="fixed-top bg_main py-3 border_bottom">
             <div class="container">
                 <div class="d-flex justify-content-between">
                     <div class="d-flex gap-2">
                         <?php foreach (menus("grup") as $i): ?>
                             <?php if ($i['grup'] == ""): ?>
                                 <a href="<?= base_url($i['controller']); ?>" class="btn btn-sm <?= (url() == $i['controller'] ? "link_secondary" : "link_main"); ?>"><i class="<?= $i['icon']; ?>"></i> <?= $i['menu']; ?></a>
                             <?php else: ?>
                                 <div class="nav-item dropdown btn btn-sm <?= (menu(url())['grup'] == $i['grup'] ? 'link_secondary' : 'link_main'); ?>">
                                     <a class="nav-link dropdown-toggle <?= (menu(url())['grup'] == $i['grup'] ? 'link_secondary' : 'link_main'); ?>" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false"><i class="<?= $i['icon']; ?>"></i> <?= $i['grup']; ?></a>
                                     <ul class="dropdown-menu bg_main border border_main p-1">
                                         <?php foreach (menus($i['grup']) as $k => $m): ?>
                                             <li class="d-grid <?= ($k > 0 ? "mt-1" : ""); ?>"><a style="text-align: left;font-size:small" href="<?= base_url($m['controller']); ?>" class="btn btn-sm <?= (url() == $m['controller'] ? "link_secondary" : "link_main"); ?>"><i class="<?= $m['icon']; ?>"></i> <?= $m['menu']; ?></a></li>
                                         <?php endforeach; ?>
                                     </ul>
                                 </div>
                             <?php endif; ?>
                         <?php endforeach; ?>

                     </div>

                     <div class="d-flex justify-content-end gap-2">
                         <?php if (user()): ?>
                             <div class="form-check form-switch" style="margin-top: 3px;">
                                 <input class="form-check-input switch_tema" type="checkbox" <?= (settings('Tema') == "dark" ? "checked" : ""); ?> role="switch">
                                 <label class="form-check-label"><?= upper_first(settings('Tema')); ?></label>
                             </div>
                             <div class="btn btn-sm link_main border border_main"><?= user()['nama']; ?></div>
                             <a href="<?= base_url('logout'); ?>" class="btn btn-sm btn-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
                         <?php else: ?>
                             <div class="btn btn-sm link_main"><?= date('M, d Y'); ?></div>
                             <div class="btn btn-sm link_main"><?= date('H:i'); ?></div>

                         <?php endif; ?>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!-- navbar sm -->
     <div class="d-block d-md-none d-sm-block fixed-top" style="font-size: 12px;">

         <div class="fixed-top bg_main border_bottom py-3">
             <div class="container">
                 <div class="d-flex justify-content-between">
                     <a class="link_main btn btn-sm" href="<?= base_url(menu()['controller']); ?>"><i class="<?= menu()['icon']; ?>"></i> <?= menu()['menu']; ?></a>
                     <div class="form-check form-switch" style="margin-top: 3px;">
                         <input class="form-check-input switch_tema" type="checkbox" <?= (settings('Tema') == "dark" ? "checked" : ""); ?> role="switch">
                         <label class="form-check-label"><?= upper_first(settings('Tema')); ?></label>
                     </div>
                     <a data-bs-toggle="offcanvas" data-bs-target="#leftMenu" aria-controls="leftMenu" class="text_main" href=""><i class="fa-solid fa-bars"></i></a>
                 </div>
             </div>
         </div>
         <!-- camvas -->
         <div class="offcanvas offcanvas-start" style="width:90%" data-bs-scroll="true" tabindex="-1" id="leftMenu" aria-labelledby="leftMenuLabel">

             <div class="offcanvas-body bg_main">
                 <div class="d-flex justify-content-between border-bottom border-light pb-2">
                     <div>MENU</div>
                     <div><a href="" role="button" class="link_main" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-circle-xmark"></i></a></div>
                 </div>
                 <?php foreach (menus() as $i): ?>
                     <div class="d-grid mt-2 <?= url() == $i['controller'] ? "link_secondary" : "link_main"; ?> py-1 px-2 rounded"><a style="text-decoration: none;" href="<?= base_url($i['controller']); ?>" role="button" class="text_main"><i class="<?= $i['icon']; ?>"></i> <?= $i['menu']; ?></a></div>
                 <?php endforeach; ?>
             </div>
         </div>


     </div>
 <?php endif; ?>