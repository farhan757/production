<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot(Dispatcher $events)
    {
        // build side menu
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $menus = Session::get('menu');      
            $event->menu->add('MENU');      
            foreach ($menus as $key => $value) {
                if($value['desc']=='DIVIDER') {
                    $event->menu->add($value['name']);
                } else {
                    if(count($value['contents'])>0) { // parent
                 
                        $event->menu->add([
                            'key'=>$key,
                            'text'=>$value['name'],
                            'icon'=>'fas fa-fw '.$value['icon']
                        ]);

                        foreach ($value['contents'] as $keys => $values) {
                            $event->menu->addIn($key, [
                                'key' => '$keys',
                                'text' => $values['name'],
                                'url' => route($values['url']),
                           ]);
                        }
                    } else { // not parent
                        $event->menu->add([
                            'key'=>$key,
                            'text'=>$value['name'],
                            'url'=>route($value['url']),
                            'icon'=>'fas fa-fw '.$value['icon']
                        ]);
                    }
                }
            }
        });
        
        Blade::directive('currency', function ($expression) {
            return "<?php echo number_format($expression); ?>";
        });        
    }
}
