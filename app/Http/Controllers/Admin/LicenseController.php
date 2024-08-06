<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\LicenseRepository;
use App\Services\MakeRandCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LicenseController extends Controller
{
    public function __construct(LicenseRepository $license)
    {
        $this->license = $license;
        $this->code = app(MakeRandCode::class);
    }

    public function moveLicenseForm()
    {
        return view('admin/licenseForm');
    }

    /**
     * 라이센스 코드 확인
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isLicenseCode(Request $request)
    {
        $licenseCode = $request->input('licenseCode');
        if(!$licenseCode) {
            $result['code']  =  -2; // 키값이 없을때
            return response()->json($result,200,[],JSON_UNESCAPED_UNICODE);
        }

        $licenseInfo = $this->license->isLicenseCode($licenseCode);
        if(!$licenseInfo){
            $result['code']  = -3; // 해당 라이센스 없음
        } else if($licenseInfo->status === 'Y'){
            $result['code']  = 1; // 이미 사용중
        } else if($licenseInfo->status === 'N') {
            $result['code']  = -1; // 사용 중지
        } else if($licenseInfo->status === 'W') {
            $result['code']  = 0;  // 사용가능
            $result['licenseId'] = $licenseInfo->license_id;
            $result['licenseType'] = $licenseInfo->license_type;
        }
        return response()->json($result,200,[],JSON_UNESCAPED_UNICODE);
    }

    /**
     * @todo:라이센스 발급 페이지 , 라이센스 관리 페이지 필요할 듯
     * 라이센스 코드 발급
     * @return void
     */
    public function makeLicenseCode(Request $request)
    {
        // 라이센스 코드 생성
        $licenseCode = $this->code->getHyphenRandCode(4,4);

        // 라이센스 코드 저장
        $insertParam['license_code']=$licenseCode;
        $insertParam['period_id']=$request->input('period_id');
        $insertParam['license_type']=$request->input('type',1);
        try {
            $this->license->licenseCodeInsert($insertParam);
        } catch(Exception $err){
            Log::info($err);
        }
    }

}
