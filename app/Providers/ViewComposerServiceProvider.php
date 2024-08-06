<?php

namespace App\Providers;

use App\Constants\TranslateConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $request = app(Request::class);
            // 모바일에서는 ko_KR en_EN 으로 받아서 분기
            if(!$request->lang) {
                $lang = explode(',',$request->header('Accept-Language'))[0];
            } else {
                $lang = $request->lang;
            }
            // 한국,미국 제외한 나라들 언어는 영어로
            if($lang !== 'ko' && $lang !== 'en') {
                $lang = 'en';
            }
            $view->with('translateConstants', TranslateConstants::class);
            $view->with('lang',$lang);
        });
    }
}
