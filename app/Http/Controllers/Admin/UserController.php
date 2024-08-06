<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\SignUpRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;

class UserController extends Controller
{
    public function __construct(UserRepository $user,SignUpRepository $signUp)
    {
        $this->user = $user;
        $this->signUp = $signUp;
    }

    /**
     * 관리자 유저 메인
     * @param Request $request
     * @return Application|Factory|View
     */
    public function getUserMainList(Request $request)
    {
        // 슈퍼 관리자,게스트 제외
        $where = "ut_code NOT IN ('00', '23') ";
        $data['tapType'] = $request->input('tapType','agency'); // 대표 / 전체 버튼
        $data['searchType'] = $request->input('searchType'); // 셀렉트박스
        $data['searchKey'] =  $request->input('searchKey');  // 검색어
        if($data['tapType'] === 'agency'){
            $where .= " and ut_code in('10','20') ";
        }
        if($data['searchType'] && $data['searchKey'] ){
            if($data['searchType'] == 10 ){
                $column ='login_id';
            } else if ($data['searchType'] == 20 ){
                $column ='name';
            } else if  ($data['searchType'] == 30 ){
                $column='nick_name';
            }else if  ($data['searchType'] == 40 ){
                $column = 'email';
            }else if  ($data['searchType'] == 50 ){
                $column ='phone_num';
            }
            $where .= " and ".$column." like '%".$data['searchKey']."%'";
        }

        $data['page'] = $request->input('page',1);
        $data['userList'] = $this->user->getUserMainList($where);
        return view('admin.userMainList',$data);
    }

    /**
     * 유저 등급별 사용, 미사용 변경
     * @param Request $request
     * @return int
     */
    public function useFlag(Request $request): int
    {
        $userId = $request->input('userId');
        $flag = $request->input('flag','N');
        $where ='user_id ='.$userId;
        $userInfo =  $this->user->getUserInfoOne($where);
        $result = 1;

        if($userInfo->ut_code === '20') { // 대표-기관일때 아래 하위 그룹 소속 사용자 정지

            $updateParam['agency_id']   = $userInfo->agency_id;
            $updateParam['use_flag']    = $flag;

            try {
                // 기관 정지
                $this->updateUseFlag(
                    updateParam: $updateParam,
                    column: 'agency_id',
                    type: 'agency'
                );

                // 해당 기관 모든 유저 정지
                $this->updateUseFlag(
                    updateParam: $updateParam,
                    column: 'agency_id',
                    type: 'user'
                );

                // 해당 기관 모든 그룹 정지
                $this->updateAgencyGroupUseFlag($this->user->getAgencyGroupList($userInfo->agency_id));
            } catch (Exception $error) {
                Log::error($error);
                $result = 0;
            }

        } else if($userInfo->ut_code === '21') { // 그룹 일때는 그룹 소속 사용자 정지

            $updateParam['group_id'] = $userInfo->group_id;
            $updateParam['use_flag'] = $flag;
            $updateParam['group_user_count'] = 0;
            $column ='group_id';

            try {
                // 해당 그룹에 모든 유저 정지
                $this->updateUseFlag(
                    updateParam: $updateParam,
                    column: $column,
                    type: 'user'
                );

                // 그룹 사용 정지
                $this->updateUseFlag(updateParam: $updateParam,
                    column: $column,
                    type: 'group'
                );
            } catch (Exception $error) {
                Log::error($error);
                $result = 0;
            }

        } else { // 일반 일때

            $updateParam['user_id'] = $userInfo->user_id;
            $updateParam['use_flag'] = $flag;
            $column ='user_id';
            // 해당 유저 정지
            try {
                $this->updateUseFlag(
                    updateParam: $updateParam,
                    column: $column,
                    type: 'user'
                );
                // 해당 그룹 카운트 -1
                $this->updateGroupCount($userInfo->group_id,'minus');
            } catch (Exception $error) {
                Log::error($error);
                $result = 0;
            }
        }

        return $result;
    }

