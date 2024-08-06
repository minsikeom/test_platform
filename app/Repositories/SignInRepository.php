<?php

namespace App\Repositories;

use App\Models\XrAgency;
use App\Models\XrEmailVerification;
use App\Models\XrLicense;
use App\Models\XrToken;
use App\Models\XrUser;

class SignInRepository
{
    public function __construct(
        XrUser $xrUser,
        XrEmailVerification $xrEmailVerification,
        XrAgency $xrAgency,
        XrLicense $xrLicense,
        XrToken $xrToken,
    )
    {
        $this->xrUser = $xrUser;
        $this->xrEmailVerification = $xrEmailVerification;
        $this->xrAgency = $xrAgency;
        $this->xrLicense = $xrLicense;
        $this->xrToken = $xrToken;
    }

    /**
     * 유저 정보 조회
     * @param string $userId
     * @return XrUser|null
     */
    public function getUserInfo(string $userId): ?XrUser
    {
        return $this->xrUser->where('login_id',$userId)->first();
    }

    /**
     * 토큰 체크
     * @param string $loginId
     * @return bool
     */
    public function isToken(string $loginId): bool
    {
         return $this->xrToken->where('login_Id',$loginId)->exists();
    }

    /**
     * 토큰 생성
     * @param array $insertParam
     * @return void
     */
    public function makeToken(array $insertParam)
    {
        $this->xrToken->insert($insertParam);
    }

}
