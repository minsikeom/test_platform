<?php

namespace App\Repositories;

use App\Models\XrAgency;
use App\Models\XrCountry;
use App\Models\XrEmailVerification;
use App\Models\XrGroup;
use App\Models\XrLicense;
use App\Models\XrUser;

class SignUpRepository
{
    public function __construct(
        XrUser $xrUser,
        XrEmailVerification $xrEmailVerification,
        XrAgency $xrAgency,
        XrLicense $xrLicense,
        XrCountry $xrCountry,
        XrGroup $xrGroup
    )
    {
        $this->xrUser = $xrUser;
        $this->xrEmailVerification = $xrEmailVerification;
        $this->xrAgency = $xrAgency;
        $this->xrLicense = $xrLicense;
        $this->xrCountry = $xrCountry;
        $this->xrGroup = $xrGroup;
    }

    /**
     * 아이디 중복 체크
     * @param string $loginId
     * @return mixed
     */
    public function isLoginId(string $loginId)
    {
        return $this->xrUser->where('login_id',$loginId)->exists();
    }

    /**
     * 유저 회원 가입
     * @param array $insertParam
     * @return int
     */
    public function insertUser(array $insertParam)
    {
        return $this->xrUser->insertGetId($insertParam);
    }

    /**
     * 단체 가입
     * @param array $insertParam
     * @return int
     */
    public function insertAgency(array $insertParam)
    {
        return $this->xrAgency->insertGetId($insertParam);
    }

    /**
     * 그룹 가입
     * @param array $insertParam
     * @return int
     */
    public function insertGroup(array $insertParam): int
    {
        return $this->xrGroup->insertGetId($insertParam);
    }

    /**
     * 이메일 인증코드 저장
     * @param array $insertParam
     * @return void
     */
    public function insertVerificationCode(array $insertParam)
    {
       $this->xrEmailVerification->insert($insertParam);
    }

    /**
     * 이메일 인증 코드 조회
     * @param string $verificationCode
     * @return mixed
     */
    public function getVerificationCode(string $verificationCode): mixed
    {
       return $this->xrEmailVerification->where([
           ['email_verification_code', '=', $verificationCode],
           ['email_verification_flag', '=', 'N'],
       ])->first();
    }

    /**
     * 이메일 인증 업데이트
     * @param array $updateParam
     * @return void
     */
    public function updateVerificationCode(array $updateParam): void
    {
        $this->xrEmailVerification->where('email_verification_id',$updateParam['email_verification_id'])->update($updateParam);
    }

    /**
     * 회원 가입 후 이메일 인증 user_id 업데이트
     * @param array $updateParam
     * @return void
     */
    public function updateVerificationUserId(array $updateParam)
    {
        $this->xrEmailVerification->where('email_verification_code',$updateParam['email_verification_code'])->update($updateParam);
    }

    /**
     * 특정 단체 그룹 정보 가져오기
     * @param int $agencyId
     * @param int|null $groupId
     * @return mixed
     */
    public function getAgencyWithGroupInfo(int $agencyId,?int $groupId)
    {
        return  $this->xrAgency->select('agency_id','agency_name')
                ->where('agency_id',$agencyId)
                ->with(['getAgencyWithGroupInfo' => function ($query) use($groupId) {
                    $query->select('xr_user.agency_id','xr_group.group_id','xr_group.group_name');
                    $query->whereIn('xr_user.ut_code',['20','21']);
                    $query->where('xr_user.use_flag','Y');
                        if($groupId){
                            $query->where('xr_group.group_id',$groupId);
                        }
                }])
                ->first();
    }

    /**
     * 회원 가입 후 라이센스 user_id 업데이트 및 상태 사용중으로 변경
     * @param array $updateParam
     * @return void
     */
    public function updateLicenseUserId(array $updateParam){
        $this->xrLicense->where('license_id',$updateParam['license_id'])->update($updateParam);
    }

    /**
     * 사용중인 국가 코드 가져오기
     * @return mixed
     */
    public function getCountryInfo(){
       return $this->xrCountry->where('use_flag','Y')->get();
    }

}