    /**
     * 유저,기관,그룹 사용 / 미사용 변경
     * @param array $updateParam
     * @param string $column
     * @param string $type
     */
    public function updateUseFlag(array $updateParam,string $column,string $type): void
    {
            $this->user->updateTableInfo(
                updateParam: $updateParam,
                column: $column,
                type: $type
            );
    }

    /**
     * 기관에 소속된 그룹 사용, 미사용 변경
     * @param Collection $groupIdList
     * @return void
     */
    public function updateAgencyGroupUseFlag(Collection  $groupIdList): void
    {
        foreach($groupIdList as $groupId){
            $groupIdArray[]=$groupId->group_id;
        }
        $updateParam['use_flag'] ='N';
        $this->user->updateAgencyGroupUseFlag(
            groupIdArray:$groupIdArray,
            updateParam:$updateParam
        );
    }

    /**
     * 관리자 비밀번호 확인
     * @param Request $request
     * @return int
     */
    public function isPassword(Request $request):int
    {
        $where = "ut_code = 00"; // 관리자 계정
        $userInfo = $this->user->getUserInfoOne($where);
        // 비밀번호 체크
        if(Hash::check($request->input('password') , $userInfo->password))
        {
            return 1;
        }
        return 0;
    }

    /**
     * 회원 가입, 수정 폼 이동
     * @param int $userId
     * @param string $utCode
     * @param Request $request
     * @return Application|Factory|View
     */
    public function moveUserEditForm(int $userId,string $utCode,Request $request)
    {
        $with   =   '';
        $where  = 'user_id = ' . $userId;
        if ($utCode === '20') { // 대표-기관
            $with = 'getAgencyInfo';
        } else if ($utCode === '21') { // 그룹
            $with = 'getGroupInfo';
        }
        $data['utCode']     = $utCode;
        $data['userInfo']   = $this->user->getUserInfoOne($where, $with);
        $data['referer']    = $request->headers->get('referer');
        return view('admin.userEditForm',$data);
    }

    /**
     * 유저 정보 변경
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateUserInfo(Request $request): RedirectResponse
    {
        // 비밀번호 입력시에만 변경
        if($request->input('password')) {
            $updateParam['password'] =bcrypt($request->input('password'));
        }

        // 유저 정보 업데이트
        $updateParam['user_id'] = $request->input('userId');
        $updateParam['name'] = $request->input('name');
        $updateParam['nick_name'] = $request->input('nickName');
        $updateParam['phone_num'] = $request->input('phoneNum');
        $this->user->updateTableInfo($updateParam,'user_id','user');

        if($request->input('utCode') === '20') { // 대표-기관
            // 기관 정보 수정
            $updateAgencyParam['agency_id'] = $request->input('agencyId');
            $updateAgencyParam['agency_name'] = $request->input('agencyName');
            $this->user->updateTableInfo($updateAgencyParam,'agency_id','agency');

        } elseif($request->input('utCode') === '21') { // 그룹
            // 그룹 정보 수정
            $updateGroupParam['group_id'] = $request->input('groupId');
            $updateGroupParam['group_name'] = $request->input('groupName');
            $this->user->updateTableInfo($updateGroupParam,'group_id','group');
        }
        // 전에 리스트로 보내기
        return redirect($request->input('referer'));
    }

    /**
     * 기관별 그룹(메인,개인) 리스트
     * @param Request $request
     * @return Application|Factory|View
     */
    public function getGroupList(Request $request): View|Factory|Application
    {
        $where='1=1';
        $data['page']       = $request->input('page',1);
        $data['tapType']  = $request->input('tapType','MN'); // 메인그룹 기본
        $data['searchType'] = $request->input('searchType'); // 셀렉트 박스
        $data['searchKey']  = $request->input('searchKey');  // 검색어
        $data['agencyId'] = $request->input('agency'); // 기관 id
        $data['approveType'] = $request->input('approveType','approved'); // 승인,대기,반려

        // 개인 그룹에서 승인,대기,반려 리스트 조건 추가
        if($data['approveType']){
            if($data['approveType'] === 'approved' ) {
                $verificationFlag = 'Y';
            } else if($data['approveType'] === 'pending'){
                $verificationFlag = 'W';
            } else {
                $verificationFlag = 'N';
            }
            $where .= " and verification_flag='".$verificationFlag."'";
        }

        // 검색어 입력 조건
        if($data['searchKey'])
        {
            $where .= " and ".$data['searchType']." like '%".$data['searchKey']."%'";
        }

        // 기관 정보
        $agencyInfoWithGroupList = $this->user->getGroupList($data['agencyId'],$data['tapType'],$where);
        $data['agencyUserInfo'] = $agencyInfoWithGroupList;
//        print_r($agencyInfoWithGroupList->toarray());
        // 그룹 리스트 모델 관계에서 pagenation 제공X
        $collection = collect($agencyInfoWithGroupList->getAgencyWithGroupInfo);
        $data['perPage'] = 30;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('page');
        $currentPageItems = $collection->slice(($currentPage - 1) *  $data['perPage'],  $data['perPage'])->all();
        // 페이징된 데이터 생성
        $paginatedData = new LengthAwarePaginator(
            $currentPageItems,
            count($collection),
            $data['perPage'],
            $currentPage,
            ['path' => route('getGroupList')]
        );
        $paginatedData->appends($request->query()); // 쿼리스트링 유지
        $data['groupList'] = $paginatedData;
        if($data['tapType'] === 'MN') { // 메인그룹
            return view('admin.mainGroupList', $data);
        } else { // 개인그룹
            // 회원가입 링크
            $data['groupId'] = $data['agencyUserInfo']->getUserInfo->group_id;
            $data['urlLink'] = route('signUpForm')."?agencyId=".$data['agencyId']."&groupId=".$data['agencyUserInfo']->getUserInfo->group_id;
            $data['selectGroupList'] = $this->user->getGroupList($data['agencyId'])->getAgencyWithGroupInfo;
            return view('admin.personalGroupList',$data);
        }
    }

