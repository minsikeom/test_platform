<?php

namespace App\Repositories;

use App\Models\XrLicense;

class LicenseRepository
{
    public function __construct(
        XrLicense $xrLicense,
    )
    {
        $this->xrLicense = $xrLicense;
    }

    /**
     * 라이센스 확인
     * @param string $licenseCode
     * @return mixed
     */
    public function isLicenseCode(string $licenseCode)
    {
        return $this->xrLicense->where('license_code',$licenseCode)->first();
    }

    /**
     * 라이센스 코드 저장
     * @param array $insertParam
     * @return void
     */
    public function licenseCodeInsert(array $insertParam)
    {
        $this->xrLicense->insert($insertParam);
    }

}
