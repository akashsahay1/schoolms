<?php

use App\SmLanguagePhrase;
use App\SmModule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Modules\RolePermission\Entities\InfixModuleStudentParentInfo;
use SpondonIt\Rpclient\RaudhahPay;

if (config('app.app_sync')) {
    Route::get('/', 'LandingController@index')->name('/');
}

if (moduleStatusCheck('Saas')) {
  echo "Hi";	
    Route::group(['middleware' => ['subdomain'], 'domain' => '{subdomain}.' . config('app.short_url')], function ($routes) {
        require ('tenant.php');
    });

    Route::group(['middleware' => ['subdomain'], 'domain' => '{subdomain}'], function ($routes) {
        require ('tenant.php');
    });
}

Route::group(['middleware' => ['subdomain']], function ($routes) {
    require ('tenant.php');
});

//Route::get('tariqul', function(){
//    DB::statement('ALTER TABLE `sm_general_settings` CHANGE `academic_id` `academic_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
//    DB::statement('ALTER TABLE `sm_general_settings` DROP FOREIGN KEY `sm_general_settings_academic_id_foreign`;');
//    DB::statement('ALTER TABLE `sm_general_settings` ADD CONSTRAINT `sm_general_settings_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `sm_academic_years`(`id`) ON DELETE SET NULL ON UPDATE RESTRICT;');
//    DB::statement('ALTER TABLE `sm_general_settings` DROP FOREIGN KEY `sm_general_settings_date_format_id_foreign`; ');
//    DB::statement('ALTER TABLE `sm_general_settings` ADD CONSTRAINT `sm_general_settings_date_format_id_foreign` FOREIGN KEY (`date_format_id`) REFERENCES `sm_date_formats`(`id`) ON DELETE SET NULL ON UPDATE RESTRICT;');
//    DB::statement('ALTER TABLE `sm_general_settings` DROP FOREIGN KEY `sm_general_settings_language_id_foreign`;');
//    DB::statement('ALTER TABLE `sm_general_settings` ADD CONSTRAINT `sm_general_settings_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `sm_languages`(`id`) ON DELETE SET NULL ON UPDATE RESTRICT;');
//    DB::statement('ALTER TABLE `sm_general_settings` DROP FOREIGN KEY `sm_general_settings_session_id_foreign`;');
//    DB::statement('ALTER TABLE `sm_general_settings` ADD CONSTRAINT `sm_general_settings_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sm_academic_years`(`id`) ON DELETE SET NULL ON UPDATE RESTRICT;');
//});




// Route::get('tariqul', function(){
//     // abort(404);
//     $lang_pharses = SmLanguagePhrase::all();
//
//     $locale = [
//         'en' => [],
//         'es' => [],
//         'bn' => [],
//         'fr' => [],
//     ];
//
//
//     $module = SmModule::all()->pluck('name', 'order');
//
//
//     foreach($lang_pharses as $pharse){
//         if(gv($module, $pharse->modules)){
//             $locale['en'][$module[$pharse->modules]][$pharse->default_phrases] = $pharse->en;
//             $locale['es'][$module[$pharse->modules]][$pharse->default_phrases] = $pharse->es;
//             $locale['bn'][$module[$pharse->modules]][$pharse->default_phrases] = $pharse->bn;
//             $locale['fr'][$module[$pharse->modules]][$pharse->default_phrases] = $pharse->fr;
//         } else{
//             $locale['en']['Common'][$pharse->default_phrases] = $pharse->en;
//             $lang['es']['Common'][$pharse->default_phrases] = $pharse->es;
//             $locale['bn']['Common'][$pharse->default_phrases] = $pharse->bn;
//             $locale['fr']['Common'][$pharse->default_phrases] = $pharse->fr;
//         }
//     }
//
//     foreach($locale as $folder => $files){
//         $folder = base_path() . '/resources/lang/' . $folder.'/json';
//
//         if (!file_exists($folder)) {
//             File::makeDirectory($folder, 0777, true, true);
//         }
//         foreach($files as $file => $lang){
//            $file = strtolower($file);
//            $file = str_replace(' ', '_', $file);
//             $file = $folder .'/'.$file.'.json';
//             File::put($file, json_encode($lang, JSON_PRETTY_PRINT));
//
//         }
//     }
//
// });

Route::get('tariqul', function(){
   $permissions = InfixModuleStudentParentInfo::all()->pluck('name', 'name')->toArray();

   echo '<pre>';
   return var_export($permissions);



   foreach($permissions as $permission){
       dd($permission);
   }
});
