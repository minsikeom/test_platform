<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ContentsRepository;
use App\Services\FileManageService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ContentsController extends Controller
{
    public function __construct(ContentsRepository $contents)
    {
        $this->contents = $contents;
        $this->fileManage = app(FileManageService::class);
    }

    /**
     * 콘텐츠 등록/수정 폼
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function moveContentsForm(int $contentsId=0)
    {
        $data['genreList']                   = $this->contents->getContentsGenreList();
        $data['themeList']                   = $this->contents->getThemeList();
        $data['sensorList']                  = $this->contents->getSensorList();
        $data['developmentalElementList']    = $this->contents->getDevelopmentalElementList();
        $data['scoreType']                   = $this->contents->getContentsScoreType();
        $data['contentsMaxSort']             = ($this->contents->getContentsMaxSort()+1);
        if($contentsId >0) {
            $data['contentsId'] = $contentsId;
            $data['contentsInfo'] = $this->contents->getContentsInfo(
                contentsId: $contentsId
            );
        }
//        print_r( $data['contentsInfo']->toarray());
        return view('admin.contentsForm',$data);
    }

    /**
     * 콘텐츠 제목(한글,영어) 중복 확인
     * @param Request $request
     * @return int
     */
    public function isContentsName(Request $request){
         try {
             $res = ($this->contents->isContentsName($request->input('name')))? 1:0;
         } catch (Exception $error) {
             Log::error($error);
             $res = -1;
         }
        return $res;
    }

    /**
     * 콘텐츠 저장
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insertContentsInfos(Request $request){

        $files = $this->fileManage->uploads(type:'exe',resourceName: 'contentExeFile',request: $request);

        // 콘텐츠 정보
        $insertContentsParam['contents_code']=$request->input('contentCode');
        $insertContentsParam['f_exe_name']=$files['original_name'];
        $insertContentsParam['f_path']= $files['path'];
        $insertContentsParam['version']=$request->input('version');
        $insertContentsParam['contents_score_type_id']=$request->input('scoreType');
        $insertContentsParam['sort']=$request->input('sort');
        $insertContentsParam['sort_by']=$request->input('sortBy');
        $contentsId = $this->contents->insertContents($insertContentsParam);

        // 콘텐츠 버전 히스토리
        $this->insertVersionWithoutExeFile(contentsId: $contentsId,version: $request->input('version'));

        // 장르
        $this->upsertContentsGenreGroup(contentsId: $contentsId,request: $request);

        // 센서
        $this->upsertContentsSensor(contentsId: $contentsId,request: $request);

        // 테마
        $this->upsertThemeContents(contentsId: $contentsId,request: $request);

        // 발달 요소
        $this->upsertDevelopmentalElement(contentsId: $contentsId,request: $request);

        // 한글,영어 텍스트
        $this->upsertContentsText(contentsId: $contentsId,request: $request);

        // 콘텐츠 리소스
        for($i=1; $i<5; $i++) {
            // 한국 썸네일,이미지 3개
            $this->upsertContentsResource(
                contentsId: $contentsId, type: 'resource', national: 1,
                resourceName: 'resource'.$i, request: $request
            );

            // 영어 썸네일 이미지 3개 있을때만
            if(!$request->hasFile('resource'.$i.'Eng')){
                continue;
            }
            $this->upsertContentsResource(
                contentsId: $contentsId, type: 'resource', national: 2,
                resourceName: 'resource'.$i.'Eng', request: $request
            );
        }

        return redirect()->route('getContentsList');
    }

    /**
     * 장르 저장 혹은 수정
     * @param int $contentsId
     * @param Request $request
     * @return void
     */
    public function upsertContentsGenreGroup(int $contentsId, Request $request){
        $insertContentsGenreParam['contents_id'] = $contentsId;
        $insertContentsGenreParam['contents_genre_id'] = $request->input('genre');
        $this->contents->upsertContentsGenreGroup($insertContentsGenreParam);
    }

    /**
     * 센서 저장
     * @param int $contentsId
     * @param Request $request
     * @param string $type
     * @return void
     */
    public function upsertContentsSensor(int $contentsId, Request $request,string $type='insert'){
        // 1순위 2순위 3순위 같은 센서 선택시 병합
        $sensorList = array_unique($request->input('sensor'));
        $count = 1;
        foreach ($sensorList as $key => $sensor){
            $insertContentsSensorParam[$key]['contents_id'] = $contentsId;
            $insertContentsSensorParam[$key]['sensor_id'] = $sensor;
            $insertContentsSensorParam[$key]['sensor_priority'] = $count;
            $count++;
        }
        $this->contents->upsertContentsSensor($insertContentsSensorParam);

        if($type === 'update'){ //수정시 항목 다르면 제거
            $this->contents->deleteContentsSensor($request->input('contentsId'),sensor: $sensorList);
        }
    }

    /**
     * 테마별 컨텐츠 저장 혹은 수정
     * @param int $contentsId
     * @param Request $request
     * @param string $type
     * @return void
     */
    public function upsertThemeContents(int $contentsId, Request $request,string $type='insert')
    {
        foreach ($request->input('theme') as $theme){
            $insertThemeContentsParam[] = [
                'contents_id' => $contentsId,
                'theme_id' => $theme,
            ];
        }
        $this->contents->upsertThemeContents($insertThemeContentsParam);

        if($type === 'update'){ // 수정시에 갯수 변경되면 없는 내역 제거
            $this->contents->deleteThemeContents($request->input('contentsId'),themes: $request->input('theme'));
        }
    }

    /**
     * 발달요소 저장 혹은 수정
     * @param int $contentsId
     * @param Request $request
     * @param string $type
     * @return void
     */
    public function upsertDevelopmentalElement(int $contentsId, Request $request,string $type='insert'){
        foreach ($request->input('developmentalElement') as $element){
            $insertDevelopmentalElementParam[] = [
            'contents_id' => $contentsId,
            'developmental_element_type_id' => $element
            ];
        }
        $this->contents->upsertDevelopmentalElement($insertDevelopmentalElementParam);
        if($type === 'update'){ // 수정시 항목 다르면 제거
            $this->contents->deleteDevelopmentalElement($request->input('contentsId'),element: $request->input('developmentalElement'));
        }
    }

    /**
     * 콘텐츠 국가별 텍스트 저장
     * @param int $contentsId
     * @param Request $request
     * @return void
     */
    public function upsertContentsText(int $contentsId, Request $request){
        // 한글 텍스트
        $insertContentsTextParam['contents_id'] = $contentsId;
        $insertContentsTextParam['country_id']  = 1; // 한국 1
        $insertContentsTextParam['text_title']  = $request->input('contentsName');
        $insertContentsTextParam['text_desc']   = $request->input('description');
        $this->contents->upsertContentsText($insertContentsTextParam);

        // 영어 텍스트
        $insertContentsEnTextParam['contents_id']  = $contentsId;
        $insertContentsEnTextParam['country_id']   = 2; // 미국 2
        $insertContentsEnTextParam['text_title']   = $request->input('contentsEnName');
        $insertContentsEnTextParam['text_desc']    = $request->input('descriptionEng');
        $this->contents->upsertContentsText($insertContentsEnTextParam);
    }

    /**
     * 콘텐츠 리소스 저장 혹은 수정
     * @param int $contentsId
     * @param string $type
     * @param int $national
     * @param string $resourceName
     * @param Request $request
     * @return void
     */
    public function upsertContentsResource(int $contentsId, string $type, int $national, string $resourceName, Request $request){
        // 파일 저장
        $files = $this->fileManage->uploads(type: $type,resourceName: $resourceName,request: $request);
        $sort = str_replace("resource", '', $resourceName);
        if($national == 2) // 영어일때 Eng 한번 더 제거
        {
            $sort = str_replace('Eng','',$sort);
        }
        $insertContentsResourceParam['contents_id'] =   $contentsId;
        $insertContentsResourceParam['country_id']  =   $national; // 1 한국 2 미국
        $insertContentsResourceParam['sort']        =   $sort;
        $insertContentsResourceParam['f_format']    =   explode('/',$files['file_format'])[0];
        $insertContentsResourceParam['f_id']        =   $files['name'];
        $insertContentsResourceParam['f_name']      =   $files['original_name'];
        $insertContentsResourceParam['f_path']      =   $files['path'];
        $insertContentsResourceParam['use_flag']    =   'Y';
        $this->contents->upsertContentsResource($insertContentsResourceParam);
    }

    /**
     * 콘텐츠 리스트
     * @param Request $request
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getContentsList(Request $request){
        $data['perPage']                = 20;
        $data['page']                   = $request->input('page',1);
        $data['sortBy']                 = $request->input('sortBy','desc');
        $data['createdAtSortBy']        = $request->input('createdAtSortBy','desc');
        $data['searchType']             = $request->input('searchType'); // 셀렉트박스
        $data['searchKey']              = $request->input('searchKey');  // 검색어
        $where=[];

        if($data['searchType'] && $data['searchKey'] ){
            if($data['searchType'] == 10 || $data['searchType'] == 20  ){
                $column ='text_title';
            } else if  ($data['searchType'] == 30 ){
                $column='text';
            }else if  ($data['searchType'] == 40 ){
                $column = 'sensor_name';
            }else if  ($data['searchType'] == 50 ){
                $column ='theme_name';
            }
            $where[$column]= $column." like '%".$data['searchKey']."%'";
        }

        $contentsListAll = $this->contents->getContentsList(
            where: $where,
            sortBy: $data['sortBy'],
            createdAtSortBy: $data['createdAtSortBy']
        );

//        print_r($contentsListAll->toarray());

        $data['contentsList'] =$contentsListAll;

        return view('admin.contentsList',$data);
    }

    /**
     * 콘텐츠 정렬순서 변경
     * @param Request $request
     * @return int
     */
    public function updateContentsSortCount(Request $request){
        $contentsId    = $request->input('contentsId');
        $option        = $request->input('option');
        $selectContentsInfo = $this->contents->getContentsInfo($contentsId);
        $result = 1;
        $where='';

        if($option === 'up') { // 콘텐츠는 내림차순이라 반대로
            $sort  = $selectContentsInfo->sort + 1;
            $where = "sort >= ".$sort." order by sort asc";
        } else if($option === 'down') {
            $sort  = $selectContentsInfo->sort - 1;
            $where = "sort <= ".$sort." order by sort desc";
        }
        $updateContentsInfo = $this->contents->getContentsInfo(contentsId: 0,where:$where);

        if(!$updateContentsInfo){
            return 0; // 위,아래로 갈 사람이 없을때
        }

        // 둘 사이에 sort 변경
        $selectContentsParam['contents_id']=$selectContentsInfo->contents_id;
        $selectContentsParam['sort']= $updateContentsInfo->sort;
        $updateContentsParam['contents_id'] = $updateContentsInfo->contents_id;
        $updateContentsParam['sort'] = $selectContentsInfo->sort;
        try{
            $this->contents->updateContents($selectContentsParam);
            $this->contents->updateContents($updateContentsParam);
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 콘텐츠 정보 수정
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateContentsInfos(Request $request)
    {
        // 장르 단일 업데이트만
        $this->upsertContentsGenreGroup($request->input('contentsId'),$request);

        // 테마 여러개 가능 인설트 업데이트 가능
        $this->upsertThemeContents($request->input('contentsId'),$request,'update');

        // 센서 여러개 가능 인설트 업데이트 가능
        $this->upsertContentsSensor($request->input('contentsId'),$request,'update');

        // 발달요소 여러개 가능 인설트 업데이트 가능
        $this->upsertDevelopmentalElement($request->input('contentsId'),$request,'update');

        // 한국 썸네일,이미지 3개 -> 수정만
        for($i=1; $i<5; $i++) {
            if(!$request->hasFile('resource'.$i)){
                continue;
            }
            // 한국 썸네일,이미지 3개 -> 수정만
            $this->upsertContentsResource(
                contentsId: $request->input('contentsId'), type: 'resource', national: 1,
                resourceName: 'resource'.$i, request: $request
            );
        }

        // 영어 썸네일 이미지 3개 있을때만 -> 저장 , 수정 둘다
        for($i=1; $i<5; $i++) {
            if (!$request->hasFile('resource' . $i . 'Eng')) {
                continue;
            }
            $this->upsertContentsResource(
                contentsId: $request->input('contentsId'), type: 'resource', national: 2,
                resourceName: 'resource' . $i . 'Eng', request: $request
            );
        }

        // 설명 단일 한글/영어 업데이트만
        $this->upsertContentsText($request->input('contentsId'),$request);

        // 스코어 정렬 순서 -> 업데이트만
        $updateContentsParam['contents_id'] = $request->input('contentsId');
        $updateContentsParam['contents_score_type_id'] = $request->input('scoreType');
        $updateContentsParam['sort_By'] =  $request->input('sortBy');
        $this->contents->updateContents($updateContentsParam);

        return redirect()->route('getContentsList');
    }

    /**
     * 콘텐츠 삭제
     * @param Request $request
     * @return int
     */
    public function deleteContents(Request $request){
        $contentsId = $request->input('contentsId');
        $path = explode('/release/',$request->input('path'))[0];

        $result = 1;
        try {
            // 콘텐츠 정보
            $this->contents->deleteContents($contentsId);

            // 콘텐츠 장르
            $this->contents->deleteContentsGenreGroup($contentsId);

            // 콘텐츠 센서
            $this->contents->deleteContentsSensor(contentsId: $contentsId, sensor: []);

            // 발달요소
            $this->contents->deleteDevelopmentalElement(contentsId: $contentsId, element: []);

            // 콘텐츠 리소스
            $this->contents->deleteContentsResource(contentsId:$contentsId,contentsResourceId: 0);

            // 콘텐츠 텍스트
            $this->contents->deleteContentsText($contentsId);

            // 테마별 콘텐츠
            $this->contents->deleteThemeContents(contentsId: $contentsId, themes: []);

            // 콘텐츠 버전 히스토리 제거
            $this->contents->deleteVersionHistory($contentsId);

            // 실행파일,리소스 폴더 통으로 제거
            $this->fileManage->deleteDirectory($path);
        }  catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }

        return $result;
    }

    /**
     * 콘텐츠 영어  특정 리소스(이미지/영상) 삭제
     * @param Request $request
     * @return int
     */
    public function deleteContentsResource(Request $request){
        $result = 1 ;
        try {
            $this->contents->deleteContentsResource(contentsId: 0, contentsResourceId: $request->input('contentsResourceId'));
        }  catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 콘텐츠 버전관리
     * @param int $contentsId
     * @param Request $request
     * @return Application|Factory|View
     */
    public function getContentsVersionList(int $contentsId,Request $request){
        $data['contentsId']   = $contentsId;
        $data['page'] = $request->input('page');
        $data['contentsInfo'] = $this->contents->getContentsInfo($contentsId);
        $data['path'] = 'contents/'.$data['contentsInfo']->contents_code.'/release/';
        $data['versionList'] = $this->contents->getContentsVersionList($contentsId);
        return view('admin.contentsVersionForm',$data);
    }

    /**
     * 히스토리 버전 저장 without 실행파일
     * @param int $contentsId
     * @param string $version
     * @return void
     */
    public function insertVersionWithoutExeFile(int $contentsId, string $version){
        $insertVersionHistoryParam['contents_id'] = $contentsId;
        $insertVersionHistoryParam['version'] = $version;
        $insertVersionHistoryParam['description'] = '최초 버전';
        $insertVersionHistoryParam['status'] = 'R';  // R=배포
        $this->contents->insertVersionHistroy($insertVersionHistoryParam);
    }

    /**
     * 버전 히스토리 저장 with 실행파일
     * @param Request $request
     * @return Application|\Illuminate\Routing\Redirector|RedirectResponse
     */
    public function insertVersionWithExeFile(Request $request)
    {
        $updateVersionHistoryParam['contents_id'] = $request->input('contentsId');
        $updateVersionHistoryParam['status'] = 'O'; // O=올드버전

        $currentVersion =explode('.',$request->input('version'));
        $major = ($request->input('changeVersion') == 1)? (int)$currentVersion[0] + 1 : (int)$currentVersion[0];
        $minor = ($request->input('changeVersion') == 2)? (int)$currentVersion[1] + 1 : (int)$currentVersion[1];
        $patch = ($request->input('changeVersion') == 3)? (int)$currentVersion[2] + 1 : (int)$currentVersion[2];
        $version = $major.".".$minor.".".$patch;

        $insertVersionHistoryParam['contents_id'] = $request->input('contentsId');
        $insertVersionHistoryParam['version'] = $version;
        $insertVersionHistoryParam['description'] = $request->input('description');
        $insertVersionHistoryParam['status'] = 'R';  // R=배포

        $updateContentsParam['contents_id'] = $request->input('contentsId');
        $updateContentsParam['version'] = $version;

        try {
            // 버전 히스토리에서 기존R=배포 였던 항목을 O=올드버전으로 변경
            $this->contents->updateVersionHistory(updateParam: $updateVersionHistoryParam,whereStatus: 'R');
            // 버전 히스토리 저장
            $this->contents->insertVersionHistroy($insertVersionHistoryParam);
            // 콘텐츠 정보에서 version 변경
            $this->contents->updateContents($updateContentsParam);
            // 파일 업로드
            $this->fileManage->uploads('exe', 'contentExeFile', request: $request);
        } catch (Exception $error) {
            Log::error($error);
            $errorMessage = '파일 업로드에 실패하였습니다.';
            return redirect()->back()->withErrors($errorMessage);
        }
        return redirect('/admin/contents/manage/exe-file-version/list/'.$request->input('contentsId'));
    }

    /**
     * 실행파일 롤백
     * @param Request $request
     * @return int
     */
    public function processExeFileRollBack(Request $request){

        // 기존 O:올드버전중 최신 버전값을 R로 변경 해당 버전을 콘텐츠 정보에 버전 변경
        $oldVersion = $this->contents->getContentsVersionOne($request->input('versionHistoryId'));
        if(!$oldVersion)
        {
            return 0;
        }

        $updateNewVersionHistoryParam['contents_id'] = $oldVersion->contents_id;
        $updateNewVersionHistoryParam['contents_version_history_id'] = $oldVersion->contents_version_history_id;
        $updateNewVersionHistoryParam['status'] = 'R';

        $updateContentParam['contents_id'] = $oldVersion->contents_id;
        $updateContentParam['version'] = $oldVersion->version;

        $updateVersionHistoryParam['contents_id']=$oldVersion->contents_id;
        $updateVersionHistoryParam['description']=$request->input('description');
        $updateVersionHistoryParam['status']='B'; // R에서 -> B:롤백 변경

        $result = 1;
        try{
            // 콘텐츠 기본 정보 version  올드버전으로 업데이트
            $this->contents->updateContents($updateContentParam);
            // 기존 R:릴리즈 버전은 B:롤백으로 변경
            $this->contents->updateVersionHistory(updateParam: $updateVersionHistoryParam,whereStatus: 'R');
            // 기존 최신 O:올드 버전은 R:배포로 변경
            $this->contents->updateVersionHistory(updateParam: $updateNewVersionHistoryParam,whereStatus: 'O');
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }

        return $result;
    }

}
