@extends('layouts.adminLayout')

@section('title')
    장르 등록/수정 폼
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

        .nav-link {
            color: black;
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .red-underline {
            color: red;
            text-decoration: underline;
        }

        .preview-container {
            //display: flex;
            flex-wrap: wrap;
            gap: 10px; /* 이미지 사이 간격 */
        }

        .preview-item {
            width: 780px; /* 최대 너비 설정 */
            height: 240px; /* 최대 높이 설정 */
            background-color: #f2f2f2; /* 회색 배경색 적용 */
            border-radius: 5px;
            text-align: center;
        }

        .preview-item img {
            max-width: 780px; /* 이미지의 최대 너비 설정 */
            max-height: 240px; /* 이미지의 최대 높이 설정 */
            margin: auto; /* 이미지 가운데 정렬 */
        }

        #preResource1 {
            width: 780px;
            height: 240px;
            /*border: 1px solid #000;*/
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #preResource1 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    </style>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <form id="form1"
                                  action="{{ !($genreInfo)?  route('insertGenreInfo'): route('updateGenreInfo')  }}"
                                  method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="contentsGenreId" value={{ ($genreInfo->contents_genre_id)??'' }}>

                                <div class="form-group row">
                                    <div class="col-sm-12 text-center label-color">
                                        <h3>{{ !($genreInfo)? '장르 추가':'장르 수정'  }}</h3>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="" style="margin: auto;">장르 이름</label>
                                    </div>
                                    <div class="px-2"></div>
                                        <div class="col-sm-2" style="margin-top: 20px; margin-bottom: 20px;" >
                                            <input type="hidden" id="nameCheck" value="1">
                                            <input type="text" class="form-control" id="textKr"
                                                   name="textKr"
                                                   maxlength="4"
                                                   style="text-align: center;"
                                                   onchange="changeCheck('nameCheck')"
                                                   value="{{ ($genreInfo->getContentsGenreGroupWithBothTexts[0]->text)??''  }}"
                                                   oninput="restrictToKorean(this)"
                                                   required>
                                            <span style="color: red">*한글 5글자 제한</span><br>
                                            <span id="name" style="font-size: 14px"></span>
                                        </div>
                                        <div class="col-sm-1" style="margin-top: 20px; margin-bottom: 20px;">
                                            <input type="button" class="btn btn-primary ms-1"  id="nameCheckButton"
                                                   onclick="duplicateCheck('name')" value="중복 확인">
                                        </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="" style="margin: auto;">장르 영문이름</label>
                                    </div>
                                    <div class="px-2"></div>
                                        <div class="col-sm-2" style="margin-top: 20px; margin-bottom: 20px;">
                                            <input type="hidden" id="enNameCheck" value="1">
                                            <input type="text" class="form-control" id="textEn"
                                                   name="textEn"
                                                   maxlength="10"
                                                   style="text-align: center;"
                                                   onchange="changeCheck('enNameCheck')"
                                                   value="{{ ($genreInfo->getContentsGenreGroupWithBothTexts[1]->text)??''  }}"
                                                   oninput="restrictToEnglish(this)"
                                                   required>
                                            <span style="color: red;">*영문 10글자 제한</span> <br>
                                            <span id="enName" style="font-size: 14px"></span>
                                        </div>
                                        <div class="col-sm-1" style="margin-top: 15px; margin-bottom: 15px;">
                                            <input type="button" class="btn btn-primary ms-2" id="enNameCheckButton"
                                                   onclick="duplicateCheck('enName')" value="중복 확인">
                                        </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="countryTab" style="margin: auto;">장르 배너</label>
                                    </div>
                                    <div class="px-2"></div>

                                    <div class="col-md-8"  style="margin-top: 20px; margin-bottom: 20px;">

                                        <div class="tab-content" id="countryTabContent">
                                            <div class="tab-pane fade show active" id="country1" role="tabpanel"
                                                 aria-labelledby="country1-tab">
                                                <div class="row">

                                                    <div class="col-md-3 mb-3">
                                                        <label for="resource1">Banner(ex. 1920x1080)</label>
                                                        <input type="file" class="form-control" id="resource1" name="resourceBanner"
                                                               onchange="previewImage(this, 'preResource1')" {{ !isset($genreInfo)??'required' }}>
                                                        <span style="color: red">*jpg,png,JSON FILE 업로드 가능</span>
                                                    </div>

                                                    <div class="col-md-9 mb-9">
                                                        <label for="preResource1">Preview</label>
                                                        <div class="preview-container">
                                                            <div class="preview-item">
                                                                <div id="preResource1">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="sort" style="margin: auto;">노출 정렬순서</label>
                                    </div>
                                    <div class="px-2"></div>
                                    <div class="col-sm-2" style="margin-top: 20px; margin-bottom: 20px;" >
                                        <input type="text" class="form-control" id="sort"
                                               name="sort"
                                               value="{{ ($genreInfo->sort)?? $genreMaxSort  }}"
                                               style="width: 50%; text-align: center;"
                                               readonly>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-sm-6 offset-sm-3 text-center">
                        <input type="button" onclick="submitForm()"  class="btn btn-primary ms-2"
                               style="margin-right: 10px;width: 10%"
                               value="{{ !($genreInfo)? '등록':'수정' }}">
                        <input type="button" class="btn btn-primary ms-2" style="width: 10%"
                               onclick="back()" value="취소">
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

        // 초기화 셋팅
        window.onload = function() {
            @if(!empty($genreInfo))
                createPreview(<?php echo $genreInfo ?>, 'preResource1');
                $('#enNameCheckButton').prop('disabled', true);
                $('#nameCheckButton').prop('disabled', true);
                $('#enNameCheck').val(0);
                $('#nameCheck').val(0);
            @endif
        };

        // 입력 값에서 한글 이외의 문자를 제거합니다.
        function restrictToKorean(input) {
            input.value = input.value.replace(/[^\uAC00-\uD7A3]+/g, '');
        }

        // 입력 값에서 영어 이외의 문자를 제거합니다.
        function restrictToEnglish(input) {
            input.value = input.value.replace(/[^a-zA-Z]+/g, '');
        }

        // 등록
        function submitForm() {

            if ($('#nameCheck').val() == 1) {
                alert('장르 이름 중복 확인을 해주세요.');
                return false;
            } else if ($('#enNameCheck').val() == 1) {
                alert('장르 영문 이름 중복 확인을 해주세요.');
                return false;
            }

            @if(empty($genreInfo))
                if (!$('#resource1').val()) {
                    alert('배너를 입력해주세요.');
                    return false;
                }
            @endif

            $('#form1').submit();
        }

        //뒤로가기
        function back() {
            window.history.back();
        }

        // 이름,영문이름 변경이 있을때 중복확인 초기화
        function changeCheck(target) {
            if (target === 'enNameCheck') {
                if ($('#enNameCheck').val() == 0) {
                    alert('장르 영문 이름 변경 되어 다시 중복확인을 눌러주세요.');
                    $('#enNameCheckButton').prop('disabled', false);
                }
            } else{
                if($('#nameCheck').val() == 0){
                    alert('장르 이름 변경 되어 다시 중복확인을 눌러주세요.');
                    $('#nameCheckButton').prop('disabled', false);
                }
            }

            $('#' + target).val(1);
        }

        // 장르 이름 , 영문 이름 중복 체크
        function duplicateCheck(type) {
            if (type === 'enName') {
                var text = $('#textEn').val();
                var checkPosition = 'enNameCheck';
                var languageText ='영문 이름';
                var button  =  "enNameCheckButton";
            } else {
                var text = $('#textKr').val();
                var checkPosition = 'nameCheck';
                var languageText ='이름';
                var button  =  "nameCheckButton";
            }

            if(!text)
            {
                alert('입력 후 중복 확인 버튼을 눌러주세요');
                return false;
            }

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/admin/platform/genre/exists-genre-name',
                type: 'POST',
                data: {text: text},
                success: function (data) {
                    if (data == 1) { // 중복
                        $('#' + type).text('중복된 '+languageText+'입니다. 다시 입력해주세요.');
                        $('#' + type).css('color', 'red');
                        $('#' + checkPosition).val(1);
                    } else if (data == 0) { // 사용가능
                        $('#' + type).text('사용하실 수 있는 '+languageText+'입니다.');
                        $('#' + type).css('color', 'green');
                        $('#' + checkPosition).val(0);
                        $('#' + button).prop('disabled', true);
                    } else {  // 에러
                        alert('에러가 발생 하였습니다.');
                    }

                },
                error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                },
            })
        }

        // 장르 베너 미리보기
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var fileType = file.type.split('/')[1]; // 파일의 확장자 가져오기
                var allowedExtensions = ["json", "jpg","jpeg", "png"];
                console.log(fileType);
                if (!allowedExtensions.includes(fileType)) {
                    alert("허용되지 않은 파일 확장자입니다.");
                    $('#resource1').val('');
                    return false;
                }

                if (file.type.includes('image')) {  // 이미지 파일인 경우
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var img = new Image();
                        img.onload = function () {
                            // 미리보기 영역 크기 설정
                            var previewWidth = 780; // 미리보기 영역의 너비
                            var previewHeight = 240; // 미리보기 영역의 높이

                            // 이미지 크기 조정
                            var width = img.width;
                            var height = img.height;
                            var aspectRatio = width / height;

                            if (width > height) {
                                // 이미지가 너비가 큰 경우
                                width = previewWidth;
                                height = width / aspectRatio;
                            } else {
                                // 이미지가 높이가 크거나 같은 경우
                                height = previewHeight;
                                width = height * aspectRatio;
                            }

                            // 조정된 크기로 이미지 미리보기에 추가
                            var previewItem = document.getElementById(previewId);
                            previewItem.innerHTML = ''; // 기존에 있는 이미지 삭제
                            var previewImg = new Image();
                            previewImg.src = e.target.result;
                            previewImg.style.display = 'block'; // 이미지 보이기
                            previewImg.style.width = width + 'px'; // 이미지 너비 조정
                            previewImg.style.height = height + 'px'; // 이미지 높이 조정
                            previewItem.appendChild(previewImg);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.includes('json')) {     // JSON 파일일 경우에는 로티 애니메이션으로 변환하여 표시
                    // JSON 파일일 경우에는 로티 애니메이션으로 변환하여 표시
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var animationData = JSON.parse(e.target.result);
                        // 미리보기 화면 초기화
                        document.getElementById('preResource1').innerHTML = '';
                        // 로티 데이터 입력
                        lottie.loadAnimation({
                            container: document.getElementById('preResource1'), // preResource1 요소에 애니메이션을 표시
                            renderer: 'svg',
                            loop: true,
                            autoplay: true,
                            animationData: animationData
                        });
                    };
                    reader.readAsText(file);
                }
            }
        }

        // 미리보기 생성 함수
        function createPreview(genreInfo, previewId) {
            if (genreInfo) {
                if (genreInfo.f_format === 'image') {
                    // 이미지인 경우 이미지 태그로 표시
                    var img = document.createElement('img');
                    img.src = '{{ env('NCLOUD_OBJECT_STORAGE_URL')."/" }}'+genreInfo.f_path + '/' + genreInfo.f_id
                    document.getElementById(previewId).appendChild(img);
                } else if (genreInfo.f_format === 'json') {
                    // JSON 파일인 경우 lottie로 애니메이션 표시
                    lottie.loadAnimation({
                        container: document.getElementById(previewId),
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        path: '{{ env('NCLOUD_OBJECT_STORAGE_URL')."/" }}'+genreInfo.f_path + '/' + genreInfo.f_id
                    });
                }
            }
        }

        // 영어 리소스 제거
        function deleteResource(id){
            confirm('리소스 삭제 하시겠습니까?')
            {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: '/admin/contents/delete-contents-resource',
                    type: 'POST',
                    data: {contentsResourceId: id},
                    success: function (data) {
                        if (data == 1) { // 제거 완료
                            alert('리소스 삭제 완료 하였습니다.');
                            location.reload();
                        } else {  // 에러
                            alert('에러가 발생 하였습니다.');
                        }
                    },
                    error: function (request, status, error) {
                        alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    },
                })
            }
        }

    </script>
@endsection
