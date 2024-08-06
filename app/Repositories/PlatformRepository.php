<?php

namespace App\Repositories;

use App\Models\XrContents;
use App\Models\XrContentsGenre;
use App\Models\XrContentsGenreGroup;
use App\Models\XrContentsGenreText;
use App\Models\XrContentsResource;
use App\Models\XrContentsScoreType;
use App\Models\XrContentsSensor;
use App\Models\XrContentsText;
use App\Models\XrContentsVersionHistory;
use App\Models\XrDevelopmentalElement;
use App\Models\XrDevelopmentalElementGroup;
use App\Models\XrProduct;
use App\Models\XrSensor;
use App\Models\XrSensorGroup;
use App\Models\XrSensorType;
use App\Models\XrTheme;
use App\Models\XrThemeContents;
use App\Models\XrThemeResource;

class PlatformRepository
{
    public function __construct(
        XrContents                  $xrContents,
        XrContentsGenre             $xrContentsGenre,
        XrTheme                     $xrTheme,
        XrSensor                    $xrSensor,
        XrDevelopmentalElement      $xrDevelopmentalElement,
        XrContentsText              $xrContentsText,
        XrContentsScoreType         $xrContentsScoreType,
        XrContentsGenreGroup        $xrContentsGenreGroup,
        XrContentsSensor            $xrContentsSensor,
        XrThemeContents             $xrThemeContents,
        XrContentsResource          $xrContentsResource,
        XrDevelopmentalElementGroup $xrDevelopmentalElementGroup,
        XrContentsGenreText         $xrContentsGenreText,
        XrSensorType                $xrSensorType,
        XrProduct                   $xrProduct,
        XrSensorGroup               $xrSensorGroup,
        XrThemeResource             $xrThemeResource,
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
        $this->xrContentsGenreText = $xrContentsGenreText;
        $this->xrSensorType = $xrSensorType;
        $this->xrProduct = $xrProduct;
        $this->xrSensorGroup = $xrSensorGroup;
        $this->xrThemeResource = $xrThemeResource;
    }

    /**
     * 특정 장르 조회
     * @param int $genreId
     * @param string $where
     * @return mixed
     */
    public function getContentsGenreInfo(int $genreId = 0, string $where = '')
    {
        $query = $this->xrContentsGenre->with('getContentsGenreGroupWithBothTexts');

        if ($genreId > 0) {
            $query->where('contents_genre_id', $genreId);
        }

        if ($where) {
            $query->whereRaw($where);
        }

        return $query->first();
    }

    /**
     * 특정 장르 존재여부 조회
     * @param string $where
     * @return bool
     */
    public function isContentsGenreText(string $where): bool
    {
        return $this->xrContentsGenreText->whereRaw($where)->exists();
    }

    /**
     * 장르 정렬 최댓값 조회
     * @return mixed
     */
    public function getContentsGenreMaxSort()
    {
        return $this->xrContentsGenre->max('sort');
    }

    /**
     * 장르 정보 수정
     * @param array $updateParam
     * @return void
     */
    public function updateContentsGenreInfo(array $updateParam)
    {
        $this->xrContentsGenre->where('contents_genre_id', $updateParam['contents_genre_id'])->update($updateParam);
    }

    /**
     * 장르 삭제
     * @param int $contentsGenreId
     * @return void
     */
    public function deleteContentsGenreInfo(int $contentsGenreId)
    {
        $this->xrContentsGenre->where('contents_genre_id', $contentsGenreId)->delete();
    }

    /**
     * 장르 등록
     * @param array $insertParam
     * @return mixed
     */
    public function insertContentsGenreInfo(array $insertParam)
    {
        return $this->xrContentsGenre->insertGetId($insertParam);
    }

    /**
     * 장르 텍스트 등록
     * @param array $insertParam
     * @return void
     */
    public function insertContentsGenreTextInfo(array $insertParam)
    {
        $this->xrContentsGenreText->insert($insertParam);
    }

    /**
     * 장르 텍스트 수정
     * @param array $updateParam
     * @return void
     */
    public function updateContentsGenreTextInfo(array $updateParam)
    {
        $this->xrContentsGenreText
            ->where([
                ['contents_genre_id', '=', $updateParam['contents_genre_id']],
                ['country_id', '=', $updateParam['country_id']]
            ])->update($updateParam);
    }

    /**
     * 장르 텍스트 삭제
     * @param int $contentsGenreId
     * @return void
     */
    public function deleteContentsGenreTextInfo(int $contentsGenreId)
    {
        $this->xrContentsGenreText->where('contents_genre_id', $contentsGenreId)->delete();
    }

    /**
     * 센서 타입 리스트
     * @return mixed
     */
    public function getSensorTypeList()
    {
        return $this->xrSensorType
            ->with('getSensorList')
            ->get();
    }

    /**
     * 상품과 그에 따른 센서 리스트 조회
     * @param array $where
     * @return mixed
     */
    public function getProductList(array $where=[]){
        $query = $this->xrProduct->orderby('product_name','asc');

        if(isset($where['product_name']))
        {
            $query->whereRaw($where['product_name']);
        }

        if(isset($where['sensor_name'])){
            $query->whereHas('getSensorGroupWithSensorInfo',function ($query) use($where) {
                $query->whereRaw($where['sensor_name']);
            });
        } else {
            $query->with('getSensorGroupWithSensorInfo');
        }

        return $query->paginate(20);
    }

