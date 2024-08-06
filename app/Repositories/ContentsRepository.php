<?php

namespace App\Repositories;

use App\Models\XrContents;
use App\Models\XrContentsGenre;
use App\Models\XrContentsGenreGroup;
use App\Models\XrContentsResource;
use App\Models\XrContentsScoreType;
use App\Models\XrContentsSensor;
use App\Models\XrContentsText;
use App\Models\XrContentsVersionHistory;
use App\Models\XrDevelopmentalElement;
use App\Models\XrDevelopmentalElementGroup;
use App\Models\XrSensor;
use App\Models\XrTheme;
use App\Models\XrThemeContents;

class ContentsRepository
{
    public function __construct(
        XrContents $xrContents,
        XrContentsGenre $xrContentsGenre,
        XrTheme $xrTheme,
        XrSensor $xrSensor,
        XrDevelopmentalElement $xrDevelopmentalElement,
        XrContentsText $xrContentsText,
        XrContentsScoreType $xrContentsScoreType,
        XrContentsGenreGroup $xrContentsGenreGroup,
        XrContentsSensor $xrContentsSensor,
        XrThemeContents $xrThemeContents,
        XrContentsResource $xrContentsResource,
        XrDevelopmentalElementGroup $xrDevelopmentalElementGroup,
        XrContentsVersionHistory $xrContentsVersionHistory,
    )
    {
        $this->xrContents = $xrContents;
        $this->xrContentsGenre = $xrContentsGenre;
        $this->xrTheme = $xrTheme;
        $this->xrSensor = $xrSensor;
        $this->xrDevelopmentalElement = $xrDevelopmentalElement;
        $this->xrContentsText = $xrContentsText;
        $this->xrContentsScoreType = $xrContentsScoreType;
        $this->xrContentsGenreGroup = $xrContentsGenreGroup;
        $this->xrContentsSensor = $xrContentsSensor;
        $this->xrThemeContents = $xrThemeContents;
        $this->xrContentsResource = $xrContentsResource;
        $this->xrDevelopmentalElementGroup = $xrDevelopmentalElementGroup;
        $this->xrContentsVersionHistory = $xrContentsVersionHistory;
    }

    /**
     * 콘텐츠 장르 최댓값
     * @return mixed
     */
    public function getContentsMaxSort(){
        return $this->xrContents->max('sort');
    }

    /**
     * 콘텐츠 장르 조회
     * @return mixed
     */
    public function getContentsGenreList(string $sortBy='asc', string $where='')
    {
         $query = $this->xrContentsGenre;
                    $query->whereHas('getContentsGenreGroupWithBothTexts', function ($query) use ($where) {
                        $query->select('xr_contents_genre_text.contents_genre_id','text');
                        if ($where) {
                            $query->whereRaw($where);
                        }
                    });
                    $query->orderby('xr_contents_genre.sort', $sortBy);
        return $query->paginate(20);
    }

    /**
     * 테마 리스트 조회
     * @return mixed
     */
    public function getThemeList()
    {
        return $this->xrTheme->get();
    }

    /**
     * 센서 리스트 조회
     * @return mixed
     */
    public function getSensorList()
    {
        return $this->xrSensor->get();
    }

    /**
     * 발달 요소 리스트 조회(한글만)
     * @return mixed
     */
    public function getDevelopmentalElementList()
    {
        return $this->xrDevelopmentalElement
                    ->where('country_id',1)
                    ->get();
    }

    /**
     * 콘텐츠 스코어 타입 리스트 조회
     * @return mixed
     */
    public function getContentsScoreType(){
        return $this->xrContentsScoreType->where('use_flag','Y')->get();
    }

    /**
     * 콘텐츠 이름(한글,영문) 중복 조회
     * @param string $text
     * @return boolean
     */
    public function isContentsName(string $text): bool
    {
        return $this->xrContentsText->where('text_title', $text)->exists();
    }

    /**
     * 콘텐츠 정보 저장
     * @param array $insertParam
     * @return mixed
     */
    public function insertContents(array $insertParam){
        return $this->xrContents->insertGetId($insertParam);
    }

    /**
     * 콘텐츠 정보 수정
     * @param array $updateParam
     * @return mixed
     */
    public function updateContents(array $updateParam){
        return $this->xrContents->where('contents_id',$updateParam['contents_id'])->update($updateParam);
    }

    /**
     * 콘텐츠 정보 삭제
     * @param int $contentsId
     * @return void
     */
    public function deleteContents(int $contentsId){
        $this->xrContents->where('contents_id',$contentsId)->delete();
    }

    /**
     * 콘텐츠 장르 그룹 저장 혹은 수정
     * @param array $insertParam
     * @return void
     */
    public function upsertContentsGenreGroup(array $insertParam){
         $this->xrContentsGenreGroup->upsert($insertParam,['contents_id','contents_genre_id']);
    }

