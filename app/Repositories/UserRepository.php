<?php

namespace App\Repositories;

use App\Models\XrAgency;
use App\Models\XrGroup;
use App\Models\XrUser;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function __construct(
        XrUser $xrUser,
        XrGroup $xrGroup,
        XrAgency $xrAgency,
    )
    {
        $this->xrUser = $xrUser;
        $this->xrGroup = $xrGroup;
        $this->xrAgency = $xrAgency;
    }

    /**
     * 유저 전체 리스트(관리자)
     * @param string $where
     * @return mixed
     */
    public function getUserMainList(string $where)
    {
       return $this->xrUser->whereRaw($where)
            ->where([
                ['use_flag', '=', 'Y'],
                ['verification_flag', '=', 'Y']
            ])
            ->orderby('created_at','desc')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * 특정 유저 정보 가져오기
     * @param string $where
     * @param string $with
     * @return mixed
     */
    public function getUserInfoOne(string $where,string $with=''): mixed
    {
         $query = $this->xrUser->whereRaw($where);
         if($with)
         {
             $query->with($with);
         }
        return $query->first();
    }

    /**
     * 특정 기관에 소속된 그룹 id 리스트(하나씩 있으면 되므로 group by)
     * @param int $agencyId
     * @return mixed
     */
    public function getAgencyGroupList(int $agencyId): mixed
    {
        return $this->xrUser->select('group_id')
                ->where([
                    ['agency_id', '=', $agencyId],
                    ['group_id', '<>', null]
                ])->groupby('group_id')->get();
    }

    /**
     * 회원,그룹,기관 정보 수정
     * @param array $updateParam
     * @param string $column
     * @param string $type
     * @return void
     */
    public function updateTableInfo(array $updateParam, string $column, string $type): void
    {
        if($type === 'user') {
            $query = $this->xrUser;
        } else if($type === 'group') {
            $query = $this->xrGroup;
        } else {
            $query = $this->xrAgency;
        }
        $query->where($column,$updateParam[$column])->update($updateParam);
    }

    /**
     * 해당 기관에 소속된 그룹 전체 사용 / 미사용 변경
     * @param array $groupIdArray
     * @param array $updateParam
     * @return void
     */
    public function updateAgencyGroupUseFlag(array $groupIdArray,array $updateParam)
    {
        $this->xrGroup->whereIn('group_id',$groupIdArray)->update($updateParam);
    }

    /**
     * 특정 기관 그룹정보 조회
     * @param int $agencyId
     * @param string $tabType
     * @param string $where
     * @return mixed|null
     */
    public function getGroupList(int $agencyId, string $tabType='',string $where=''): mixed
    {
        return $this->xrAgency->select('agency_id','agency_name')
            ->where('agency_id',$agencyId)
            ->with(['getUserInfo'=> function ($query) {
                $query->where('ut_code','20');
            }])
            ->with(['getAgencyWithGroupInfo' => function ($query) use($tabType,$where) {
                $query->select('xr_user.agency_id','xr_user.phone_num','xr_user.verification_flag','xr_user.sort','xr_user.birthday','xr_user.ut_code','xr_user.user_id','xr_user.login_id','xr_user.name','xr_user.phone_num','xr_group.group_id','xr_group.group_name','xr_group.group_user_count');
                if($tabType === 'MN') {
                    $query->where('xr_group.group_code', 'MN');
                    $query->where('xr_user.ut_code','21');
                    $query->where('xr_user.use_flag','Y');
                } else if($tabType === 'PS') {
                    $query->where('xr_group.group_code', 'PS');
                    $query->where('xr_user.ut_code','22'); // 기관 회원을 개인그룹 계정으로
                    $query->where('xr_user.use_flag','Y');
                    $query->orderby('sort','asc');
                } else { // 모든 그룹 리스트 추출용
                    $query->where('xr_user.use_flag','Y');
                    $query->wherein('xr_user.ut_code',['20','21']);
                }

                if($where){
                    $query->whereRaw($where);
                }
            }])
            ->first();
    }

    /**
     * 선택된 유저들 정보 변경
     * @param array $userIdArray
     * @param array $updateParam
     * @return void
     */
    public function updateUserListInfo(array $userIdArray,array $updateParam)
    {
        $this->xrUser->whereIn('user_id',$userIdArray)->update($updateParam);
    }

    /**
     * 해당 그룹에 정렬 순서 최댓값 구하기
     * @param int $agencyId
     * @param int $groupId
     * @return mixed
     */
    public function getLastSortNum(int $agencyId,int $groupId)
    {
        return $this->xrUser->where(
                                    [
                                        ['agency_id','=',$agencyId],
                                        ['group_id','=',$groupId],
                                        ['verification_flag','=','Y'],
                                        ['use_flag','=','Y'],
                                        ['ut_code','=','22']
                                    ])->max('sort');
    }

    /**
     * 해당 그룹 정보 조회
     * @param int $groupId
     * @return mixed
     */
    public function getGroupInfo(int $groupId)
    {
        return $this->xrGroup->where('group_id',$groupId)->first();
    }

    /**
     * 해당기관 그룹 유저 리스트
     * @param int $agencyId
     * @param int $groupId
     * @param string $where
     * @return mixed
     */
    public function getGroupUserList(int $agencyId,int $groupId,string $where)
    {
        return $this->xrAgency->select('agency_id','agency_name')
            ->where('agency_id',$agencyId)
            ->with(['getUserInfo'=> function ($query) use($groupId) {
                $query->where('ut_code','21');
                $query->where('group_id',$groupId);
            }])
            ->with(['getAgencyWithGroupInfo' => function ($query) use($groupId,$where) {
                $query->select('xr_user.agency_id','xr_user.phone_num','xr_user.verification_flag','xr_user.sort','xr_user.ut_code','xr_user.user_id','xr_user.login_id','xr_user.birthday','xr_user.name','xr_user.phone_num','xr_group.group_id','xr_group.group_name','xr_group.group_user_count');
                $query->where('xr_group.group_code', 'MN');
                $query->where('xr_group.group_id',$groupId);
                $query->where('xr_user.ut_code','22');
                $query->where('xr_user.use_flag','Y');
                $query->orderby('xr_user.sort','asc');
                if($where){
                    $query->whereRaw($where);
                }
            }])
            ->first();
    }

}