    /**
     * 그룹폼 이동
     * @param int $agencyId
     * @param Request $request
     * @return Application|Factory|View
     */
    public function moveGroupForm(int $agencyId,Request $request)
    {
        $data['agencyId'] = $agencyId;
        $data['countryInfo'] = $this->signUp->getCountryInfo();
        $data['referer'] = $request->headers->get('referer');
        return view('admin.groupForm',$data);
    }

    /**
     * 그룹 추가
     * @param Request $request
     * @return Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function insertGroup(Request $request)
    {
        // 그룹 계정 인설트
        $insertGroupParam['group_name']=$request->input('groupName');
        $insertGroupParam['group_code']='MN'; // 메인그룹만 생성
        $insertUserParam['group_id'] = $this->signUp->insertGroup($insertGroupParam);

        // 그룹 담당자 계정 인설트
        $insertUserParam['login_id'] =$request->input('loginId');
        $insertUserParam['password'] =bcrypt($request->input('password'));
        $insertUserParam['agency_id'] =$request->input('agency');
        $insertUserParam['name'] =$request->input('name');
        $insertUserParam['nick_name'] =$request->input('nickName');
        $insertUserParam['ut_code'] ='21';
        $insertUserParam['use_flag'] ='Y';
        $insertUserParam['verification_flag'] ='Y';
        $insertUserParam['sort'] =0;
        $this->signUp->insertUser($insertUserParam);

        return redirect('/admin/user/agency/group/list?agency='.$request->input('agency'));
    }

    /**
     * 유저 승인상태 수정
     * @param Request $request
     * @return int
     */
    public function updateApproveStatus(Request $request):int
    {
        $result = 1;
        $updateParam['user_id'] = $request->input('userId');
        if($request->input('status') === 'approved' ) {
            $updateParam['verification_flag'] = 'Y';
        } else if($request->input('status') === 'pending'){
            $updateParam['verification_flag'] = 'W';
        } else {
            $updateParam['verification_flag'] = 'N';
        }

        // 승인일때 sort + 1
        if($updateParam['verification_flag'] === 'Y') {
            // 마지막 번호 가져오기
            $lastSortNum = $this->user->getLastSortNum(
                agencyId: $request->input('agencyId'),
                groupId: $request->input('groupId')
            );
            $updateParam['sort'] = $lastSortNum + 1;
        }

        try {
            $this->user->updateTableInfo($updateParam, 'user_id', 'user');
            // 승인일때 그룹 카운트 + 1
            if($updateParam['verification_flag'] === 'Y') {
                $this->updateGroupCount($request->input('groupId'),'plus');
            }
        } catch (Exception $error) {
            Log::error($error);
            $result = 0;
        }
        return $result;
    }