    /**
     * 상품 이름 존재여부 조회
     * @param string $productName
     * @return mixed
     */
    public function isProductName(string $productName)
    {
        return $this->xrProduct->where('product_name', $productName)->exists();
    }

    /**
     * 상품 정보 저장
     * @param array $insertParam
     * @return mixed
     */
    public function insertProductInfo(array $insertParam)
    {
        return $this->xrProduct->insertGetId($insertParam);
    }

    /**
     * 특정 상품 정보 조회
     * @param int $productId
     * @return mixed
     */
    public function getProductInfo(int $productId){
        return $this->xrProduct->where('product_id',$productId)
                ->with('getSensorGroupWithSensorInfo')
                ->first();
    }

    /**
     * 상품 정보 수정
     * @param array $updateParam
     * @return void
     */
    public function updateProductInfo(array $updateParam)
    {
        $this->xrProduct->where('product_id', $updateParam['product_id'])->update($updateParam);
    }

    /**
     * 상품 정보 삭제
     * @param int $productId
     * @return void
     */
    public function deleteProductInfo(int $productId)
    {
        $this->xrProduct->where('product_id', $productId)->delete();
    }

    /**
     * 센서 그룹 저장 혹은 수정
     * @param array $insertParam
     * @return void
     */
    public function upsertSensorGroupInfo(array $insertParam)
    {
        $this->xrSensorGroup->upsert($insertParam,['sensor_id','product_id'],['sensor_id','product_id']);
    }

    /**
     * 센서 그룹 삭제
     * @param int $productId
     * @param array $sensor
     * @return void
     */
    public function deleteSensorGroupInfo(int $productId,array $sensor)
    {
        $this->xrSensorGroup->where('product_id', $productId)
            ->whereNotIn('sensor_id', $sensor)
            ->delete();
    }

    /**
     * 센서 리스트 조회
     * @param array $where
     * @return mixed
     */
    public function getSensorList(array $where){
        $query= $this->xrSensor->select('*');

        if(isset($where['sensor_code'])){
            $query->whereRaw($where['sensor_code']);
        } else if(isset($where['sensor_name'])) {
            $query->whereRaw($where['sensor_name']);
        }

        if(isset($where['sensor_type']))
        {
            $query->whereHas('getSensorTypeInfo',function ($query) use($where) {
                $query->whereRaw($where['sensor_type']);
            });
        }else{
            $query->with('getSensorTypeInfo');
        }

        $query->orderby('sensor_type_id','asc');

        return $query->paginate(20);
    }

    /**
     * 센서 이름 존재 여부 체크
     * @param string $sensorName
     * @return mixed
     */
    public function inSensorName(string $sensorName){
        return $this->xrSensor->where('sensor_name',$sensorName)->exists();
    }

    /**
     * 특정 센서 조회
     * @param int $sensorId
     * @return mixed
     */
    public function getSensorInfo(int $sensorId){
        return $this->xrSensor->where('sensor_id',$sensorId)->first();
    }

    /**
     * 센서 저장
     * @param array $insertParam
     * @return void
     */
    public function insertSensorInfo(array $insertParam)
    {
        $this->xrSensor->insert($insertParam);
    }

    /**
     * 센서 수정
     * @param array $updateParam
     * @return void
     */
    public function updateSensorInfo(array $updateParam){
        $this->xrSensor->where('sensor_id',$updateParam['sensor_id'])->update($updateParam);
    }

    /**
     * 센서 삭제
     * @param int $sensorId
     * @return void
     */
    public function deleteSensorInfo(int $sensorId){
        $this->xrSensor->where('sensor_id',$sensorId)->delete();
    }

    /**
     * 센서 그룹 삭제(특정 센서 삭제시)
     * @param int $sensorId
     * @return void
     */
    public function deleteSensorGroupInfoFromSensorId(int $sensorId)
    {
        $this->xrSensorGroup->where('sensor_id',$sensorId)->delete();
    }

    /**
     * 콘텐츠 센서 삭제(특정 센서 삭제시)
     * @param int $sensorId
     * @return void
     */
    public function deleteContentsSensorInfoFromSensorId(int $sensorId){
        $this->xrContentsSensor->where('sensor_id',$sensorId)->delete();
    }

    /**
     * 테마 리스트 조회
     * @param array $where
     * @return mixed
     */
    public function getThemeList(array $where){
        $query = $this->xrTheme;
            if(isset($where['theme_name'])){
                $query->whereRaw($where['theme_name']);
            }
        $query->orderby('theme_name','asc');
         return $query->paginate(20);
    }

    /**
     * 특정 테마 조회
     * @param int $themeId
     * @return mixed
     */
    public function getThemeInfo(int $themeId){
        return $this->xrTheme->where('theme_id',$themeId)->first();
    }

    /**
     * 테마 이름 존재 여부 체크
     * @param string $themeName
     * @return mixed
     */
    public function isThemeName(string $themeName){
        return $this->xrTheme->where('theme_name',$themeName)->exists();
    }

    /**
     * 테마 정보 저장
     * @param array $insertParam
     * @return int
     */
    public function insertThemeInfo(array $insertParam){
        return $this->xrTheme->insertGetId($insertParam);
    }

    /**
     * 테마 리소스 저장
     * @param array $insertParam
     * @return void
     */
    public function insertThemeResoruce(array $insertParam){
        $this->xrThemeResource->insert($insertParam);
    }

}
