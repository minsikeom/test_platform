@extends('layouts.adminLayout')

@section('title')
     개인그룹 사용자 리스트
@endsection

@section('style')
<style>

    .buttons-and-search-container {
        display: flex;
        gap: 10px;
        /*align-items: center;*/
        width: 100%;
    }

    .buttons-container {
        display: flex;
        gap: 10px;
        /*align-items: center;*/
        margin-right: auto; /* Pushes buttons to the left */
        width: 100%;
    }

    .searchGroup {
        display: flex;
        gap: 10px;
        align-items: center;
        width:30%;
    }

    .pagination {
        display: flex;justify-content: center;
    }

    .sizeFix{
        width:40%;
    }

    .activeTab
    {
        background-color:rgb(0, 123, 255);
        color:rgb(255, 255, 255);
    }

</style>
@endsection

@section('content')
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header  d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0 text-center" style="flex: 1;">기관 정보</h3>
                                <div>
                                    <input type="button" class="btn-block btn-default" onclick="moveEditForm({{ $agencyUserInfo->getUserInfo->user_id }},'20')"  value="기관 정보 수정">
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="agencyName" class="col-md-4 col-form-label text-md-right">기관 이름 :</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="agencyName" name="agencyName" value="{{ $agencyUserInfo->agency_name }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="agencyPhone" class="col-md-4 col-form-label text-md-right">대표 연락처 :</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="agencyPhone" name="agencyPhone" value="{{ $agencyUserInfo->getUserInfo->phone_num }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="managerName" class="col-md-4 col-form-label text-md-right">담당자 :</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="managerName" name="managerName" value="{{ $agencyUserInfo->getUserInfo->name }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="email" class="col-md-4 col-form-label text-md-right">E-mail : </label>
                                                <div class="col-md-8">
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ $agencyUserInfo->getUserInfo->email }}" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <input type="button" class="btn btn-block btn-default" onclick="copyUrl()"  value="개인 사용자 초대 URL 복사">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form id="submitForm">
                                    <input type="hidden" name="agency" id="agencys" value="{{ $agencyId }}">
                                    <input type="hidden" name="page" id="pages" value="{{ $page }}">
                                        <div class="buttons-and-search-container">

                                            <div class="buttons-container sizeFix">
                                                <input type="hidden" name="tapType" id="tapTypes" value="{{ $tapType }}">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <input type="button" class="nav-link {{ ($tapType === 'MN')? 'activeTab':'' }}"  id="group" value="Group" onclick="moveTap('MN')">
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <input type="button" class="nav-link {{ ($tapType === 'PS')? 'activeTab':'' }}" id="personal"  value="Personal" onclick="moveTap('PS')">
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="buttons-container">
                                                <input type="hidden" name="approveType" id="approveTypes" value="{{ $approveType }}">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <input type="button" class="nav-link {{ ($approveType === 'approved')? 'activeTab':'' }}"  id="approveY" value="승인완료" onclick="approveTap('approved')">
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <input type="button" class="nav-link {{ ($approveType === 'pending')? 'activeTab':'' }}" id="approveW"  value="승인대기" onclick="approveTap('pending')">
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <input type="button" class="nav-link {{ ($approveType === 'denied')? 'activeTab':'' }}" id="approveN"  value="반려" onclick="approveTap('denied')">
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="searchGroup" style="width: 100%; margin-right: 10%;">
                                                <select id="searchSelect" class="form-control form-control-sm" name="searchType" style="width: 30%">
                                                    <option value='login_id'  {{ ($searchType === 'login_id')? 'selected' :'' }}>ID</option>
                                                    <option value='name'  {{ ($searchType === 'name')? 'selected' :'' }}>이름</option>
                                                    <option value='phone_num'  {{ ($searchType === 'phone_num')? 'selected' :'' }}>전화번호</option>
                                                </select>
                                                <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" value="{{ $searchKey }}" style="width: 60%"  placeholder="검색어를 입력하세요">
                                                <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                                <button type="button" class="btn btn-default" style="width: 40%" onclick="moveAddForm({{ $agencyId }})">사용자+</button>
                                                <button type="button" class="btn btn-default" style="width: 60%" onclick="moveUserExcelForm({{ $agencyId }})">사용자 일괄 추가</button>
                                            </div>
                                        </div>

                                <div style="height: 10px"></div>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="text-align: center;">
                                        <th>선택</th>
                                        <th>정렬번호</th>
                                        <th>ID</th>
                                        <th>사용자 이름</th>
                                        <th>전화번호</th>
                                        <th>생년월일</th>
                                        <th>정보</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $countNum = (($page - 1) * $perPage)+1 @endphp
                                    @foreach($groupList  as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:5%"><input type="checkbox" value="{{ $list->user_id }}" name="checkUser" class="minimal"></td>
                                            <td style="width:15%">{{ $countNum }}&nbsp;<i class="fa fa-fw fa-angle-up" onclick="sortBy({{ $list->user_id }},'up')"></i><i class="fa fa-fw fa-angle-down" onclick="sortBy({{ $list->user_id }},'down')"></i></td>
                                            <td style="width:15%">{{ $list->login_id }}</td>
                                            <td style="width:15%">{{ $list->name }}</td>
                                            <td style="width:18%">{{ $list->phone_num }}</td>
                                            <td style="width:18%">{{ $list->birthday }}</td>
                                            <td style="display: flex;gap: 10px;">
                                                @if($list->verification_flag === 'W')
                                                    <input type="button" class="btn btn-block btn-success btn-xs" onclick="approveUser({{ $list->user_id }},{{ $agencyId }},{{ $groupId }},'approved')" value="승인">
                                                    <input type="button" class="btn btn-block btn-danger btn-xs" style="margin-top:0;" onclick="approveUser({{ $list->user_id }},{{ $agencyId }},{{ $groupId }},'denied')" value="미승인">
                                                @else
                                                    <input type="button" class="btn btn-block btn-default btn-xs" onclick="moveEditForm({{ $list->user_id }},'22')" value="정보수정">
                                                    <input type="button" class="btn btn-block btn-danger btn-xs" style="margin-top:0;" onclick="deleteUser({{ $list->user_id }},'22')" value="사용자 삭제">
                                                @endif
                                            </td>
                                        </tr>
                                        @php $countNum++ @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                    <div class="pagination">
                                        {{ $groupList->links() }}
                                    </div>
                                </form>

                                <div class="form-group  d-flex align-items-center" >
                                    <select id="selectGroup" class="form-control mr-2" style="width:10%">
                                        @foreach($selectGroupList as $list)
                                            @continue($groupId == $list->group_id)
                                            <option value="{{ $list->group_id }}">{{ $list->group_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="button" class="btn btn-default" onclick="changeGroup()" value="그룹이동">
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
@endsection

@section('script')
    <script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="/admin/plugins/jszip/jszip.min.js"></script>
    <script src="/admin/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="/admin/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="/admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="/admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- Page specific script -->
    <script>

        // 그룹 이동
        function changeGroup() {
            const groupCode = $('#selectGroup').val();
            const checkboxes = document.querySelectorAll('input[name="checkUser"]:checked');
            const values = Array.from(checkboxes).map(checkbox => checkbox.value);

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'post',
                url: '/admin/user/agency/group/move-group',
                data: {groupCode: groupCode,beforeGroupId:{{ $groupId }}, array: values},
                dataType: 'json',
                success: function (result) {
                    if (result == 1) {
                        alert('{{ $translateConstants::SUCCESS_MESSAGE[$lang]['success'] }}');
                        window.location.reload();
                    }  else {
                        alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                    }
                },
                error: function (e) {
                    alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}' + e);
                }
            })
        }

        // 승인완료,대기,반려 탭
        function approveTap(val)
        {
            $('#approveTypes').val(val);
            $('#pages').val(1);
            $('#searchSelect').val('');
            $('#searchInput').val('');
            $('#submitForm').submit();
        }

        // url 링크 복사
        function copyUrl()
        {
            var copyText = '{{ $urlLink }}';
            copyText = copyText.replace(/amp;/g, '');
            var tempTextArea = $('<textarea>');
            // textarea 속성 설정
            tempTextArea.val(copyText);
            tempTextArea.css('position', 'fixed');
            tempTextArea.css('top', 0);
            tempTextArea.css('left', 0);

            // body에 임시 textarea 추가
            $('body').append(tempTextArea);

            // textarea 선택 및 복사 명령 실행
            tempTextArea.select();
            document.execCommand('copy');

            // 임시 textarea 제거
            tempTextArea.remove();

            alert('텍스트가 클립보드에 복사되었습니다.');
        }

        // 사용자 추가
        function moveAddForm(id)
        {
            window.location.href="/admin/user/agency/group/user-form/"+id+"/"+{{ $groupId }};
        }

        // 사용자 엑셀 일괄 추가
        function moveUserExcelForm(id)
        {
            var newWindow = window.open("/admin/user/agency/group/excel-form/"+id+"/"+{{ $groupId }},'_blank','width=1000px,height=600px');
            newWindow.focus();
        }

        // 그룹 , 개인
        function moveTap(value)
        {
            $('#pages').val(1);
            $('#tapTypes').val(value);
            $('#approveTypes').val('');
            $('#searchSelect').val('');
            $('#searchInput').val('');
            $('#submitForm').submit();
        }

        // 검색 버튼 눌렀을때 페이지 초기화 후 폼전송
        function search()
        {
            $('#pages').val(1);
            $('#submitForm').submit();
        }

        // 수정 버튼
        function moveEditForm(id,code)
        {
            window.location.href="/admin/user/main/edit-form/"+id+"/"+code;
        }

        // 정렬 순서 변경
        function sortBy(id,val)
        {
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'post',
                url: '/admin/user/agency/group/edit-sort-count',
                data: {userId: id, option: val},
                dataType: 'json',
                success: function (result) {

                    if (result == 1) {
                        alert('{{ $translateConstants::SUCCESS_MESSAGE[$lang]['success'] }}');
                        window.location.reload();
                    } else if (result == 0) {
                        alert('{{ $translateConstants::PERSONAL_LIST[$lang]['changeSort'] }}');
                    } else {
                        alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                    }
                },
                error: function (e) {
                    alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}' + e);
                }
            })
        }

        // 승인 , 미승인 버튼
        function approveUser(id,agency,group,value) {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'post',
                    url: '/admin/user/agency/group/edit-approve-status',
                    data: {userId: id,agencyId:agency,groupId:group,status: value},
                    dataType: 'json',
                    success: function (result) {
                        if (result == 1) {
                            alert('{{ $translateConstants::SUCCESS_MESSAGE[$lang]['success'] }}');
                            window.location.reload();
                        } else {
                            alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                        }
                    },
                    error: function (e) {
                        alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}' + e);
                    }
                })
        }

        // 삭제 버튼
        function deleteUser(id) {
            if (confirm('삭제 하시겠습니까?')) {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'post',
                    url: '/admin/user/main/use-flag',
                    data: {userId: id, flag: 'N'},
                    dataType: 'json',
                    success: function (result) {
                        if (result == 1) {
                            alert('{{ $translateConstants::SUCCESS_MESSAGE[$lang]['success'] }}');
                            window.location.reload();
                        } else {
                            alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                        }
                    },
                    error: function (e) {
                        alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}' + e);
                    }
                })
            }
        }

    </script>
@endsection
