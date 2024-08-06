@extends('layouts.adminLayout')

@section('title')
    콘텐츠 버전 관리
@endsection

@section('style')
    <style>

        .form-group.row {
            border: 1px solid #ccc;
            padding: 1px;
            margin-bottom: 1px;
            border-radius: 3px;
        }

        .label-color {
            background-color: #99CCCC; /* 연초록색 배경색 */
            padding: 5px; /* 내부 여백 */
            border-radius: 5px; /* 모서리를 둥글게 */
            text-align: center; /* 텍스트 가운데 정렬 */
        }

        .font-style {
            color: #ee3148;
            font-size:1.2rem;
        }

        .custom-modal-size {
            max-width: 50%;
        }
        .custom-modal-body {
            max-height: 40vh; /* 모달 바디의 최대 높이를 70%의 뷰포트 높이로 설정 */
            overflow-y: auto; /* 모달 바디 내용이 넘칠 경우 스크롤 생성 */
        }

        .pagination {
            display: flex;justify-content: center;
        }
    </style>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="form1" action="{{ route('insertVersionWithExeFile') }}" method="post" enctype="multipart/form-data" onsubmit="submitForm();return false;">
                                @csrf
                                <input type="hidden" name="contentsId" value={{ $contentsId }}>
                                <input type="hidden" name="contentsEnName" value="{{ $contentsInfo->getContentsText[1]->text_title }}" >

                                <div class="form-group row">
                                    <div class="col-sm-12 text-center label-color">
                                        <h4>콘텐츠 버전관리<br>
                                            Contents Name : <span style="color: red">{{ $contentsInfo->getContentsText[1]->text_title }}</span>
                                        </h4>
                                    </div>
                                </div>

                                <div class="form-group row ">
                                    <label for="current_version" class="col-sm-3 col-form-label label-color">현재 버전(Current Version)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control font-style" style="text-align: center" id="current_version" value="Ver {{ $contentsInfo->version }}" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label label-color">실행 파일</label>
                                <div class="px-2"></div>
                                <div class="col-sm-4">
                                    <input type="file" class="form-control" id="content_executable" name="contentExeFile" onchange="checkFileWithEnName()" required>
                                    <span style="color:red;">●주의사항 :</span><span> 파일명은 영문명과 동일해야 합니다.</span>
                                </div>
                        </div>

                        <div class="form-group row" style="margin-bottom: 10px;">
                                    <label for="version" class="col-sm-3 col-form-label label-color">버전 추가(Add Version)</label>
                                    <div class="px-2"></div>
                                        <div class="col-sm-8">
                                            <input type="hidden" class="form-control" name="version" value="{{ $contentsInfo->version }}">
                                            <label for="" class="col-sm-1 col-form-label" style="font-size: 18px;">MAJOR : </label>
                                            <input type="radio" id="radio1" name="changeVersion" value=1 style="transform: scale(2);" checked>
                                            &nbsp;
                                            <label for="" class="col-sm-1 col-form-label" style="font-size: 18px;">MINOR : </label>
                                            <input type="radio" id="radio1" name="changeVersion" value=2 style="transform: scale(2);">
                                            &nbsp;
                                            <label for="selectBox3" class="col-sm-1 col-form-label" style="font-size: 18px;">PATCH : </label>
                                            <input type="radio" id="radio1" name="changeVersion" value=3 style="transform: scale(2);">
                                            <br>
                                            <span style="color:red;">●MAJOR :</span><span>기존 API 변경 및 삭제 되거나 하위 호환이 되지 않은 버전</span>
                                            <br>
                                            <span style="color:red;">●MINOR :</span><span>신규 기능이 추가되거나 개선되었고 하위 호환이 되는 버전</span>
                                            <br>
                                            <span style="color:red;">●PATCH :</span><span>버그 수정이 되었고 하위 호환이 되는 버전</span>
                                            <textarea class="form-control" id="description1" name="description" placeholder="add Version" rows="6" style="width: 80%;margin-bottom: 20px;" required></textarea>
                                        </div>
                                </div>

                                <div class="col-sm-6 offset-sm-3 text-center">
                                    <input type="submit" class="btn btn-primary ms-2" style="margin-right: 10px;width: 10%" value="업로드">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form id="submitForm">
                                <h3>Version History</h3>
                                <input type="hidden" name="page" value="{{ $page }}">
                                <table class="table table-bordered table-striped" style="margin-top:3px">
                                    <thead>
                                    <tr style="text-align: center;">
                                        <th>Version</th>
                                        <th>Release Date</th>
                                        <th>State</th>
                                        <th>Description</th>
                                        <th>File</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($versionList as $list)
                                        <tr style="text-align: center;">
                                            <td style="width:8%">
                                                @if($list['status'] === 'R')
                                                    <span style="color: red">{{ $list['version'] }}(Now)</span>
                                                @else
                                                    <span style="color: black">{{ $list['version'] }}</span>
                                                @endif
                                            </td>
                                            <td style="width:10%">{{ $list['created_at']->format('Y-m-d') }}</td>
                                            <td style="width:8%">
                                                @if($list['status'] === 'R')  {{--릴리즈--}}
                                                <input type="button" class="btn btn-block btn-danger btn-sm" value="Rollback"
                                                       data-version="{{ $list['version'] }}"
                                                       data-version_history_id={{ $list['contents_version_history_id'] }}
                                                       data-toggle="modal" data-target="#modal-rollback">
                                                @elseif($list['status'] === 'O') {{--올드버전--}}
                                                    Old Version
                                                @else   {{--롤백--}}
                                                    <span style="color:grey">RollBack</span>
                                                @endif
                                            </td>
                                            <td style="width:20%">
                                                <div style="display: flex;">
                                                <span style="flex: 1;">
                                                     {!! nl2br(e(mb_substr($list['description'], 0, 30))) !!}
                                                </span>
                                                    <input type="button" class="btn btn-default"
                                                           value="More" style="width: 20%;"
                                                           data-version="{{ $list['version'] }}"
                                                           data-description="{!! nl2br(e($list['description'])) !!}"
                                                           data-toggle="modal" data-target="#modal-description">
                                                </div>
                                            </td>
                                            <td style="width:8%">
                                                <a href="{{ app()->make('App\Services\FileManageService')->generateTemporaryDownLoadUrl($path.$list->version."/".$contentsInfo->f_exe_name) }}">
                                                    <i class="fa fa-fw fa-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                            <div class="pagination">
                                @if(count($versionList)>0)
                                    {{ $versionList->links() }}
                                @endif
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

        <!-- Rollback 모달 -->
        <div class="modal fade" id="modal-rollback" tabindex="-1" role="dialog" aria-labelledby="roll-back-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered custom-modal-size" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-center w-100" id="roll-back-modalLabel">Ver <span id="modal-version"></span></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <input type="hidden" id="versionHistoryId">
                        <h6>
                            <span style="color: red;"> ●Rollback</span>
                            진행 사유를 입력해주세요.
                        </h6>
                        <div class="form-group">
                            <textarea class="form-control" id="rollback-reason" rows="5" ></textarea>
                        </div>
                        <h6>
                            <span style="color: red;">●Check!</span>
                            한번 진행된 Rollback 은 다시 되돌릴 수 없습니다. 정말 RollBack 를 진행하시겠습니까?
                        </h6>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-danger" onclick="rollBack()" value="Rollback 진행">
                        <input type="button" class="btn btn-secondary" data-dismiss="modal" value="취소">
                    </div>
                </div>
            </div>
        </div>

        <!-- description 모달 -->
        <div class="modal fade" id="modal-description" tabindex="-1" role="dialog" aria-labelledby="description-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered custom-modal-size" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-center w-100" id="description-modalLabel">Ver <span id="modal-version"></span></h5>
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
                        <input type="button" class="btn btn-secondary" data-dismiss="modal" value="취소">
                    </div>
                </div>
            </div>
        </div>

    </section>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#modal-rollback').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // 모달을 트리거한 버튼
                var version = button.data('version'); // 버튼의 data-version 속성 값
                var versionHistoryId = button.data('version_history_id'); // 버튼의 data-contentsVersionHistoryId 속성 값
                var modal = $(this);
                modal.find('#modal-version').text(version);
                modal.find('#versionHistoryId').val(versionHistoryId);
            });

            $('#modal-description').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // 모달을 트리거한 버튼
                var version = button.data('version'); // 버튼의 data-title 속성 값
                var description = button.data('description'); // 버튼의 data-subject 속성 값

                // 모달의 내용 업데이트
                var modal = $(this);
                modal.find('#modal-version').text(version);
                modal.find('#description').text(description);
            });
        });

        // 롤백
        function rollBack(){
            var versionHistoryId=$('#versionHistoryId').val();
            var description=$('#rollback-reason').val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/admin/contents/manage/exe-file-rollBack',
                type: 'POST',
                data: {versionHistoryId: versionHistoryId,description:description},
                success: function (data) {
                    if (data == 1) { // 완료
                        alert('롤백 처리 완료되었습니다.');
                        location.reload();
                    } else if(data == 0){
                        alert('롤백 할 이전 버전이 없습니다.');
                    } else {  // 에러
                        alert('에러가 발생 하였습니다.');
                    }
                },
                error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                },
            })
        }

        // 다운로드
        function fileDownload(path){
            location.href='/admin/contents/manage/exe-file-version/download-url?path='+path;
        }

        // 등록
        function submitForm() {
            $('#form1').submit();
        }

        //뒤로가기
        function back() {
            window.history.back();
        }

        // 실행 파일 입력시 영문 이름과 비교
        function checkFileWithEnName() {
            var enName = '{{ $contentsInfo->getContentsText[1]->text_title }}'; // 영문 이름
            var executableFile = $('#content_executable').val(); // 실행 파일
            var fileName = executableFile.split('\\').pop(); // 파일 이름 추출
            var fileNameWithoutExtension = fileName.split('.')[0]; // 확장자 제외한 파일 이름 추출
            if (fileName.split('.')[1] !== 'zip') {
                alert('실행파일 확장자는 zip만 가능합니다.');
                $('#content_executable').val('');
            } else if (enName !== fileNameWithoutExtension) {
                alert('실행파일은 확장자 제외하고 영문 이름과 동일 해야합니다. ');
                $('#content_executable').val('');
            }
        }

    </script>
@endsection
