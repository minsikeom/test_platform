@extends('layouts.adminLayout')

@section('title')
    그룹 리스트
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
        width:40%;
    }

    .pagination {
        display: flex;justify-content: center;
    }

    .sizeFix{
        width:50%;
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
                                        <div class="col-md-5">
                                            <div class="form-group row">
                                                <label for="agencyName" class="col-md-4 col-form-label text-md-right">기관 이름 :</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="agencyName" name="agencyName" value="{{ $agencyUserInfo->agency_name }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group row">
                                                <label for="agencyPhone" class="col-md-4 col-form-label text-md-right">대표 연락처 :</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="agencyPhone" name="agencyPhone" value="{{ $agencyUserInfo->getUserInfo->phone_num }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group row">
                                                <label for="managerName" class="col-md-4 col-form-label text-md-right">담당자 :</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="managerName" name="managerName" value="{{ $agencyUserInfo->getUserInfo->name }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group row">
                                                <label for="email" class="col-md-4 col-form-label text-md-right">E-mail : </label>
                                                <div class="col-md-8">
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ $agencyUserInfo->getUserInfo->email }}" disabled>
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
                                    <input type="hidden" name="agency" id="agency" value="{{ $agencyId }}">
                                    <input type="hidden" name="page" id="pages" value="{{ $page }}">
                                        <div class="buttons-and-search-container">
                                            <div class="buttons-container">
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

                                            <div class="searchGroup">
                                                <select id="searchSelect" class="form-control form-control-sm" name="searchType" style="width: 30%">
                                                    <option value='group_name'  {{ ($searchType === 'group_name')? 'selected' :'' }}>그룹이름</option>
                                                </select>
                                                <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 50%"  placeholder="검색어를 입력하세요">
                                                <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                                <button type="button" class="btn btn-default sizeFix" onclick="makeGroup({{ $agencyId }})">그룹+</button>
                                            </div>
                                        </div>

                                <div style="height: 10px"></div>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="text-align: center;">
                                        <th>번호</th>
                                        <th>그룹 이름</th>
                                        <th>관리자 </th>
                                        <th>전화번호</th>
                                        <th>소속인원</th>
                                        <th>소속사용자</th>
                                        <th>정보</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $countNum = (($page - 1) * $perPage)+1 @endphp
                                    @foreach($groupList  as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:15%">{{ $countNum }}</td>
                                            <td style="width:12%">{{ $list->group_name }}</td>
                                            <td style="width:15%">{{ $list->name }}</td>
                                            <td style="width:15%">{{ $list->phone_num }}</td>
                                            <td style="width:16%">{{ $list->group_user_count }}</td>
                                            <td>
                                                <input type="button" class="btn btn-block btn-primary btn-xs" onclick="moveSpecificList({{ $agencyId }},{{ $list->group_id }})" value="사용자 관리">
                                            </td>
                                            <td style="display: flex;gap: 10px;">
                                                <input type="button" class="btn btn-block btn-default btn-xs" onclick="moveEditForm({{ $list->user_id }},'21')" value="정보수정">
                                                <input type="button" class="btn btn-block btn-danger btn-xs" style="margin-top:0;" onclick="deleteGroup({{ $list->user_id }},'21')"  value="그룹삭제">
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

        // 사용자 관리
        function moveSpecificList(id,groupCode) {
            window.location.href = "/admin/user/agency/group/user-list?agency=" + id + "&group=" + groupCode;
        }
        // 그룹 추가
        function makeGroup(id)
        {
            window.location.href="/admin/user/agency/group/form/"+id;
        }

        // 그룹 , 개인
        function moveTap(value)
        {
            $('#pages').val(1);
            $('#tapTypes').val(value);
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

        // 삭제 버튼
        function deleteGroup(id) {
            if (confirm('삭제 하시겠습니까?')) {
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
        }

    </script>
@endsection
