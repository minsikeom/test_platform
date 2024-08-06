<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\XrUser;
use App\Services\FileManageService;
use App\Services\MakeRandCode;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class TestController extends Controller
{
    public function __construct()
    {
        $this->code = app(MakeRandCode::class);
        $this->fileManage = app(FileManageService::class);
    }

    public function excelForm(){
//        $this->fileManage->setCorsOption();
//        echo $this->fileManage->getCorsOption();
//        FastExcel::data([
//            ['name' => 'John', 'email' => 'john@example.com'],
//            ['name' => 'Jane', 'email' => 'jane@example.com'],
//        ])->export('file.xlsx');
//        return view('admin.test');
    }

    public function test(Request $request){
        /*
        ini_set('memory_limit', '-1');
        echo $cryptKey = $this->code->getRandCode(12);  //$this->code->getRandCode(15);  //
        echo "<br>";
        echo $num = '111A-11B1-1C11-D111';
        echo "<br>";
        echo "암호화:";
        echo '<br>';
        echo $cryCode =  Crypt::encryptString($num);
        echo "<br>";
        echo "복호화";
        echo "<br>";
        echo  Crypt::decryptString($cryCode);
        echo "<br>";
        */
    }


    /**
     * 시리얼 암호화
     * @param string $cryptKey
     * @return void
     */
    public function getEncryptCode(string $cryptKey)
    {
        $cryptKey = $this->code->getRandCode(12);
        $serialCode = ''; // DB에 있는 시리얼 코드 가져와서 복호화
        try{
            Crypt::encryptString($serialCode);
            // pc_contents에 serial_code 업데이트
            // $crypt_key 테이블에 $cryptKey 값 업데이트
        } catch (DecryptException $e) {
            Log::info('에러 발생'.$e);
        }
    }

    /**
     * 복호화
     * @param string $code
     * @param string $cryptKey
     * @return void
     */
    public function getSerialCode(string $code,string $cryptKey)
    {
        if(!$cryptKey){
            // db 조회 해서 없으면 다시 암호화 하라고 보냄
        }
        try{
            Crypt::decryptString($code);
        } catch (DecryptException $e) {
            Log::info('에러 발생'.$e);
        }
    }

}
