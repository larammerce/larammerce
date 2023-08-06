<?php

namespace App\Providers;

use App\Interfaces\FileHandlerInterface;
use App\Interfaces\Repositories\SettingRepositoryInterface;
use App\Repositories\Eloquent\SettingRepositoryEloquent;
use App\Services\Common\EnvFile\EnvFileHandler;
use App\Services\Invoice\NewInvoiceService;
use App\Utils\CMS\RobotTxt\RobotTxtService;
use App\Utils\CMS\Setting\Logistic\LogisticService;
use App\Utils\Validation\ValidationRule;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $proxy_url = env('PROXY_URL');
        $proxy_schema = env('PROXY_SCHEMA');

        if (!empty($proxy_url)) {
            URL::forceRootUrl($proxy_url);
        }

        if (!empty($proxy_schema)) {
            URL::forceScheme($proxy_schema);
        }

        //Blade directives
        Blade::directive('roleinput', function ($expression) {
            return '<?php echo role_input(' . $expression . ') ;?>';
        });
        Blade::directive('back', function ($expression) {
            return '<?php echo history_back(' . $expression . ') ;?>';
        });
        Blade::directive('price', function ($expression) {
            return '<?php echo format_price(' . $expression . ') ;?>';
        });
        Blade::directive('modal', function ($expression) {
            return '<?php echo modal_html(' . $expression . ') ;?>';
        });

        //Validator extensions
        Validator::extend('national_code', ValidationRule::class . '@nationalCode');
        Validator::extend('user_alphabet_rule', ValidationRule::class . '@alpha');
        Validator::extend('mobile_number', ValidationRule::class . '@mobileNumber');
        Validator::extend('delivery_period', LogisticService::class . "@validateDeliveryPeriod");
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        // set the public path to this directory
        $this->app->bind('path.public', function () {
            return base_path() . '/public_html';
        });

        $this->app->singleton('robotTxtService.admin', function ($app) {
            return new RobotTxtService('admin');
        });

        $this->app->singleton('robotTxtService.auto', function ($app) {
            return new RobotTxtService('auto');
        });

        //Services
        $this->app->singleton(FileHandlerInterface::class, EnvFileHandler::class);
        $this->app->singleton(NewInvoiceService::class);

        //Repositories
        $this->app->bind(SettingRepositoryInterface::class, SettingRepositoryEloquent::class);
    }
}
