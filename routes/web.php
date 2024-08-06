<?php

use App\Http\Controllers\Admin\ContentsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\Admin\SignInController;
use App\Http\Controllers\Admin\SignUpController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('admin/')->group(function () {

    // 라이센스
    Route::get('license/form', [LicenseController::class, 'moveLicenseForm'])->name('licenseForm');
    Route::post('license/exists-license-code', [LicenseController::class, 'isLicenseCode'])->name('isLicenseCode');
    Route::post('license/creat-license-code', [LicenseController::class, 'makeLicenseCode'])->name('makeLicenseCode');

    // 회원 가입
    Route::get('signup/form', [SignUpController::class, 'moveSignUpForm'])->name('signUpForm');
    Route::post('signup/exists-login-id', [SignUpController::class, 'isLoginId'])->name('isLoginId');
    Route::post('signup/send-email', [SignUpController::class, 'sendEmail'])->name('sendEmail');
    Route::post('signup/exists-email-code', [SignUpController::class, 'isEmailCode'])->name('isEmailCode');
    Route::post('signup/register-user', [SignUpController::class, 'registerUser'])->name('registerUser');

    // 로그인
    Route::get('signin/form', [SignInController::class, 'moveSignInForm'])->name('signInForm');
    Route::post('signin/process-login', [SignInController::class, 'isLogin'])->name('isLogin');
    Route::get('signin/process-logout', [SignInController::class, 'logout'])->name('logout');

    // Auth 인증내역이 있는 회원만 관리자 페이지 접근가능
    Route::middleware(['auth'])->group(function () {

        // 대쉬보드 @todo: 그래프 등 통계부분이 나올 수 있어 추후 내용 확인 후 작업 필요
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // 사용자 리스트(슈퍼관리자)
        Route::get('user/main/list', [UserController::class, 'getUserMainList'])->name('getUserMainList');
        Route::post('user/main/use-flag', [UserController::class, 'useFlag'])->name('useFlag');
        Route::post('user/main/exists-password', [UserController::class, 'isPassword'])->name('isPassword');
        Route::get('user/main/edit-form/{userId}/{utCode}', [UserController::class, 'moveUserEditForm'])->name('userEditForm');
        Route::post('user/main/edit-info', [UserController::class, 'updateUserInfo'])->name('updateUserInfo');

        // 기관별 그룹 유저 리스트
        Route::get('user/agency/group/list', [UserController::class, "getGroupList"])->name("getGroupList");
        Route::get('user/agency/group/form/{agencyId}', [UserController::class, "moveGroupForm"])->name("moveGroupForm");
        Route::post('user/agency/group/register-group', [UserController::class, "insertGroup"])->name("insertGroup");
        Route::post('user/agency/group/edit-approve-status', [UserController::class, "updateApproveStatus"])->name("updateApproveStatus");
        Route::post('user/agency/group/edit-sort-count', [UserController::class, "updateSortCount"])->name("updateSortCount");
        Route::post('user/agency/group/move-group', [UserController::class, "updateUserGroupPosition"])->name("updateUserGroupPosition");
        Route::get('user/agency/group/user-form/{agencyId}/{groupId}', [UserController::class, "moveUserForm"])->name("moveUserForm");
        Route::post('user/agency/group/register-group-user', [UserController::class, "insertGroupUser"])->name("insertGroupUser");
        Route::get('user/agency/group/user-list', [UserController::class, "getGroupUserList"])->name("getGroupUserList");
        Route::get('user/agency/group/excel-form/{agencyId}/{groupId}', [UserController::class, "moveExcelForm"])->name("moveExcelForm");
        Route::post('user/agency/group/excel-user-list', [UserController::class, "getExcelDataList"])->name("getExcelDataList");
        Route::post('user/agency/group/register-excel-data', [UserController::class, "insertExcelData"])->name("insertExcelData");

        // 콘텐츠
        Route::get('contents/form/{contentsId?}', [ContentsController::class, 'moveContentsForm'])->name('moveContentsForm');
        Route::post('contents/exists-contents-name', [ContentsController::class, 'isContentsName'])->name('isContentsName');
        Route::post('contents/register-contents-info', [ContentsController::class, 'insertContentsInfos'])->name('insertContentsInfos');
        Route::post('contents/edit-contents-info', [ContentsController::class, 'updateContentsInfos'])->name('updateContentsInfos');
        Route::post('contents/delete-contents-resource', [ContentsController::class, 'deleteContentsResource'])->name('deleteContentsResource');
        Route::get('contents/list', [ContentsController::class, 'getContentsList'])->name('getContentsList');
        Route::post('contents/edit-contents-sort-count', [ContentsController::class, 'updateContentsSortCount'])->name('updateContentsSortCount');
        Route::post('contents/delete-contents-info', [ContentsController::class, 'deleteContents'])->name('deleteContents');
        Route::get( 'contents/manage/exe-file-version/list/{contentsId?}', [ContentsController::class, 'getContentsVersionList'])->name('getContentsVersionList');
        Route::post('contents/manage/exe-file-upload', [ContentsController::class, 'insertVersionWithExeFile'])->name('insertVersionWithExeFile');
        Route::post('contents/manage/exe-file-rollBack', [ContentsController::class, 'processExeFileRollBack'])->name('processExeFileRollBack');

        // 플렛폼-장르
        Route::get('platform/genre/form/{genreId?}', [PlatformController::class, 'moveGenreForm'])->name('moveGenreForm');
        Route::get('platform/genre/list', [PlatformController::class, 'getGenreList'])->name('getGenreList');
        Route::post('platform/genre/exists-genre-name', [PlatformController::class, 'isGenreText'])->name('isGenreInfo');
        Route::post('platform/genre/register-genre-info', [PlatformController::class, 'insertContentsGenreInfo'])->name('insertGenreInfo');
        Route::post('platform/genre/edit-genre-info', [PlatformController::class, 'updateContentsGenreInfo'])->name('updateGenreInfo');
        Route::post('platform/genre/edit-genre-sort-count', [PlatformController::class, 'updateContentsGenreSortCount'])->name('updateGenreSortCount');
        Route::post('platform/genre/delete-genre-info', [PlatformController::class, 'deleteContentsGenreInfo'])->name('deleteContentsGenreInfo');

        // 플렛폼-상품
        Route::get('platform/product/form/{productId?}', [PlatformController::class, 'moveProductForm'])->name('moveProductForm');
        Route::get('platform/product/list', [PlatformController::class, 'getProductList'])->name('getProductList');
        Route::post('platform/product/exists-product-name', [PlatformController::class, 'isProductName'])->name('isProductName');
        Route::post('platform/product/register-product-info', [PlatformController::class, 'insertProductInfo'])->name('insertProductInfo');
        Route::post('platform/product/edit-product-info', [PlatformController::class, 'updateProductInfo'])->name('updateProductInfo');
        Route::post('platform/product/delete-product-info', [PlatformController::class, 'deleteProductInfo'])->name('deleteProductInfo');

        // 플렛폼-센서
        Route::get('platform/sensor/form/{sensorId?}', [PlatformController::class, 'moveSensorForm'])->name('moveSensorForm');
        Route::get('platform/sensor/list', [PlatformController::class, 'getSensorList'])->name('getSensorList');
        Route::post('platform/sensor/exists-sensor-name', [PlatformController::class, 'isSensorName'])->name('isSensorName');
        Route::post('platform/sensor/register-sensor-info', [PlatformController::class, 'insertSensorInfo'])->name('insertSensorInfo');
        Route::post('platform/sensor/edit-sensor-info', [PlatformController::class, 'updateSensorInfo'])->name('updateSensorInfo');
        Route::post('platform/sensor/delete-sensor-info', [PlatformController::class, 'deleteSensorInfo'])->name('deleteSensorInfo');

        // 플렛폼-테마
        Route::get('platform/theme/form/{themeId?}', [PlatformController::class, 'moveThemeForm'])->name('moveThemeForm');
        Route::get('platform/theme/list', [PlatformController::class, 'getThemeList'])->name('getThemeList');
        Route::post('platform/theme/exists-theme-name', [PlatformController::class, 'isThemeName'])->name('isThemeName');
        Route::post('platform/theme/register-theme-info', [PlatformController::class, 'insertThemeInfo'])->name('insertThemeInfo');
        Route::post('platform/theme/edit-theme-info', [PlatformController::class, 'updateThemeInfo'])->name('updateThemeInfo');
        Route::post('platform/theme/delete-theme-info', [PlatformController::class, 'deleteThemeInfo'])->name('deleteThemeInfo');

    });

});

// 테스트
Route::post('test', [TestController::class, 'test']);
Route::get('excelForm', [TestController::class, 'excelForm']);
