@extends('layouts.adminLayout')

@section('title')
    센서 리스트
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
                                        <div class="buttons-and-search-container">
                                            <div class="searchGroup">
                                                <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 25%">
                                                    <option value=""  {{ ($searchType)? 'selected' :'' }}>전체</option>
                                                    <option value=10  {{ ($searchType == 10)? 'selected' :'' }}>센서코드</option>
                                                    <option value=20  {{ ($searchType == 20)? 'selected' :'' }}>센서타입</option>
                                                    <option value=30  {{ ($searchType == 30)? 'selected' :'' }}>이름</option>
                                                </select>
                                                <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 40%" value="{{ ($searchKey)??''  }}"  placeholder="검색어">
                                                <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                                <button type="button" class="btn btn-default sizeFix" onclick="makeSensor()">센서등록</button>
                                            </div>
                                        </div>
                                <div style="height: 10px"></div>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="text-align: center;">
                                        <th>No.</th>
                                        <th>센서코드</th>
                                        <th>센서타입</th>
                                        <th>이름</th>
                                        <th>Icon</th>
                                        <th>관리</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $countNum = (($page - 1) * $perPage)+1 @endphp
                                    @foreach($sensorList as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:6%">{{ $countNum }}</td>
                                            <td style="width:20%">{{ $list->sensor_code }}</td>
                                            <td style="width:20%">{{ $list->getSensorTypeInfo->sensor_type_name }}</td>
                                            <td style="width:20%">{{ $list->sensor_name }}</td>
                                            <td style="width:10%;text-align: center;">
                                                <img src="{{ env('NCLOUD_OBJECT_STORAGE_URL')."/sensor/".$list->sensor_code."/".$list->sensor_icon_f_id }}" style="width: 60px;">
                                            </td>
                                            <td style="display: flex;justify-content: center;gap: 10px;">
                                                <input type="button" class="btn btn-block btn-default" style="width: 30%" onclick="moveEditForm({{ $list->sensor_id }})" value="정보수정">
                                                <input type="button" class="btn btn-block btn-danger" style="width: 30%;margin-top:0;" onclick="deleteSensor({{ $list->sensor_id }},'{{ $list->sensor_exe_f_path."/".$list->sensor_icon_f_id }}')" value="삭제" >
                                            </td>
                                        </tr>
                                        @php $countNum++ @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                    <div class="pagination">
                                        @if(count($sensorList)>0)
                                            {{ $sensorList->links() }}
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

        function makeSensor(){
            window.location='/admin/platform/sensor/form';
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
            window.location.href="/admin/platform/sensor/form/"+id;
        }

        // 삭제 버튼
        function deleteSensor(id,url)
        {
            if(confirm('삭제 하시겠습니까?'))
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'post',
                    url: '/admin/platform/sensor/delete-sensor-info',
                    data: {sensorId: id,path:url},
                    dataType: 'json',
                    success: function (result) {
                        if (result == 1) {
                            alert('센서 삭제 완료 되었습니다.');
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
