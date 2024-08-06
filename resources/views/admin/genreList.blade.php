@extends('layouts.adminLayout')

@section('title')
    장르 리스트
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
                                        <div class="buttons-and-search-container">
                                            <div class="searchGroup">
                                                <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 25%">
                                                    <option value=""  {{ ($searchType)? 'selected' :'' }}>전체</option>
                                                    <option value=10  {{ ($searchType == 10)? 'selected' :'' }}>한글이름</option>
                                                    <option value=20  {{ ($searchType == 20)? 'selected' :'' }}>영문이름</option>
                                                </select>
                                                <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 40%" value="{{ ($searchKey)??''  }}"  placeholder="검색어">
                                                <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                                <button type="button" class="btn btn-default sizeFix" onclick="makeGenre()">장르등록</button>
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
                                        <th>장르 한글 이름</th>
                                        <th>장르 영문 이름</th>
                                        <th>관리</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $countNum = (($page - 1) * $perPage)+1 @endphp
                                    @foreach($genreList as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:30%">{{ $countNum }}
                                                <i class="fa fa-fw fa-angle-up" onclick="genreSortBy({{ $list->contents_genre_id  }},'up')"></i>
                                                <i class="fa fa-fw fa-angle-down" onclick="genreSortBy({{ $list->contents_genre_id }},'down')"></i>
                                            </td>
                                            @foreach( $list->getContentsGenreGroupWithBothTexts as $text)
                                                <td style="width:20%">
                                                    {{ $text-> text}}
                                                </td>
                                            @endforeach
                                            <td style="display: flex;justify-content: center;gap: 10px;">
                                                <input type="button" class="btn btn-block btn-default" style="width: 30%" onclick="moveEditForm({{ $list->contents_genre_id }})" value="정보수정">
                                                <input type="button" class="btn btn-block btn-danger" style="width: 30%;margin-top:0;" onclick="deleteGenre({{ $list->contents_genre_id }},'{{ $list->f_path }}')"  value="삭제">
                                            </td>
                                        </tr>
                                        @php $countNum++ @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                    <div class="pagination">
                                        @if(count($genreList)>0)
                                            {{ $genreList->links() }}
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
        function genreSortBy(id, option){
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'post',
                url: '/admin/platform/genre/edit-genre-sort-count',
                data: {contentsGenreId:id,option:option},
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

        function makeGenre(){
            window.location='/admin/platform/genre/form';
        }

        $(document).ready(function() {
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
