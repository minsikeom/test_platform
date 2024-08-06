@extends('layouts.adminLayout')

@section('title')
    상품 리스트
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

    .tooltip-container {
        position: relative;
        display: inline-block;
    }

    #tooltip-text {
        display: none;
        position: absolute;
        background-color: #333;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        z-index: 1;
        width: 400px;
        height: 200px;
        transition: opacity 0.3s ease; /* 툴팁이 나타날 때 부드럽게 표시되도록 설정 */
    }

    .truncated-text {
        display: flex;
        align-items: center;
        margin: 0 auto;
        width: 200px; /* 텍스트가 표시될 최대 너비 설정 */
        white-space: nowrap; /* 텍스트가 줄 바꿈되지 않도록 설정 */
        overflow: hidden; /* 넘치는 텍스트를 숨김 */
        text-overflow: ellipsis; /* 넘치는 텍스트에 대해 "..."으로 대체 */
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
                                                <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 25%">
                                                    <option value=""  {{ ($searchType)? 'selected' :'' }}>전체</option>
                                                    <option value=10  {{ ($searchType == 10)? 'selected' :'' }}>상품이름</option>
                                                    <option value=20  {{ ($searchType == 20)? 'selected' :'' }}>구성(센서)</option>
                                                </select>
                                                <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 40%" value="{{ ($searchKey)??''  }}"  placeholder="검색어">
                                                <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                                <button type="button" class="btn btn-default sizeFix" onclick="makeProduct()">상품등록</button>
                                            </div>
                                        </div>
                                <div style="height: 10px"></div>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="text-align: center;">
                                        <th>No.</th>
                                        <th>상품이름</th>
                                        <th>구성(센서) </th>
                                        <th>설명</th>
                                        <th>관리</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $countNum = (($page - 1) * $perPage)+1 @endphp
                                    @foreach($productList as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:10%">{{ $countNum }}</td>
                                            <td style="width:15%">{{ $list->product_name }}</td>
                                            <td style="width:23%;">
                                                <div  style="display: flex;text-align: center;">

                                                    <div class="truncated-text" style="display: flex; align-items: center; margin: 0 auto; margin-right: 30px;">
                                                        @if(count($list->getSensorGroupWithSensorInfo) < 3)
                                                            @foreach($list->getSensorGroupWithSensorInfo as $sensorInfo)
                                                                {{ $sensorInfo->sensor_name }} +
                                                                @if(count($list->getSensorGroupWithSensorInfo) >= 1)
                                                                    <br>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            @php $count = 0; @endphp
                                                            @foreach($list->getSensorGroupWithSensorInfo as $sensorInfo)
                                                                {{ $sensorInfo->sensor_name }} +
                                                                @php
                                                                    $count++;
                                                                    if($count >= 2) break;
                                                                @endphp
                                                                <br>
                                                            @endforeach
                                                            ...
                                                        @endif
                                                    </div>

                                                    <div class="tooltip-container">
                                                        <p id="tooltip-text">
                                                            @foreach($list->getSensorGroupWithSensorInfo as $sensorInfo)
                                                                {{ $sensorInfo->sensor_name }} +<br>
                                                            @endforeach
                                                        </p>
                                                        <span><i class="fas fa-fw fa-external-link-alt tooltip-button"></i></span>
                                                    </div>

                                                </div>
                                            </td>
                                            <td style="width:30%">

                                                <div style="display: flex;">
                                                <span style="flex: 1;">
                                                     {!! nl2br(e(mb_substr($list->product_desc, 0,10))) !!}
                                                     {{ (mb_strlen($list->product_desc,'UTF-8') > 10)? '...' : ''   }}
                                                </span>

                                                <input type="button" class="btn btn-default"
                                                       value="More" style="width: 20%;"
                                                       data-name="{{ $list->product_name }}"
                                                       data-description="{!! nl2br(e($list->product_desc)) !!}"
                                                       data-toggle="modal" data-target="#modal-description">
                                                </div>

                                            </td>
                                            <td style="display: flex;justify-content: center;gap: 10px;">
                                                <input type="button" class="btn btn-block btn-default" style="width: 30%" onclick="moveEditForm({{ $list->product_id }})" value="상품수정">
                                                <input type="button" class="btn btn-block btn-danger" style="width: 30%;margin-top:0;" onclick="deleteProduct({{ $list->product_id }})"  value="삭제">
                                            </td>
                                        </tr>
                                        @php $countNum++ @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                    <div class="pagination">
                                        @if(count($productList)>0)
                                            {{ $productList->links() }}
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
                            <h5 class="modal-title text-center w-100" id="description-modalLabel"><span id="productName"></span></h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body custom-modal-body ">
                            <h6>●상세설명</h6>
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
    <script>

        $(document).ready(function(){
            // 토글
            $('.tooltip-container').hover(function(){
                $('#tooltip-text', this).fadeIn(200); // 툴팁이 나타날 때 fadeIn 메서드를 사용하여 부드럽게 나타나도록 함
            }, function(){
                $('#tooltip-text', this).fadeOut(200); // 툴팁이 사라질 때 fadeOut 메서드를 사용하여 부드럽게 사라지도록 함
            });

            // 모달
            $('#modal-description').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // 모달을 트리거한 버튼
                var productName = button.data('name'); // 버튼의 data-title 속성 값
                var description = button.data('description'); // 버튼의 data-subject 속성 값

                // 모달의 내용 업데이트
                var modal = $(this);
                modal.find('#productName').text(productName);
                modal.find('#description').text(description);
            });
        });

        // 등록
        function makeProduct(){
            window.location='/admin/platform/product/form';
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
            window.location.href="/admin/platform/product/form/"+id;
        }

        // 삭제 버튼
        function deleteProduct(id)
        {
            if(confirm('삭제 하시겠습니까?'))
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'post',
                    url: '/admin/platform/product/delete-product-info',
                    data: {productId: id},
                    dataType: 'json',
                    success: function (result) {
                        if (result == 1) {
                            alert('상품 삭제 완료 되었습니다.');
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