    /**
     * 콘텐츠 장르 삭제
     * @param int $contentsId
     * @param int $contentsGenreId
     * @return void
     */
    public function deleteContentsGenreGroup(int $contentsId=0, int $contentsGenreId=0){
      $query = $this->xrContentsGenreGroup;
        if($contentsId > 0) {
             $query->where('contents_id', $contentsId)->delete();
         } else if($contentsGenreId > 0) {
             $query->where('contents_genre_id', $contentsGenreId)->delete();
         }
    }

    /**
     * 콘텐츠별 센서 저장
     * @param array $insertParam
     * @return void
     */
    public function upsertContentsSensor(array $insertParam){
        $this->xrContentsSensor->upsert($insertParam,['contents_id','sensor_id']);
    }

    /**
     * 콘텐츠 센서 삭제
     * @param int $contentsId
     * @param array $sensor
     * @return void
     */
    public function deleteContentsSensor(int $contentsId, array $sensor){
        $this->xrContentsSensor->where('contents_id', $contentsId)
            ->whereNotIn('sensor_id', $sensor)
            ->delete();
    }

    /**
     * 테마별 컨텐츠 저장 혹은 수정
     * @param array $insertParam
     * @return void
     */
    public function upsertThemeContents(array $insertParam){
        $this->xrThemeContents->upsert($insertParam,['contents_id','theme_id']);
    }

    /**
     * 테마별 컨텐츠 삭제
     * @param int $contentsId
     * @param array $themes
     * @return void
     */
    public function deleteThemeContents(int $contentsId, array $themes){
        $this->xrThemeContents->where('contents_id', $contentsId)
            ->whereNotIn('theme_id', $themes)
            ->delete();
    }

    /**
     * 콘텐츠 국가별 텍스트 저장 혹은 수정
     * @param array $insertParam
     * @return void
     */
    public function upsertContentsText(array $insertParam){
        $this->xrContentsText->upsert($insertParam,['contents_id','country_id']);
    }

    /**
     * 콘텐츠 텍스트 삭제
     * @param int $contentsId
     * @return void
     */
    public function deleteContentsText(int $contentsId){
        $this->xrContentsText->where('contents_id', $contentsId)->delete();
    }

    /**
     * 발달 요소 저장
     * @param array $insertParam
     * @return void
     */
    public function upsertDevelopmentalElement(array $insertParam){
        $this->xrDevelopmentalElementGroup->upsert($insertParam,['contents_id','developmental_element_type_id']);
    }

    /**
     * 발달 요소 삭제
     * @param int $contentsId
     * @param array $element
     * @return void
     */
    public function deleteDevelopmentalElement(int $contentsId, array $element){
        $this->xrDevelopmentalElementGroup->where('contents_id', $contentsId)
            ->whereNotIn('developmental_element_type_id', $element)
            ->delete();
    }

    /**
     * 콘텐츠 리소스(이미지,영상) 저장 혹은 수정
     * @param array $insertParam
     * @return void
     */
    public function upsertContentsResource(array $insertParam){
        $this->xrContentsResource->upsert($insertParam,['contents_id','country_id','sort']);
    }

    /**
     * 콘텐츠 리소스 삭제
     * @param int $contentsId
     * @param int $contentsResourceId
     */
    public function deleteContentsResource(int $contentsId, int $contentsResourceId ){
         $query =$this->xrContentsResource;
         if($contentsId > 0) {
             $query->where('contents_id', $contentsId)->delete();
         } else if($contentsResourceId > 0) {
             $query->where('contents_resource_id', $contentsResourceId)->delete();
         }
    }

