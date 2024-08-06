@extends('layouts.adminLayout')

@section('title')
    사용자 리스트
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

    .activeTab {
        background-color: #0c84ff;
        color:white;
    }

</style>
@endsection

@section('content')
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
{{--
                                <div class="card-header">--}}
{{--                                <h3 class="card-title">DataTable with default features</h3>--}}
{{--                            </div>
--}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form id="submitForm">
                                    <input type="hidden" name="page" id="pages" value="{{ $page }}">
                                        <div class="buttons-and-search-container">
                                            <div class="buttons-container">
                                                <input type="hidden" name="tapType" id="tapTypes" value="{{ $tapType }}">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <input type="button" class="nav-link {{ ($tapType === 'agency')? 'activeTab':'' }}"  id="agency-tab" value="대표 사용자" onclick="moveTap('agency')">
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <input type="button" class="nav-link {{ ($tapType === 'all')? 'activeTab':'' }}" id="all-tab"  value="전체" onclick="moveTap('all')">
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="searchGroup">
                                                <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 30%">
                                                    <option value=""  {{ ($searchType)? 'selected' :'' }}>전체</option>
                                                    <option value=10  {{ ($searchType == 10)? 'selected' :'' }}>사용자ID</option>
                                                    <option value=20  {{ ($searchType == 20)? 'selected' :'' }}>이름</option>
                                                    <option value=30  {{ ($searchType == 30)? 'selected' :'' }}>별명</option>
                                                    <option value=40  {{ ($searchType == 40)? 'selected' :'' }}>E-mail</option>
                                                    <option value=50  {{ ($searchType == 50)? 'selected' :'' }}>전화번호</option>
                                                </select>
                                                <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 50%"  placeholder="검색어를 입력하세요">
                                                <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                            </div>
                                        </div>

                                <div style="height: 10px"></div>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="text-align: center;">
                                        <th>사용자ID</th>
                                        <th>이름</th>
                                        <th>별명 </th>
                                        <th>E-mail</th>
                                        <th>전화번호</th>
                                        <th>관리구분</th>
                                        <th>정보</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($userList as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:15%">{{ $list->login_id }}</td>
                                            <td style="width:12%">{{ $list->name }}</td>
                                            <td style="width:15%">{{ $list->nick_name }}</td>
                                            <td style="width:15%">{{ $list->email }}</td>
                                            <td style="width:16%">{{ $list->phone_num }}</td>
                                            <td>
                                                @if($list->ut_code === '20')
                                                    <input type="button" class="btn btn-block btn-success btn-xs" onclick="moveSpecificList({{ $list->agency_id }},'','group')" value="대표-기관">
                                                @elseif($list->ut_code === '21')
                                                    <input type="button" class="btn btn-block btn-warning btn-xs" onclick="moveSpecificList({{ $list->agency_id }},{{ $list->group_id }},'groupUserList')" value="그룹">
                                                @elseif($list->ut_code === '22')
                                                    <input type="button" class="btn btn-block btn-primary btn-xs" value="일반">
                                                @elseif($list->ut_code === '10')
                                                    <input type="button" class="btn btn-block btn-primary btn-xs" value="대표-일반">
                                                @endif
                                            </td>
                                            <td style="display: flex;gap: 10px;">
                                                <input type="button" class="btn btn-block btn-default btn-xs" onclick="moveEditForm({{ $list->user_id }},{{ $list->ut_code }})" value="수정">
                                                <input type="button" class="btn btn-block btn-danger btn-xs" style="margin-top:0;" onclick="deleteUser({{ $list->user_id }},'{{ $list->ut_code }}')"  value="삭제">
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                    <div class="pagination">
                                    {{ $userList->links() }}
                                    </div>
                                </form>
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

        $(document).ready(function() {

            // 버튼 탭 색상 변경
            if( '{{ $tapType }}' === 'agency') {
                $('#agency').css("background-color", "rgb(0, 123, 255)");
                $('#agency').css("color", "rgb(255, 255, 255)");
            } else {
                $('#all').css("background-color", "rgb(0, 123, 255)");
                $('#all').css("color", "rgb(255, 255, 255)");
            }

            @if(!$searchType)
                $('#searchInput').hide();
            @endif
        });

        // 검색 타입 전체 시 입력폼 숨기기
        function selectType(val){
            if(val)
            {
                $('#searchInput').show();
            } else if(val == '') {
                $('#searchInput').hide();
            }
        }


        // 그룹, 대표-기관 관리 선택
        function moveSpecificList(id,groupCode,code)
        {
            if(code === 'group') {
                window.location.href = "/admin/user/agency/group/list?agency=" + id;
            } else {
                window.location.href = "/admin/user/agency/group/user-list?agency=" + id +"&group="+groupCode;
            }
        }

        // 대표 사용자, 전체 탭 선택
        function moveTap(value)
        {
            $('#pages').val(1);
            $('#tapTypes').val(value);
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

        // 삭제 버튼
        function deleteUser(id,code)
        {
            if(code === '20'){
                var confirmPassword = prompt("관리자 비밀번호를 입력해주세요");
                if(confirmPassword) {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: 'post',
                        url: '/admin/user/main/exists-password',
                        data: {password: confirmPassword},
                        dataType: 'json',
                        success: function (result) {
                            if (result == 1) {
                                deleteProcess(id);
                            } else if (result == 0) {
                                alert('비밀번호가 일치하지 않습니다.');
                            } else {
                                alert('에러가 발생 하였습니다\n 잠시 후 다시 해주세요.');
                            }
                        },
                        error: function (e) {
                            alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}' + e);
                        }
                    })
                }
            } else {
                if(confirm('삭제 하시겠습니까?'))
                {
                    deleteProcess(id);
                }
            }

        }

        function deleteProcess(id){
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'post',
                url: '/admin/user/main/use-flag',
                data: {userId: id, flag: 'N'},
                dataType: 'json',
                success: function (result) {
                    if (result == 1) {
                        alert('삭제 처리되었습니다');
                        window.location.reload();
                    } else {
                        alert('에러가 발생 하였습니다\n 잠시 후 다시 해주세요.');
                    }
                },
                error: function (e) {
                    alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}' + e);
                }
            })
        }

    </script>
@endsection
