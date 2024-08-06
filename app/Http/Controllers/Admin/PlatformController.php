<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ContentsRepository;
use App\Repositories\PlatformRepository;
use App\Services\FileManageService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PlatformController extends Controller
{
    public function __construct(
        PlatformRepository $platform,
        ContentsRepository $contents,
    )
    {
        $this->platform = $platform;
        $this->contents = $contents;
        $this->fileManage = app(FileManageService::class);
    }

    /**
     * 장르 리스트
     * @param Request $request
     * @return Application|Factory|View
     */
    public function getGenreList(Request $request){
        $data['page'] = $request->input('page',1);
        $data['sortBy'] = $request->input('sortBy','asc');
        $data['searchType'] = $request->input('searchType');
        $data['searchKey']  = $request->input('searchKey');  // 검색어
        $data['perPage'] = 20;
        $where='';
        if($data['searchType'] && $data['searchKey'] ){
            $where .= " text like '%".$data['searchKey']."%'";
        }
        $data['genreList'] = $this->contents->getContentsGenreList(sortBy: $data['sortBy'],where: $where);
        return view('admin.genreList',$data);
    }

    /**
     * 장르 등록/수정 폼
     * @param int $genreId
     * @return Application|Factory|View
     */
    public function moveGenreForm(int $genreId=0){
        $data['genreInfo'] ='';
        if($genreId > 0){
            $data['genreInfo'] = $this->platform->getContentsGenreInfo($genreId);
        }
        // 정렬 최댓값
        $data['genreMaxSort'] = ($this->platform->getContentsGenreMaxSort() + 1);
        return view('admin.genreForm',$data);
    }

    /**
     * 장르명 (한글/영어) 중복 확인
     * @param Request $request
     * @return int
     */
    public function isGenreText(Request $request){
        $where = " text = '". $request->input('text')."'";
        try {
            $result = ($this->platform->isContentsGenreText($where))? 1:0;
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 장르 등록
     * @param Request $request
     * @return Application|\Illuminate\Routing\Redirector|RedirectResponse
     */
    public function insertContentsGenreInfo(Request $request){

        // 장르 기본 등록
        $insertGenreParam['genre_code'] = '';
        $insertGenreParam['sort'] = $request->input('sort');
        $contentsGenreId = $this->platform->insertContentsGenreInfo($insertGenreParam);
        $genreCode = ($contentsGenreId < 10)?'0'.$contentsGenreId : $contentsGenreId ;
        // 파일 업로드
        $files = $this->fileManage->uploads(type: 'banner',resourceName: 'resourceBanner',request: $request,foldName: $genreCode);
        // 업데이트 DB 인설트 ID값으로
        $fileFormat = explode('/',$files['file_format'])[0];
        $updateGenreParam['contents_genre_id']  =   $contentsGenreId;
        $updateGenreParam['genre_code']         =   $genreCode;
        $updateGenreParam['f_id']               =   $files['name'];
        $updateGenreParam['f_format']           =   ($fileFormat ==='application')? 'json' : $fileFormat;
        $updateGenreParam['f_name']             =   $files['original_name'];
        $updateGenreParam['f_path']             =   $files['path'];
        $this->platform->updateContentsGenreInfo($updateGenreParam);

        // 장르 텍스트
        $insertGenreTextParam['contents_genre_id'] = $contentsGenreId;

        // 한글 장르명 등록
        $insertGenreTextParam['country_id'] = 1;
        $insertGenreTextParam['text'] = $request->input('textKr');
        $this->platform->insertContentsGenreTextInfo($insertGenreTextParam);

        // 영어 장르명 등록
        $insertGenreTextParam['country_id'] = 2;
        $insertGenreTextParam['text'] = $request->input('textEn');
        $this->platform->insertContentsGenreTextInfo($insertGenreTextParam);

        return redirect(route('getGenreList'));
    }

    /**
     * 장르 수정
     * @param Request $request
     * @return Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateContentsGenreInfo(Request $request)
    {
        $contentsGenreId   = $request->input('contentsGenreId');
        $genreInfo = $this->platform->getContentsGenreInfo($contentsGenreId);
        // 장르 이미지 수정
        if($request->hasFile('resourceBanner')) { // 업로드 파일 있을경우에만
            $files = $this->fileManage->uploads(type: 'banner', resourceName: 'resourceBanner', request: $request, foldName: $genreInfo->genre_code);
            $fileFormat = explode('/', $files['file_format'])[0];
            $updateGenreParam['f_id'] = $files['name'];
            $updateGenreParam['f_format'] = ($fileFormat === 'application') ? 'json' : $fileFormat;
            $updateGenreParam['f_name'] = $files['original_name'];
            $updateGenreParam['contents_genre_id'] = $genreInfo->contents_genre_id;
            $this->platform->updateContentsGenreInfo($updateGenreParam);
        }

        // 장르 텍스트
        $updateGenreTextParam['contents_genre_id'] = $genreInfo->contents_genre_id;

        // 한글 장르명 수정
        $updateGenreTextParam['country_id'] = 1; // 1한국 2 미국
        $updateGenreTextParam['text'] = $request->input('textKr');
        $this->platform->updateContentsGenreTextInfo($updateGenreTextParam);

        // 영어 장르명 수정
        $updateGenreTextParam['country_id'] = 2;
        $updateGenreTextParam['text'] = $request->input('textEn');
        $this->platform->updateContentsGenreTextInfo($updateGenreTextParam);

        return redirect(route('getGenreList'));
    }

    /**
     * 장르 정렬순서 변경
     * @param Request $request
     * @return int
     */
    public function updateContentsGenreSortCount(Request $request){
        $contentsGenreId = $request->input('contentsGenreId');
        $option = $request->input('option');
        $selectGenreInfo = $this->platform->getContentsGenreInfo($contentsGenreId);
        $result = 1;

        if($option === 'up'){
            $sort = $selectGenreInfo->sort - 1;
            $where = "sort <= ".$sort." order by sort desc ";
            $updateGenreInfo = $this->platform->getContentsGenreInfo(0,$where);
        } else if($option === 'down'){
            $sort = $selectGenreInfo->sort + 1;
            $where = "sort >= ".$sort." order by sort asc ";
            $updateGenreInfo = $this->platform->getContentsGenreInfo(0,$where);
        }

        if(!$updateGenreInfo){
            return 0; // 위,아래로 갈 사람이 없을때
        }

        // 둘간에 변경
        try{
            $updateSelectGenreParam['contents_genre_id'] = $selectGenreInfo->contents_genre_id;
            $updateSelectGenreParam['sort'] = $sort;
            $this->platform->updateContentsGenreInfo($updateSelectGenreParam);

            $updateGenreParam['contents_genre_id'] = $updateGenreInfo->contents_genre_id;
            $updateGenreParam['sort'] = $selectGenreInfo->sort;
            $this->platform->updateContentsGenreInfo($updateGenreParam);

        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 장르 삭제
     * @param Request $request
     * @return int
     */
    public function deleteContentsGenreInfo(Request $request){
        $contentsGenreId = $request->input('contentsGenreId');
        $path = $request->input('path');
        $result = 1;

        try {
            // 장르 삭제
            $this->platform->deleteContentsGenreInfo($contentsGenreId);
            // 장르 텍스트 삭제
            $this->platform->deleteContentsGenreTextInfo($contentsGenreId);
            // 장르 그룹 삭제
            $this->contents->deleteContentsGenreGroup(0,$contentsGenreId);
            // 장르 배너 파일 삭제
            $this->fileManage->deleteDirectory($path);
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }

        return $result;
    }
    /// 장르 끝

    // 상품
    /**
     * 상품 리스트
     * @param Request $request
     * @return Application|Factory|View
     */
    public function getProductList(Request $request){
        $data['page'] = $request->input('page',1);
        $data['searchType'] = $request->input('searchType');
        $data['searchKey'] = $request->input('searchKey');
        $data['perPage'] = 20;
        $where=[];
        if($data['searchType'] && $data['searchKey'] ){
            if($data['searchType'] == 10   ){
                $column ='product_name';
            } else if  ($data['searchType'] == 20 ){
                $column='sensor_name';
            }
            $where[$column]= $column." like '%".$data['searchKey']."%'";
        }

        $data['productList'] =$this->platform->getProductList($where);
//        print_r( $data['productList']->toarray());
        return view('admin.productList',$data);
    }

    /**
     * 상픔 등록/수정 폼
     * @param int $productId
     * @return Application|Factory|View
     */
    public function moveProductForm(int $productId =0){
        $data['productInfo'] ='';
        if($productId > 0){
            $data['productInfo'] =$this->platform->getProductInfo($productId);
//                    print_r($data['productInfo']->toarray());
        }
        $data['sensorTypeList'] =$this->platform->getSensorTypeList();
//        print_r($data['sensorTypeList']->toarray());
        return view('admin.productForm',$data);
    }

    /**
     * 상품 이름 존재 여부 조회
     * @param Request $request
     * @return int
     */
    public function isProductName(Request $request){
        try {
            $result = ($this->platform->isProductName($request->input('productName')))? 1 : 0 ;
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 상품 저장
     * @param Request $request
     * @return Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function insertProductInfo(Request $request){
        $insertProductParam['product_name'] = $request->input('productName');
        $insertProductParam['product_desc'] = $request->input('productDescription');
        $selectSensorArray = explode(',',$request->input('selectedSensorIds'));
        try {
            // 상품 저장
            $productId = $this->platform->insertProductInfo($insertProductParam);
            // 센서 그룹 저장
            $this->upsertSensorGroupInfo(
                productId: $productId,
                sensorIdArray: $selectSensorArray,
                type: 'insert'
            );
        }  catch (Exception $error) {
            Log::error($error);
            return redirect()->back()->withErrors(['error' => '오류가 발생 했습니다.']);
        }

        return redirect(route('getProductList'));

    }

    /**
     * 상품 수정
     * @param Request $request
     * @return Application|\Illuminate\Routing\Redirector|RedirectResponse
     */
    public function updateProductInfo(Request $request){
        $updateProductParam['product_id'] = $request->input('productId');
        $updateProductParam['product_name'] = $request->input('productName');
        $updateProductParam['product_desc'] = $request->input('productDescription');
//        $selectSensorArray = explode(',',$request->input('selectedSensorIds'));
        try {
            // 상품 정보 수정
            $this->platform->updateProductInfo($updateProductParam);

//            // 상품 수정시 센서 그룹 수정 막기
//            $this->upsertSensorGroupInfo(
//                productId: $request->input('productId'),
//                sensorIdArray: $selectSensorArray,
//                type: 'update'
//            );
        } catch (Exception $error) {
            Log::error($error);
            return redirect()->back()->withErrors(['error' => '오류가 발생 했습니다.']);
        }

        return redirect(route('getProductList'));

    }

    /**
     * 상품 정보 with 센서 그룹 삭제
     * @param Request $request
     * @return int
     */
    public function deleteProductInfo(Request $request){
        $result = 1 ;
        try {
            // 상품 정보
            $this->platform->deleteProductInfo($request->input('productId'));
            // 상품 센서 정보
            $this->platform->deleteSensorGroupInfo(productId: $request->input('productId'),sensor: []);
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 상품 센서 그룹 저장 혹은 수정
     * @param int $productId
     * @param array $sensorIdArray
     * @param string $type
     * @return void
     */
    public function upsertSensorGroupInfo(int $productId, array $sensorIdArray, string $type){
        $sensorIdArray = array_unique($sensorIdArray);
        foreach ($sensorIdArray as $sensorId){
            $insertSensorGroupParams[] = [
                'product_id' => $productId,
                'sensor_id' => $sensorId
            ];
        }
        $this->platform->upsertSensorGroupInfo($insertSensorGroupParams);
        if($type === 'update')
        {
            $this->platform->deleteSensorGroupInfo(productId: $productId,sensor: $sensorIdArray);
        }
    }
    // 상품 끝

    // 센서관리 시작
    /**
     * 센서 리스트 조회
     * @param Request $request
     * @return Application|Factory|View
     */
    public function getSensorList(Request $request){
        $data['page'] = $request->input('page',1);
        $data['searchType'] = $request->input('searchType');
        $data['searchKey'] = $request->input('searchKey');
        $data['perPage'] = 20;
        $where=[];

        if($data['searchType'] && $data['searchKey'] ){
            if($data['searchType'] == 10   ){
                $column ='sensor_code';
            } else if  ($data['searchType'] == 20 ){
                $column='sensor_type';
            } else if  ($data['searchType'] == 30 ){
                $column='sensor_name';
            }
            $where[$column]= $column." like '%".$data['searchKey']."%'";
        }

        $data['sensorList'] = $this->platform->getSensorList($where);
        return view('admin.sensorList',$data);
    }

    /**
     * 센서 폼
     * @param int $sensorId
     * @return Application|Factory|View
     */
    public function moveSensorForm(int $sensorId=0){
        $data['sensorInfo'] ='';
        if($sensorId > 0) {
            $data['sensorInfo'] =$this->platform->getSensorInfo($sensorId);
//                    print_r($data['sensorInfo']->toarray());
        }
        $data['sensorTypeList'] =$this->platform->getSensorTypeList();
        return view('admin.sensorForm',$data);
    }

    /**
     * 센서이름 존재 여부 체크
     * @param Request $request
     * @return int
     */
    public function isSensorName(Request $request){
        try{
            $result = ($this->platform->inSensorName($request->input('sensorName')))? 1:0;
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 센서 저장
     * @param Request $request
     * @return Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function insertSensorInfo(Request $request)
    {
        $insertSensorInfoParam['sensor_type_id']    =   $request->input('sensorType');
        $insertSensorInfoParam['sensor_code']       =   $request->input('sensorCode');
        $insertSensorInfoParam['sensor_name']       =   $request->input('textEn');
        // 센서 아이콘 업로드
        $files = $this->fileManage->uploads(type: 'sensorIcon',resourceName: 'resourceIcon',request: $request,foldName: $request->input('sensorCode'));
        $insertSensorInfoParam['sensor_icon_f_name']    =   $files['original_name'];
        $insertSensorInfoParam['sensor_icon_f_id']      =   $files['name'];
        $insertSensorInfoParam['sensor_exe_f_path']     =   'sensor/'.$request->input('sensorCode');

        try {
            $this->platform->insertSensorInfo($insertSensorInfoParam);
        } catch (Exception $error) {
            Log::error($error);
            return redirect()->back()->withErrors(['error' => '오류가 발생 했습니다.']);
        }
        return redirect(route('getSensorList'));
    }

    /**
     * 센서 수정
     * @param Request $request
     * @return Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateSensorInfo(Request $request){
        $updateSensorInfoParam['sensor_id']             =   $request->input('sensorId');
        $updateSensorInfoParam['sensor_type_id']        =   $request->input('sensorType');
        $updateSensorInfoParam['sensor_name']           =   $request->input('textEn');
        // 센서 아이콘 업로드
        $files = $this->fileManage->uploads(type: 'sensorIcon',resourceName: 'resourceIcon',request: $request,foldName: $request->input('sensorCode'));
        $updateSensorInfoParam['sensor_icon_f_name']    =   $files['original_name'];
        $updateSensorInfoParam['sensor_icon_f_id']      =   $files['name'];
        $updateSensorInfoParam['sensor_exe_f_path']     =   'sensor/'.$request->input('sensorCode');

        try{
            $this->platform->updateSensorInfo($updateSensorInfoParam);
        }catch (Exception $error) {
            Log::error($error);
            return redirect()->back()->withErrors(['error' => '오류가 발생 했습니다.']);
        }
        return redirect(route('getSensorList'));
    }


    /**
     * 센서 삭제
     * @param Request $request
     * @return int
     */
    public function deleteSensorInfo(Request $request){
        $sensorId = $request->input('sensorId');

        try{
            // 센서 정보 삭제
            $this->platform->deleteSensorInfo($sensorId);
            // 센서 그룹 삭제
            $this->platform->deleteSensorGroupInfoFromSensorId($sensorId);
            // 컨텐츠 센서 삭제
            $this->platform->deleteContentsSensorInfoFromSensorId($sensorId);
            // 아이콘 파일 삭제
            $this->fileManage->deleteFile($request->input('path'));

            $result = 1 ;
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }
    // 센서관리 끝

    // 테마 시작

    // 테마 관리
    public function getThemeList(Request $request){
        $data['page'] = $request->input('page',1);
        $data['searchType'] = $request->input('searchType');
        $data['searchKey'] = $request->input('searchKey');
        $data['perPage'] = 20;
        $where=[];

        if($data['searchType'] && $data['searchKey'] ){
            if($data['searchType'] == 10   ){
                $column ='theme_name';
            }
            $where[$column]= $column." like '%".$data['searchKey']."%'";
        }

        $data['themeList'] = $this->platform->getThemeList($where);

        return view('admin.themeList',$data);
    }

    /**
     * 테마 폼
     * @param int $themeId
     * @return Application|Factory|View
     */
    public function moveThemeForm(int $themeId=0){
        $data['themeInfo'] = '';
        if($themeId > 0){
            $data['themeInfo'] = $this->platform->getThemeInfo($themeId);
        }
        return view('admin.themeForm',$data);
    }

    /**
     * 테마명 존재 여부 체크
     * @param Request $request
     * @return int
     */
    public function isThemeName(Request $request){
         $request->input('themeName');
         try{
             $result = ($this->platform->isThemeName($request->input('themeName')))? 1:0;
         }  catch (Exception $error) {
             Log::error($error);
             $result = -1;
         }
         return $result;
    }

    /**
     * 테마 정보 저장
     * @param Request $request
     * @return Application|\Illuminate\Routing\Redirector|RedirectResponse
     */
    public function insertThemeInfo(Request $request){

        $insertThemeParam['theme_name']         =  $request->input('themeName');
        $insertThemeParam['theme_desc']         =  $request->input('themeDescription');
        // 테마 정보 저장
        $themeId  =  $this->platform->insertThemeInfo($insertThemeParam);

        // 테마 리소스 저장 타입(bg,bgm,lightLogo,darkLogo,subLightBg,subDarkBg)
        $sort = 1;
        // 배경 이미지 1~5개 저장
        for($i=1; $i<=5; $i++) {
            if (!$request->hasFile('resourceBackgroundImage'.$i)) {
                continue;
            }
            $this->upsertThemeResource(
                themeId: $themeId,
                sort: $sort,
                resourceType: 'bg',
                resourceName: 'resourceBackgroundImage'.$i,
                request: $request
            );
            $sort++;
        }

        // 배경 음악 저장 1개
        $this->upsertThemeResource(
            themeId: $themeId,
            sort: 1,
            resourceType: 'bgm',
            resourceName: 'resourceBackgroundMusic',
            request: $request
        );

        // 로고(light,dark) 각 1개씩
        $this->upsertThemeResource(
            themeId: $themeId,
            sort: 1,
            resourceType: 'lightLogo',
            resourceName: 'resourceLightLogo',
            request: $request
        );
        $this->upsertThemeResource(
            themeId: $themeId,
            sort: 1,
            resourceType: 'darkLogo',
            resourceName: 'resourceDarkLogo',
            request: $request
        );

        // 서브 배경 이미지(light,dark) 각 1개씩
        $this->upsertThemeResource(
            themeId: $themeId,
            sort: 1,
            resourceType: 'subLightBg',
            resourceName: 'resourceLightBackGroundSub',
            request: $request
        );
        $this->upsertThemeResource(
            themeId: $themeId,
            sort: 1,
            resourceType: 'subDarkBg',
            resourceName: 'resourceDarkBackGroundSub',
            request: $request
        );

        return redirect(route('getThemeList'));
    }

    /**
     * 테마 리소스 저장
     * @param int $themeId
     * @param int $sort
     * @param string $resourceType
     * @param string $resourceName
     * @param Request $request
     * @return void
     */
    public function upsertThemeResource(int $themeId, int $sort,string $resourceType,string $resourceName,Request $request){
        $insertThemeResourceParam['theme_id'] =  $themeId;
        $insertThemeResourceParam['sort'] = $sort;
        $insertThemeResourceParam['resource_type'] =  $resourceType;

        try {
            // 파일 업로드
            $files = $this->fileManage->uploads(type: 'themeResource', resourceName: $resourceName, request: $request, foldName: $request->input('themeName'));
            $insertThemeResourceParam['f_name'] = $files['original_name'];
            $insertThemeResourceParam['f_id'] = $files['name'];
            $insertThemeResourceParam['path'] = 'theme/' . $request->input('themeName');
            $this->platform->insertThemeResoruce($insertThemeResourceParam);
        }  catch (Exception $error) {
            Log::error($error);
        }

    }
}