    /**
     * 콘텐츠 리스트
     * @return mixed
     */
    public function getContentsList(array $where=[],string $sortBy='desc', string $createdAtSortBy='desc')
    {
        $query = $this->xrContents->select('contents_id','version','f_path','sort','sort_by','created_at')
             ->orderby('sort',$sortBy);

        if ($createdAtSortBy) {
            $query->orderBy('created_at', $createdAtSortBy);
        }

        if (isset($where['text'])) { // 장르 검색
            $query->whereHas('getContentsGenreGroup', function ($query) use ($where) {
                $query->select('xr_contents_genre.contents_genre_id', 'xr_contents_genre_group.contents_id')
                    ->whereHas('getContentsGenreGroupTexts' , function ($query) use ($where) {
                        $query->select('xr_contents_genre_text.contents_genre_id', 'text')
                            ->where('country_id', 1)
                            ->whereRaw($where['text']);
                    });
            });
        } else {
            $query->with(['getContentsGenreGroup' => function ($query) {
                $query->select('xr_contents_genre.contents_genre_id', 'xr_contents_genre_group.contents_id');
                $query->with(['getContentsGenreGroupTexts' => function ($query){
                    $query->select('xr_contents_genre_text.contents_genre_id', 'text')
                        ->where('country_id', 1);
                }]);
            }]);
        }

        if(isset($where['sensor_name'])) { // 센서명 검색
            $query->whereHas('getContentsSensor', function ($query) use ($where) {
                $query->select('xr_sensor.sensor_id', 'sensor_name')
                        ->orderby('sensor_priority','asc')
                        ->whereRaw($where['sensor_name']);
            });
        } else {
            $query->with(['getContentsSensor' => function ($query) {
                $query->select('xr_sensor.sensor_id', 'sensor_name')
                        ->orderby('sensor_priority','asc');
            }]);
        }

        if(isset($where['theme_name'])) { // 테마명 검색
            $query->whereHas('getThemeContents',function ($query) use($where) {
                $query->select('xr_theme.theme_id', 'theme_name')->whereRaw($where['theme_name']);
            });
        } else {
            $query->with(['getThemeContents' => function ($query) {
                $query->select('xr_theme.theme_id', 'theme_name');
            }]);
        }

        if(isset($where['text_title'])) { // 컨텐츠명 검색
            $query->whereHas('getContentsText' , function ($query) use($where) {
                $query->select('contents_id', 'country_id', 'text_title', 'text_desc');
                    $query->whereRaw($where['text_title']);
            });
        } else {
            $query->with(['getContentsText' => function ($query) {
                $query->select('contents_id', 'country_id', 'text_title', 'text_desc');
            }]);
        }

        return $query->paginate(20);
    }

    /**
     * 특정 콘텐츠 정보 조회
     * @param int $contentsId
     * @param string $where
     * @return mixed
     */
    public function getContentsInfo(int $contentsId,string $where='')
    {
        $query = $this->xrContents->select('contents_id','contents_code','f_exe_name','f_path','contents_score_type_id','version','sort','sort_by');

        if($contentsId > 0)
        {
            $query->where('contents_id',$contentsId)
            ->with(['getContentsGenreGroup' => function ($query) {
                $query->select('xr_contents_genre.contents_genre_id');
            }])

            ->with(['getContentsSensor'=> function ($query)  {
                $query->select('xr_sensor.sensor_id','sensor_name')
                    ->orderby('sensor_priority','asc');
            }])

            ->with(['getThemeContents'=> function ($query) {
                $query->select('xr_theme.theme_id','theme_name');
            }])

            ->with(['getContentsText'=> function ($query) {
                $query->select('contents_id','country_id','text_title','text_desc');
            }])

            ->with(['getContentsResource'=> function ($query) {
                $query->select('contents_resource_id','contents_id','country_id','f_id','f_format','f_path','sort')
                    ->where('use_flag','Y')
                    ->orderby('country_id','asc')
                    ->orderby('sort','asc');
            }])

            ->with(['getDevelopmentalElementGroup'=> function ($query) {
                $query->select('xr_developmental_element.type_id','text')
                    ->where('xr_developmental_element.country_id',1);
            }]);
        }

        if($where)
        {
            $query->whereRaw($where);
        }

        return $query->first();
    }

    /**
     * 콘텐츠 버전 히스토리 리스트
     * @param int $contentsId
     * @return mixed
     */
    public function getContentsVersionList(int $contentsId){
        return $this->xrContentsVersionHistory->where('contents_id',$contentsId)
            ->orderby('updated_at','desc')
            ->orderby('contents_version_history_id','desc')->paginate(20);
    }

    /**
     * 특정 콘텐츠 버전 히스토리 조회
     * @param int $versionHistoryId
     * @return mixed
     */
    public function getContentsVersionOne(int $versionHistoryId){
        return $this->xrContentsVersionHistory
            ->where([
                ['contents_version_history_id','<',$versionHistoryId],
                ['status','O']
            ])
            ->orderby('contents_version_history_id','desc')
            ->first();
    }

    /**
     * 콘텐츠 버전 히스토리 저장
     * @param array $insertParam
     * @return void
     */
    public function insertVersionHistroy(array $insertParam){
        $this->xrContentsVersionHistory->insert($insertParam);
    }

    /**
     * 콘텐츠 버전 히스토리 수정
     * @param array $updateParam
     * @param string $whereStatus
     * @return void
     */
    public function updateVersionHistory(array $updateParam,string $whereStatus){
        $query = $this->xrContentsVersionHistory->where([
                ['contents_id',$updateParam['contents_id']],
                ['status',$whereStatus]
        ]);
        if(isset($updateParam['contents_version_history_id'])){
            $query->where('contents_version_history_id',$updateParam['contents_version_history_id']);
        }
        $query->update($updateParam);
    }

    /**
     * 콘텐츠 버전 히스토리 삭제
     * @param int $contentsId
     * @return void
     */
    public function deleteVersionHistory(int $contentsId){
        $this->xrContentsVersionHistory->where('contents_id',$contentsId)->delete();
    }
}
