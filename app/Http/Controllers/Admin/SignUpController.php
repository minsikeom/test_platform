<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SignUpMail;
use App\Repositories\SignUpRepository;
use App\Repositories\UserRepository;
use App\Services\MakeRandCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SignUpController extends Controller
{
    public function __construct(UserRepository $user, SignUpRepository $signUp)
    {
        $this->user = $user;
        $this->signUp = $signUp;
        $this->code = app(MakeRandCode::class);
    }

    public function moveSignUpForm(Request $request)
    {
        $data = [];
        $data['licenseId']= $request->input('licenseId');
        // 1= 단체 BtoG, 2= 개인 BtoC , 3= 단체에 속한 개인회원
        $data['licenseType']= ($request->input('licenseType'))?? 3;
        $data['countryInfo'] = $this->signUp->getCountryInfo();
        if($request->input('agencyId') && $request->input('groupId') ) //  단체 그룹에 속한 개인 회원
        {
            $data['agencyId'] = $request->input('agencyId');
            $data['groupId']= $request->input('groupId');
            // 그룹 정보
            $data['agency_info']= $this->signUp->getAgencyWithGroupInfo(
                agencyId: $request->input('agencyId'),
                groupId: $request->input('groupId')
            );

            if(count($data['agency_info']->getAgencyWithGroupInfo) < 1){
                echo "<script>alert('잘못된 접속경로 입니다.')</script>";
                exit;
            }
        }

        return view('admin/signUpForm',$data);
    }

    /**
     * 중복 아이디 체크
     * @param Request $request
     * @return int
     */
    public function isLoginId(Request $request)
    {
        $loginId = $request->input('loginId');
        if(!$loginId){
            return -1; // 키값이 없을때
        } else if($this->signUp->isLoginId($loginId)){
            return 1; // 중복 아이디
        } else {
            return 0; // 사용 가능 아이디
        }
    }

    /**
     * 회원 가입 (단체 생성도 같이 )
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function registerUser(Request $request)
    {
        // 기관 회원 or b2c
        if (in_array($request->input('licenseType'), [1, 2])){
            if($request->input('licenseType') == 1) {
                $insertAgencyParam['agency_name'] = $request->input('agencyName');
                $insertAgencyParam['use_flag'] = 'Y';
                try {
                    $agencyId = $this->signUp->insertAgency($insertAgencyParam);
                } catch (Exception $error) {
                    Log::error($error);
                    return back()->withInput()->withErrors('msg', '오류가 발생 되었습니다. 다시 시도해주세요');
                }

                // 그룹
                $insertGroupParam['group_name'] = $request->input('agencyName') . "기본 그룹";
                $insertGroupParam['group_code'] = 'PS';
                try {
                    $groupId = $this->signUp->insertGroup($insertGroupParam);
                } catch (Exception $error) {
                    Log::error($error);
                    return back()->withInput()->withErrors('msg', '오류가 발생 되었습니다. 다시 시도해주세요');
                }
            }

            // 기관 혹은 b2c 회원
            $insertUserParam['login_id'] = $request->input('loginId');
            $insertUserParam['password'] = bcrypt($request->input('password'));
            $insertUserParam['agency_id'] = ($agencyId)??null;
            $insertUserParam['group_id'] = ($groupId)??null;
            $insertUserParam['name'] = $request->input('userName');
            $insertUserParam['nick_name'] = $request->input('nickName');
            $insertUserParam['phone_num'] = $request->input('phoneNum');
            $insertUserParam['email'] = $request->input('email');
            $insertUserParam['birthday'] = $request->input('birthday');
            $insertUserParam['verification_flag'] = 'N';
            $insertUserParam['sort'] = 0;
            $insertUserParam['ut_code'] = ($request->input('licenseType') == 1)? '20':'10';
            try {
                $userId = $this->signUp->insertUser($insertUserParam);
            } catch (Exception $error) {
                Log::error($error);
                return back()->withInput()->withErrors('msg', '오류가 발생 되었습니다. 다시 시도해주세요');
            }

            // license 업데이트 상태-> 사용중  , user_id 업데이트
            $updateLicenseParam['user_id'] = $userId;
            $updateLicenseParam['license_id'] = $request->input('licenseId');
            $updateLicenseParam['status'] = 'Y';
            $this->signUp->updateLicenseUserId($updateLicenseParam);

            // 이메일 인증 업데이트
            $updateVerificationParam['user_id'] = $userId;
            $updateVerificationParam['email_verification_code'] = $request->input('emailVerificationCode');
            $this->signUp->updateVerificationUserId($updateVerificationParam);

        } else { // 그룹소속 회원 가입

            // 회원
            $insertUserParam['login_id'] = $request->input('loginId');
            $insertUserParam['password'] = bcrypt($request->input('password'));
            $insertUserParam['agency_id'] = $request->input('agencyId');
            $insertUserParam['group_id'] = $request->input('groupId');
            $insertUserParam['name'] = $request->input('userName');
            $insertUserParam['nick_name'] = $request->input('nickName');
            $insertUserParam['phone_num'] = $request->input('phoneNum');
            $insertUserParam['email'] = $request->input('email');
            $insertUserParam['birthday'] = $request->input('birthday');
            // 승인시 마지막 정렬값 변경 인설트시 0으로
            $insertUserParam['sort'] = 0;
            $insertUserParam['verification_flag'] = 'W';
            $insertUserParam['ut_code'] = '22';
            try {
                $this->signUp->insertUser($insertUserParam);
            } catch (Exception $error) {
                Log::error($error);
                return back()->withInput()->withErrors('msg', '오류가 발생 되었습니다. 다시 시도해주세요');
            }
        }

        echo "<script>alert('등록 완료 되었습니다.');</script>";
        exit;
    }

    // 이메일 인증 이메일 보내기
    public function sendEmail(Request $request)
    {
        $name = $request->input('name','');
        $lang = $request->input('lang','en');
        $to   = $request->input('to','');

        if(!$to) return 0;

        //난수 코드 생성
        $verificationCode = $this->code->getRandCode(6);

        $data = [
            'title' => ($lang === 'en')? 'Mail Verification Code' : '메일 인증코드 ',
            'name' => $name,
            'verificationCode' => $verificationCode,
        ];

        try {
            Mail::to($to)->send(new SignUpMail($data));
            $insertParam['email_verification_code'] = $verificationCode;
            $this->signUp->insertVerificationCode($insertParam);
        } catch (Exception $error) {
            Log::error($error);
            return 0;
        }
        return 1;
    }

    /**
     * 이메일 코드 인증
     * @param Request $request
     * @return int
     */
    public function isEmailCode(Request $request)
    {
        $emailVerificationCode = $request->input('emailVerificationCode');
        $verificationInfo = $this->signUp->getVerificationCode($emailVerificationCode);
        if(!$verificationInfo)
        {
            return 0;  // 해당 코드값 없음
        } else if($verificationInfo->verification_flag === 'Y'){
            return '이미 사용된 코드 입니다.';
        }

        // 이메일 인증 완료로 업데이트
        $updateParam['email_verification_id'] = $verificationInfo->email_verification_id;
        $updateParam['email_verification_flag'] = 'Y';
        try {
            $this->signUp->updateVerificationCode($updateParam);
        } catch (Exception $error) {
            Log::error($error);
            return -1;  // 에러
        }
        return 1; // 인증 완료
    }

}
