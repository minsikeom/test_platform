@extends('layouts.adminLayout')

@section('title')
    테마 리스트
@endsection

@section('style')
<style>

    .buttons-and-search-container {
        display: flex;
        justify-content: space-between; /* 좌우 정렬을 명확하게 */
        align-items: center; /* 수직 중앙 정렬 */
        gap: 10px;
        width: 100%;
    }

    .buttons-container {
        display: flex;
        gap: 10px;
        width: 100%;
        margin-right: auto; /* 좌측 정렬 */
    }

    .searchGroup {
        display: flex;
        gap: 10px;
        align-items: center;
        width: 30%;
        margin-left: auto; /* 우측 정렬 */
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px; /* 상단 간격 추가 */
    }

    .sizeFix {
        width: 30%;
    }

    .custom-modal-size {
        max-width: 50%;
    }
    .custom-modal-body {
        max-height: 40vh; /* 모달 바디의 최대 높이를 70%의 뷰포트 높이로 설정 */
        overflow-y: auto; /* 모달 바디 내용이 넘칠 경우 스크롤 생성 */
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
                                            <div class="searchGroup">
                                                <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 32%">
                                                    <option value=""  {{ ($searchType)? 'selected' :'' }}>전체</option>
                                                    <option value=10  {{ ($searchType == 10)? 'selected' :'' }}>테마이름</option>
                                                </select>
                                                <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 40%" value="{{ ($searchKey)??''  }}"  placeholder="검색어">
                                                <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                                <button type="button" class="btn btn-default sizeFix" onclick="makeTheme()">테마등록</button>
                                            </div>
                                        </div>
                                <div style="height: 10px"></div>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="text-align: center;">
                                        <th>No.</th>
                                        <th>테마이름</th>
                                        <th>설명</th>
                                        <th>관리</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $countNum = (($page - 1) * $perPage)+1 @endphp
                                    @foreach($themeList as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:10%">{{ $countNum }}</td>
                                            <td style="width:30%">{{ $list->theme_name }}</td>
                                            <td style="width:30%">
                                                <div style="display: flex;">
                                                <span style="flex: 1;">
                                                      {!! nl2br(e(mb_substr($list->theme_desc, 0, 30))) !!}
                                                </span>
                                                    <input type="button" class="btn btn-default"
                                                           value="More" style="width: 20%;"
                                                           data-theme_name="{{  $list->theme_name }}"
                                                           data-description="{!! nl2br(e($list->theme_desc)) !!}"
                                                           data-toggle="modal" data-target="#modal-description">
                                                </div>
                                            </td>
                                            <td style="display: flex;justify-content: center;gap: 10px;">
                                                <input type="button" class="btn btn-block btn-success " style="width: 30%;margin-top:0;" onclick="moveEditForm({{ $list->theme_id }})" value="콘텐츠">
                                                <input type="button" class="btn btn-block btn-default" style="width: 30%;margin-top:0;" onclick="moveEditForm({{ $list->theme_id }})" value="테마수정">
                                                <input type="button" class="btn btn-block btn-danger" style="width: 30%;margin-top:0;" onclick="deleteGenre({{ $list->theme_id }})"  value="삭제">
                                            </td>
                                        </tr>
                                        @php $countNum++ @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                    <div class="pagination">
                                        @if(count($themeList)>0)
                                            {{ $themeList->links() }}
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

            <!-- description 모달 -->
            <div class="modal fade" id="modal-description" tabindex="-1" role="dialog" aria-labelledby="description-modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered custom-modal-size" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-red" style="background-color: darkgrey">
                            <h5 class="modal-title text-center w-100" id="description-modalLabel"><span id="themeName"></span></h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body custom-modal-body ">
                            <h6>●Description</h6>
                            <div class="form-group">
                                <textarea class="form-control" id="description" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-secondary" data-dismiss="modal" value="닫기">
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <!-- /.content -->
@endsection

@section('script')
    <!-- Page specific script -->
    <script>

        function makeTheme(){
            window.location='/admin/platform/theme/form';
        }

        $(document).ready(function() {
            @if(!$searchType)
                $('#searchInput').hide();
            @endif

            $('#modal-description').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // 모달을 트리거한 버튼
                var themeName = button.data('theme_name'); // 버튼의 data-subject 속성 값
                var description = button.data('description'); // 버튼의 data-subject 속성 값

                // 모달의 내용 업데이트
                var modal = $(this);
                modal.find('#themeName').text(themeName);
                modal.find('#description').text(description);
            });

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

        // 검색 버튼 눌렀을때 페이지 초기화 후 폼전송
        function search()
        {
            $('#pages').val(1);
            $('#submitForm').submit();
        }

        // 수정 버튼
        function moveEditForm(id)
        {
            window.location.href="/admin/platform/genre/form/"+id;
        }

        // 삭제 버튼
        function deleteGenre(id,url)
        {
            if(confirm('삭제 하시겠습니까?'))
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'post',
                    url: '/admin/platform/genre/delete-genre-info',
                    data: {contentsGenreId: id,path:url},
                    dataType: 'json',
                    success: function (result) {
                        if (result == 1) {
                            alert('장르 삭제 완료 되었습니다.');
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
