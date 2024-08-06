@extends('layouts.adminLayout')

@section('title')
    콘텐츠 리스트
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
        margin-left: auto;
    }

    .pagination {
        display: flex;justify-content: center;
    }

    .sizeFix
    {
        width: 30%;
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
                                    <input type="hidden" name="sortBy" id="sortBy" value="{{ $sortBy }}">
                                    <input type="hidden" name="createdAtSortBy" id="createdAtSortBy" value="{{ $createdAtSortBy }}">
                                        <div class="buttons-and-search-container">
                                            <div class="searchGroup">
                                                <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 20%">
                                                    <option value=""  {{ ($searchType)? 'selected' :'' }}>전체</option>
                                                    <option value=10  {{ ($searchType == 10)? 'selected' :'' }}>이름</option>
                                                    <option value=20  {{ ($searchType == 20)? 'selected' :'' }}>영문이름</option>
                                                    <option value=30  {{ ($searchType == 30)? 'selected' :'' }}>장르</option>
                                                    <option value=40  {{ ($searchType == 40)? 'selected' :'' }}>센서</option>
                                                    <option value=50  {{ ($searchType == 50)? 'selected' :'' }}>테마</option>
                                                </select>
                                                <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 40%" value="{{ ($searchKey)??''  }}"  placeholder="검색어">
                                                <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                                <button type="button" class="btn btn-default sizeFix" onclick="makeContents()">콘텐츠 등록</button>
                                            </div>
                                        </div>
                                <div style="height: 10px"></div>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="text-align: center;">
                                        <th>정렬순서
                                            @if($sortBy === 'desc')
                                                <i class="fa fa-fw fa-angle-up" onclick="orderBy('sortBy','asc')"></i>
                                            @else
                                                <i class="fa fa-fw fa-angle-down" onclick="orderBy('sortBy','desc')"></i>
                                            @endif
                                        </th>
                                        <th>장르</th>
                                        <th>센서</th>
                                        <th>테마</th>
                                        <th>이름</th>
                                        <th>영문이름</th>
                                        <th>버전</th>
                                        <th>등록일
                                            @if($createdAtSortBy === 'desc')
                                                <i class="fa fa-fw fa-angle-up" onclick="orderBy('createdAtSortBy','asc')"></i>
                                            @else
                                                <i class="fa fa-fw fa-angle-down" onclick="orderBy('createdAtSortBy','desc')"></i>
                                            @endif
                                        </th>
                                        <th>관리</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $countNum = (($page - 1) * $perPage)+1 @endphp
                                    @foreach($contentsList as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:8%">{{ $countNum }}
                                                <i class="fa fa-fw fa-angle-up" onclick="contentsSortBy({{ $list->contents_id }},'up')"></i>
                                                <i class="fa fa-fw fa-angle-down" onclick="contentsSortBy({{ $list->contents_id }},'down')"></i>
                                            </td>
                                            <td style="width:12%">
                                                @if(isset($list->getContentsGenreGroup))
                                                    {{ $list->getContentsGenreGroup->getContentsGenreGroupTexts->text }}
                                                @endif
                                            </td>
                                            <td style="width:8%">
                                                @if(isset($list->getContentsSensor))
                                                    {{ $list->getContentsSensor[0]->sensor_name }}
                                                @endif
                                            </td>
                                            <td style="width:15%">
                                                @foreach($list->getThemeContents as $s => $w)
                                                    {{ $w->theme_name }}
                                                    @if(count($list->getThemeContents) > 1 && (count($list->getThemeContents)-1) != $s ) {{ ' / ' }} @endif
                                                @endforeach
                                            </td>
                                            <td style="width:12%">
                                                @if(isset($list->getContentsText))
                                                    {{ $list->getContentsText[0]->text_title }}
                                                @endif
                                            </td>
                                            <td style="width:12%">
                                                @if(isset($list->getContentsText))
                                                    {{ $list->getContentsText[1]->text_title }}
                                                @endif
                                            </td>
                                            <td style="width:5%">{{ $list->version }}</td>
                                            <td style="width:10%">{{ $list->created_at->format('Y-m-d') }}</td>
                                            <td style="display: flex;gap: 10px;">
                                                <input type="button" class="btn btn-block btn-default " onclick="moveEditForm({{ $list->contents_id }})" value="정보수정">
                                                <input type="button" class="btn btn-success " onclick="moveVersionForm({{ $list->contents_id }})" value="버전관리">
                                                <input type="button" class="btn btn-block btn-danger " style="margin-top:0;" onclick="deleteContents({{ $list->contents_id }},'{{ $list->f_path }}')"  value="삭제">
                                            </td>
                                        </tr>
                                        @php $countNum++ @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                    <div class="pagination">
                                        @if(count($contentsList)>0)
                                            {{ $contentsList->links() }}
                                        @endif
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
    <!-- Page specific script -->
    <script>
        // 정렬순서,등록일 오름차순,내림차순 정렬 변경
        function orderBy(type,direction)
        {
            $('#'+type).val(direction);
            $('#submitForm').submit();
        }

        // 콘텐츠 리스트 정렬 순서 변경
        function contentsSortBy(id,option){
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'post',
                url: '/admin/contents/edit-contents-sort-count',
                data: {contentsId:id,option:option},
                dataType: 'json',
                success: function (result) {
                    if (result == 1) {
                        alert('정렬순서 변경 완료 되었습니다.');
                        location.reload();
                    } else if(result == 0){
                        alert('정렬순서가 최소이거나 최대값 입니다.');
                    } else {
                        alert('에러가 발생 하였습니다\n 잠시 후 다시 해주세요.');
                    }
                },
                error: function (e) {
                    alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}' + e);
                }
            })
        }

        function moveVersionForm(id){
            window.location='/admin/contents/manage/exe-file-version/list/'+id;
        }

        function makeContents(){
            window.location='/admin/contents/form';
        }

        $(document).ready(function() {
            @if(!$searchType)
                $('#searchInput').hide();
            @endif
        });

        // 검색 타입 전체 시 입력폼 숨기기
        function selectType(val){
            if(val) {
                $('#searchInput').show();
            } else if(val == '') {
                $('#searchInput').hide();
            }
        }

        // 검색 버튼 눌렀을때 페이지 초기화 후 폼전송
        function search()
        {
            $('#pages').val(1);
            $('#submitForm').submit();
        }

        // 수정 버튼
        function moveEditForm(id)
        {
            window.location.href="/admin/contents/form/"+id;
        }

        // 삭제 버튼
        function deleteContents(id,path)
        {
            if(confirm('삭제 하시겠습니까?'))
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'post',
                    url: '/admin/contents/delete-contents-info',
                    data: {contentsId: id,path:path},
                    dataType: 'json',
                    success: function (result) {
                        if (result == 1) {
                            alert('콘텐츠 삭제 완료 되었습니다.');
                            location.reload();
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
