<?php

namespace App\Services;

use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileManageService
{
    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => env('NCLOUD_OBJECT_STORAGE_REGION'),
            'endpoint' => env('NCLOUD_OBJECT_STORAGE_ENDPOINT'),
            'credentials' => [
                'key'    => env('NCLOUD_OBJECT_STORAGE_KEY'),
                'secret' => env('NCLOUD_OBJECT_STORAGE_SECRET'),
            ],
            'use_path_style_endpoint' => true,
        ]);
        $this->code = app(MakeRandCode::class);
    }

    /**
     * 파일 업로드
     * @param string $type
     * @param string $resourceName
     * @param Request $request
     * @param string $foldName
     * @return array
     */
    public function uploads(string $type,string $resourceName,Request $request,string $foldName=''):array
    {
        $nCloud = Storage::disk('ncloud');
        try {
            if ($type === 'exe') {  // 실행파일
                $file = $request->file($resourceName);
                $path = 'contents/' . $request->input('contentsEnName') . '/release/' . $request->input('version');
                $fileName = $file->getClientOriginalName();
            } else if ($type === 'resource') { // 리소스
                $file = $request->file($resourceName);
                $path = 'contents/' . $request->input('contentsEnName') . '/resource';
                $fileName = $file->hashName();
            } else if ($type === 'banner') { // 장르 배너
                $file = $request->file($resourceName);
                $path = 'genre/' . $foldName;
                if ($file->getClientOriginalExtension() === 'json') { // json 일때는 txt 로 자동변경 되어 확장자 변경
                    $fileName = $this->code->getRandCodeWithLoweCase(30) . ".json";
                } else {
                    $fileName = $file->hashName();
                }
            } else if ($type === 'sensorIcon') {
                $file = $request->file($resourceName);
                $path = 'sensor/' . $foldName;
                $fileName = $file->hashName();
            } else if ($type === 'themeResource') {
                $file = $request->file($resourceName);
                $path = 'theme/' . $foldName;
                $fileName = $file->hashName();
            }

            // 디렉토리 생성
            if (!$nCloud->exists($path)) {
                $nCloud->makeDirectory($path);
            }
            // 스토리지 저장
            $nCloud->putFileAs($path, $file,$fileName,'public');

        } catch(Exception $error) {
            Log::error($error);
        }
        $files['path']              =   $path;
        $files['name']              =   ($file->getClientOriginalExtension() === 'json')? $fileName : $file->hashName();
        $files['original_name']     =   $file->getClientOriginalName();
        $files['file_format']       =   $file->getClientMimeType();
        return  $files;
    }

    /**
     * 해당 디렉토리 삭제
     * @param string $path
     * @return bool
     */
    public function deleteDirectory(string $path){
        $nCloud = Storage::disk('ncloud');
        if (!$nCloud->exists($path)) {
            return false;
        }
        return $nCloud->deleteDirectory($path);
    }

    /**
     * 파일 삭제
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path){
        $nCloud = Storage::disk('ncloud');
        if (!$nCloud->exists($path)) {
            return false;
        }
        return $nCloud->delete($path);
    }

    /**
     * 파일 다운로드 임시 url 발급
     * @param string $path
     * @param string $expiration
     * @return int|string
     */
    public function generateTemporaryDownLoadUrl(string $path,string $expiration ='+30 minutes')
    {
        try {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => env('NCLOUD_OBJECT_STORAGE_BUCKET'),
                'Key' => $path,
            ]);

            $request = $this->s3Client->createPresignedRequest($cmd, $expiration);
            $result =  (string) $request->getUri();
        } catch(Exception $err){
            Log::info($err);
            $result = -1;
        }
        return $result;
    }

    /**
     * 오브젝트 스토리지 버킷 CORS 권한 확인
     * @return \Aws\Result
     */
    public function getCorsOption(){
          $cmd = $this->s3Client->getBucketCors([
                'Bucket' =>  env('NCLOUD_OBJECT_STORAGE_BUCKET'),
            ]);
        return $cmd;
    }

    /**
     * 오브젝트 스토리지 버킷 CORS 권한 주기
     * @return void
     */
    public function setCorsOption(){
        $corsRules = [
            [
                'AllowedHeaders' => ['*'],
                'AllowedMethods' => ['GET', 'PUT', 'POST'],
                'AllowedOrigins' => ['https://aifitplay.com','https://www.aifitplay.com','http://localhost'],
                'ExposeHeaders'  => [],
                'MaxAgeSeconds'  => 3000,
            ],
        ];

        $this->s3Client->putBucketCors([
            'Bucket' => env('NCLOUD_OBJECT_STORAGE_BUCKET'),
            'CORSConfiguration' => [
                'CORSRules' => $corsRules,
            ],
        ]);
    }

}