    /**
     * 회원 정렬 순서 변경
     * @param Request $request
     * @return int
     */
    public function updateSortCount(Request $request)
    {
        $userId = $request->input('userId');
        $option = $request->input('option');
        $where = 'user_id='.$userId;
        $selectUserInfo = $this->user->getUserInfoOne($where);
        $result = 1;

        if($option === 'up'){
              $sort = $selectUserInfo->sort - 1;
              $where = 'group_id='.$selectUserInfo->group_id." and agency_id=".$selectUserInfo->agency_id." and sort <= ".$sort." and ut_code='22' and use_flag='Y' order by sort desc ";
              $updateUserInfo = $this->user->getUserInfoOne($where);
        } else if($option === 'down'){
            $sort = $selectUserInfo->sort + 1;
            $where = 'group_id='.$selectUserInfo->group_id." and agency_id=".$selectUserInfo->agency_id." and sort >= ".$sort." and ut_code='22' and use_flag='Y' order by sort asc ";
            $updateUserInfo = $this->user->getUserInfoOne($where);
        }

        if(!$updateUserInfo){
            return 0; // 위,아래로 갈 사람이 없을때
        }

        // 둘간에 변경
        try{
            $updateSelectUserParam['user_id'] = $selectUserInfo->user_id;
            $updateSelectUserParam['sort'] = $sort;
            $this->user->updateTableInfo($updateSelectUserParam, 'user_id', 'user');
//            return $updateSelectUserParam;
            $updateUserParam['user_id'] = $updateUserInfo->user_id;
            $updateUserParam['sort'] = $selectUserInfo->sort;
            $this->user->updateTableInfo($updateUserParam, 'user_id', 'user');
//            return $updateUserParam;
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 선택된 유저 그룹 변경
     * @param Request $request
     * @return int
     */
    public function updateUserGroupPosition(Request $request)
    {
        $updateParam['group_id'] = $request->input('groupCode');
        $userIdArray = $request->input('array');
        $updateParam['verification_flag'] ='W';
        $result = 1;
        try {
            $this->user->updateUserListInfo(
                userIdArray: $userIdArray,
                updateParam: $updateParam
            );
            // 해당 그룹이동 인원수 만큼 마이너스
            $this->updateGroupCount($request->input('beforeGroupId'),'minus',count($userIdArray));
        } catch (Exception $error) {
            Log::error($error);
            $result = -1;
        }
        return $result;
    }

    /**
     * 그룹 카운트 업데이트
     * @param int $groupId
     * @param string $status
     * @param int $userCount
     * @return void
     */
    public function updateGroupCount(int $groupId,string $status,int $userCount = 1 )
    {
        $groupInfo = $this->user->getGroupInfo($groupId);
        if($status === 'plus' ) {
            $count = $groupInfo->group_user_count + $userCount ;
        } else {
            $count = $groupInfo->group_user_count - $userCount ;
        }
        $updateParam['group_id'] = $groupInfo->group_id;
        $updateParam['group_user_count'] = $count;
        $this->user->updateTableInfo($updateParam, 'group_id', 'group');

    }

    /**
     * 사용자 추가 폼
     * @param int $agencyId
     * @param int $groupId
     * @param Request $request
     * @return Application|Factory|View
     */
    public function moveUserForm(int $agencyId,int $groupId,Request $request)
    {
        $data['agencyId'] = $agencyId;
        $data['countryInfo'] = $this->signUp->getCountryInfo();
        $data['groupId'] = $groupId;
        $data['referer'] = $request->headers->get('referer');
        return view('admin.userForm',$data);
    }

    /**
     * 그룹 유저 추가
     * @param Request $request
     * @return Application|\Illuminate\Routing\Redirector|RedirectResponse
     */
    public function insertGroupUser(Request $request)
    {
        $insertParam['agency_id'] = $request->input('agency');
        $insertParam['group_id'] = $request->input('group');
        $insertParam['name'] = $request->input('name');
        $insertParam['nick_name'] = $request->input('nickName');
        $insertParam['phone_num'] = $request->input('phoneNum');
        $insertParam['birthday'] = $request->input('birthday');
        $insertParam['login_id'] = $request->input('loginId');
        $insertParam['password'] = bcrypt($request->input('password'));
        // 그룹 유저 추가
        $this->insertGroupUserProcess($insertParam);

        return redirect($request->input('referer'));
    }

    /**
     * 그룹 유저 추가, 그룹 유저 카운트 증가
     * @param array $insertParam
     * @return void
     */
    public function insertGroupUserProcess(array $insertParam){

        $lastSortNum = $this->user->getLastSortNum(
            agencyId:$insertParam['agency_id'],
            groupId:$insertParam['group_id']
        );
        $insertParam['sort'] = $lastSortNum + 1;
        $insertParam['ut_code'] = '22';
        $insertParam['verification_flag'] = 'Y';
        // 그룹 유저 추가
        $this->signUp->insertUser($insertParam);

        // 그룹 카운트 업데이트
        $groupInfo = $this->user->getGroupInfo($insertParam['group_id']);
        $updateParam['group_id'] =$insertParam['group_id'];
        $updateParam['group_user_count'] = $groupInfo->group_user_count + 1;
        $this->user->updateTableInfo($updateParam,'group_id','group');
    }

    /**
     * 메인 그룹 유저 리스트
     * @param Request $request
     * @return Application|Factory|View
     */
    public function getGroupUserList(Request $request)
    {
        $where = '1=1';
        $data['page']       = $request->input('page',1);
        $data['searchType'] = $request->input('searchType'); // 셀렉트 박스
        $data['searchKey']  = $request->input('searchKey');  // 검색어
        $data['agencyId'] = $request->input('agency'); // 기관 id
        $data['groupId'] = $request->input('group'); // 그룹 id
        $data['approveType'] = $request->input('approveType','approved'); // 승인,대기,반려

        // 개인 그룹에서 승인,대기,반려 리스트 조건 추가
        if($data['approveType']){
            if($data['approveType'] === 'approved' ) {
                $verificationFlag = 'Y';
            } else if($data['approveType'] === 'pending'){
                $verificationFlag = 'W';
            } else {
                $verificationFlag = 'N';
            }
            $where .= " and verification_flag='".$verificationFlag."'";
        }

        // 검색어 입력 조건
        if($data['searchKey'])
        {
            $where .= " and".$data['searchType']." like '%".$data['searchKey']."%'";
        }

        $agencyWithGroupUserInfo = $this->user->getGroupUserList(
            agencyId:$data['agencyId'],
            groupId:$data['groupId'],
            where:$where
        );
        $data['agencyGroupUserInfo'] = $agencyWithGroupUserInfo;
        // 그룹 리스트 모델 관계에서 pagenation 제공X
        $collection = collect($agencyWithGroupUserInfo->getAgencyWithGroupInfo);
        $data['perPage'] = 30;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('page');
        $currentPageItems = $collection->slice(($currentPage - 1) * $data['perPage'], $data['perPage'])->all();
        // 페이징된 데이터 생성
        $paginatedData = new LengthAwarePaginator(
            $currentPageItems,
            count($collection),
            $data['perPage'],
            $currentPage,
            ['path' => route('getGroupUserList')]
        );
        $paginatedData->appends($request->query()); // 쿼리스트링 유지
        $data['groupUserList'] = $paginatedData;
//        print_r($agencyWithGroupUserInfo->toarray());
        $data['groupInfo'] = $this->user->getGroupInfo($data['groupId']);
        $data['urlLink'] = route('signUpForm')."?agencyId=".$data['agencyId']."&groupId=".$data['groupId'];
        $data['selectGroupList'] = $this->user->getGroupList($data['agencyId'])->getAgencyWithGroupInfo;
        return view('admin.mainGroupUserList',$data);
    }

    /**
     * 사용자 엑셀 일괄 업로드
     * @param int $agencyId
     * @param int $groupId
     * @param Request $request
     * @return Application|Factory|View
     */
    public function moveExcelForm(int $agencyId,int $groupId,Request $request)
    {
        $data['agencyId'] = $agencyId;
        $data['groupId'] = $groupId;
        $data['referer'] = $request->headers->get('referer');
        return view('admin.excelForm',$data);
    }

    /**
     * 엑셀 업로드 및 데이터 읽어오기
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public function getExcelDataList(Request $request)
    {
        if (!$request->hasFile('files')) {
            $errorMessage = '엑셀 파일을 올려주세요.';
            return redirect()->back()->withErrors($errorMessage);
        }

        //엑셀 데이터 읽기
        $dataList = (new FastExcel)->sheet(1)->import($request->file('files'));

        if( $dataList->count() >= 30) {
            $errorMessage = '최대 30명까지 업로드 가능합니다.';
            return redirect()->back()->withError($errorMessage);
        }

        foreach ($dataList as $row) {

            if (
                $this->isSpecialKey(trim($row['name'])) ||
                $this->isSpecialKey(trim($row['nickName']))||
                $this->isSpecialKey(trim($row['phone'])) ||
                $this->isSpecialKey(trim($row['loginId']))
            ) {
                $errorMessage = '특수문자 사용 불가능 합니다.';
                return redirect()->back()->withError($errorMessage);
            }

            $where = "login_id='".$row['loginId']."'";
            $userDuplicateCheck = $this->user->getUserInfoOne($where);

            $filteredData[] = [
                'name'      =>  trim($row['name']),
                'nick_name' =>  trim($row['nickName']),
                'phone_num' =>  trim($row['phone']),
                'birthday'  =>  ($row['birthday'])? Carbon::parse($row['birthday']) : '',
                'login_id'  =>  trim($row['loginId']),
                'loginIdDuplicate' =>(!$userDuplicateCheck)? 'N':'Y' // Y이면 중복,N이면 없음
            ];
        }

       $data['groupId']           = $request->input('group');
       $data['agencyId']          = $request->input('agency');
       $data['uploadUserList']    = $filteredData;
//       print_r($filteredData);
//       exit;
       return view('admin.excelDataList',$data);
    }

    /**
     * 그룹 엑셀 유저 단체 인설트
     * @param Request $request
     * @return void
     */
    public function insertExcelData(Request $request)
    {
        $result = 1;
        $insertParam['group_id'] = $request->input('group');
        $insertParam['agency_id'] = $request->input('agency');
        foreach( $request->input('loginId') as $key => $value)
        {
            $insertParam['login_id']            = $value;
            $insertParam['name']                = ($request->input('name')[$key])??'';
            $insertParam['nick_name']           = ($request->input('nickName')[$key])??'';
            $insertParam['phone_num']           = ($request->input('phoneNum')[$key])??'';
            $insertParam['birthday']            = ($request->input('birthday')[$key])??'';
            $insertParam['verification_flag']   = 'Y';
            $insertParam['ut_code']             = '22';
            $insertParam['password']            =  bcrypt($request->input('password')[$key]);

            // 그룹 유저 추가
            try {
                $this->insertGroupUserProcess($insertParam);
            } catch (Exception $error) {
                Log::error($error);
                $result = -1;
            }

        }

        if($result == 1) {
            echo "<script>alert('등록 완료 되었습니다.');window.opener.location.reload();window.close();</script>";
        } else {
            echo "<script>alert('에러가 발생하였습니다 관리자에게 문의 해주세요.');window.opener.location.reload();window.close();</script>";
        }
    }

    /**
     * 특수문자 제거
     * @param string $string
     * @return false|int
     */
    public function isSpecialKey(string $string){
        return preg_match("/[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/i",$string);
    }

}
