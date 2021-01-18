<?php
use Spatie\Menu\Laravel\Menu;
use Spatie\Menu\Laravel\Html;
use Spatie\Menu\Laravel\Link;

//$list = DB::table('menus')->where('user_id','=',1)->get();
//Menu::macro('fullsubmenuexample', function () {
//    return Menu::new()->prepend('<a href="#"><span> Multilevel PROVA </span> <i class="fa fa-angle-left pull-right"></i></a>')
//        ->addParentClass('treeview')
//        ->add(Link::to('/link1prova', 'Link1 prova'))->addClass('treeview-menu')
//        ->add(Link::to('/link2prova', 'Link2 prova'))->addClass('treeview-menu')
//        ->url('http://www.google.com', 'Google');
//});

Menu::macro('adminlteSubmenu', function ($submenuName) {
    return Menu::new()->prepend('<a href="#"><span> ' . $submenuName . '</span> <i class="fa fa-angle-left pull-right"></i></a>')
        ->addParentClass('treeview')->addClass('treeview-menu');
});
Menu::macro('arfiMenu', function () {
    return Menu::new()
        ->addClass('nav nav-pills nav-sidebar flex-column')
        ->setAttribute('data-widget','treeview')
        ->setAttribute('role', 'menu');
});
Menu::macro('arfiLink', function($url, $title) {
    return Link::toRoute($url, $title)
    ->addClass('nav-link')
    ->addParentClass('nav-item');
});
Menu::macro('adminlteSeparator', function ($title) {
    return Html::raw($title)->addParentClass('nav-header');
});

Menu::macro('adminlteDefaultMenu', function ($content) {
    return Html::raw('<i class="fa fa-link"></i><span>' . $content . '</span>')->html();
});


Menu::macro('sidebar', function () {
    /*
    return Menu::adminlteMenu()
        ->add(Html::raw('Menu')->addParentClass('header'))
        ->route('home', '<i class="fa fa-home"></i><span>Home</span>')
        ->route('dashboard', '<i class="fa fa-dashboard"></i><span>Dashboard</span>')
        ->add(Menu::new()->prepend('<a href="#"><i class="fa fa-database"></i><span>Data</span> <i class="fa fa-angle-left pull-right"></i></a>')
            ->addParentClass('treeview')
            ->route('daftar', 'Daftar')->addClass('treeview-menu')
            ->route('upload', 'Upload')->addClass('treeview-menu')
            ->route('list', 'List')->addClass('treeview-menu')
        )
        ->add(Menu::new()->prepend('<a href="#"><i class="fa fa-calendar-check-o"></i><span>Produksi</span> <i class="fa fa-angle-left pull-right"></i></a>')
            ->addParentClass('treeview')
            ->route('scanqc', 'Scan QC')->addClass('treeview-menu')
            ->route('scandistribusi', 'Scan Distribusi')->addClass('treeview-menu')

        )
        ->add(Menu::new()->prepend('<a href="#"><i class="fa fa-send-o"></i><span>Soft Copy</span> <i class="fa fa-angle-left pull-right"></i></a>')
            ->addParentClass('treeview')
            ->route('uploadsoftcopy', 'Upload')->addClass('treeview-menu')
            ->route('listsoftcopy', 'List')->addClass('treeview-menu') 
        )
        ->add(Menu::new()->prepend('<a href="#"><i class="fa fa-comments"></i><span>Customer Service</span> <i class="fa fa-angle-left pull-right"></i></a>')
            ->addParentClass('treeview')
            ->route('requestcs', 'Request')->addClass('treeview-menu')
            ->route('taskcs', 'Task')->addClass('treeview-menu') 
        )
        ->link('/adira/public/logout','<i class="fa fa-sign-out"></i><span>Log out</span>')
        ->setActiveFromRequest();
        */

        /*$menu = Menu::adminlteMenu() 
        ->add(Html::raw('Menu')->addParentClass('header')); 
        ->route('home', '<i class="fa fa-home"></i><span>Home</span>')
        ->route('Dashboard', '<i class="fa fa-dashboard"></i><span>Dashboard</span>')
        ->add(Menu::new()->prepend('<a href="#"><i class="fa fa-database"></i><span>Data</span> <i class="fa fa-angle-left pull-right"></i></a>')
            ->addParentClass('treeview')
            ->route('daftar', 'Daftar')->addClass('treeview-menu')
            ->route('upload', 'Upload')->addClass('treeview-menu')
            ->route('list', 'List')->addClass('treeview-menu')
        )
        ->add(Menu::new()->prepend('<a href="#"><i class="fa fa-calendar-check-o"></i><span>Produksi</span> <i class="fa fa-angle-left pull-right"></i></a>')
            ->addParentClass('treeview')
            ->route('scandistribusi', 'Scan Distribusi')->addClass('treeview-menu')

        )
        ->add(Menu::new()->prepend('<a href="#"><i class="fa fa-send-o"></i><span>Soft Copy</span> <i class="fa fa-angle-left pull-right"></i></a>')
            ->addParentClass('treeview')
            ->route('uploadsoftcopy', 'Upload')->addClass('treeview-menu')
            ->route('listsoftcopy', 'List')->addClass('treeview-menu') 
        )
        ->add(Menu::new()->prepend('<a href="#"><i class="fa fa-comments"></i><span>Customer Service</span> <i class="fa fa-angle-left pull-right"></i></a>')
            ->addParentClass('treeview')
            ->route('requestcs', 'Request')->addClass('treeview-menu')
            ->route('taskcs', 'Task')->addClass('treeview-menu')
            ->route('taskcscomplete','Task Complete')->addClass('treeview-menu')
        )
        ->link('/adira/public/logout','<i class="fa fa-sign-out"></i><span>Log out</span>');*/
        $submenu = Menu::new()
        ->setTagName('div')
        ->addClass('dropdown-menu')
        ->setWrapLinksInList(false)
        ->setActiveClassOnLink(true)
        ->add(Link::to('/about', 'About')->addParentClass('nav-item')->addClass('dropdown-item'));

    Menu::new()
        ->addClass('navbar-nav')
        ->add(Link::to('/', 'Home')->addParentClass('nav-item')->addClass('nav-link'))
        ->submenu(Link::to('#', 'Dropdown link')->addClass('nav-link dropdown-toggle')->setAttribute('data-toggle', 'dropdown'), $submenu->addParentClass('nav-item dropdown'))
        ->setActive(function (Link $link) {
            return $link->url() === '/about';
        })->render();

        /*
        $menu = Menu::adminlteMenu() 
        ->add(Html::raw('Menu')->addParentClass('header')); 
        
        $menus = Session::get('menu');
        foreach ($menus as $key => $value) {
            if(count($value['contents'])>0)
            {
                $tmp = Menu::new()->prepend('<a class="nav-link" href=""><i class="'.$value['icon'].'"></i><p>'.$value['name'].'<i class="fa fa-angle-left pull-right"></i></p></a>')->addParentClass('treeview');
                $contents = $value['contents'];
                foreach ($contents as $key2 => $value2) {
                    $tmp->route($value2['url'],$value2['name'])->addClass('nav nav-treeview');
                }
                $menu->add($tmp);
            } else {
                $menu->route($value['url'], '<i class="'.$value['icon'].'"></i><span>'.$value['name'].'</span>');
            }
        }
        $menu->link('/production/public/logout','<i class="fa fa-sign-out"></i><span>Log out</span>');
        */
    return $menu->setActiveFromRequest();
});
