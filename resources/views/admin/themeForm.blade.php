@extends('layouts.adminLayout')

@section('title')
    테마 등록/수정 폼
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
            width: 380px; /* 최대 너비 설정 */
            height: 120px; /* 최대 높이 설정 */
            background-color: #f2f2f2; /* 회색 배경색 적용 */
            border-radius: 5px;
            text-align: center;
        }

        .preview-item img {
            max-width: 380px; /* 이미지의 최대 너비 설정 */
            max-height: 120px; /* 이미지의 최대 높이 설정 */
            margin: auto; /* 이미지 가운데 정렬 */
        }

        #preResource1 {
            width: 380px;
            height: 120px;
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
                            <form id="form1" action="{{ !($themeInfo)?  route('insertThemeInfo'): route('updateThemeInfo')  }}"
                                  method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="themeId" value={{ ($themeInfo->theme_id)??'' }}>

                                <div class="form-group row">
                                    <div class="col-sm-12 text-center label-color">
                                        <h3>{{ !($themeInfo)? '테마 추가':'테마 수정'  }}</h3>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="" style="margin: auto;">테마 이름</label>
                                    </div>
                                    <div class="px-2"></div>
                                        <div class="col-sm-2" style="margin-top: 20px; margin-bottom: 20px;" >
                                            <input type="hidden" id="nameCheck" value="1">
                                            <input type="text" class="form-control" id="themeName"
                                                   name="themeName"
                                                   maxlength="10"
                                                   style="text-align: center;"
                                                   onchange="changeCheck('nameCheck')"
                                                   value="{{ ($themeInfo->theme_name)??''  }}"
                                                   required>
                                            <span style="color: red">*한글 6글자 제한,영문 10글자 제한(한글,영문 입력가능)</span><br>
                                            <span id="name" style="font-size: 14px"></span>
                                        </div>
                                        <div class="col-sm-1" style="margin-top: 20px; margin-bottom: 20px;">
                                            <input type="button" class="btn btn-primary ms-1"  id="nameCheckButton"
                                                   onclick="duplicateCheck('name')" value="중복 확인">
                                        </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex">
                                        <label for="themeDescription" style="margin: auto;">테마 설명</label>
                                    </div>
                                    <div class="px-2"></div>
                                    <div class="col-sm-8" style="margin-top: 10px; margin-bottom: 20px;">
                                        <textarea id="themeDescription" name="themeDescription" class="form-control mt-3" style="width: 100%" placeholder="설명을 입력하세요." rows="5">@if(isset($productInfo->product_desc)) {!! nl2br(e($productInfo->product_desc)) !!} @endif</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex">
                                        <label for="themeDescription" style="margin: auto;">Theme Resource</label>
                                    </div>
                                    <div class="px-2"></div>
                                    <div class="col-sm-5" style="margin-top: 15px; margin-bottom: 20px;">
                                        <div style="display: flex; align-items: center;">
                                            <select id="sensorTypeSelect" onchange="" style="width: 37%;" class="form-control">
                                                <option value="">NEW</option>
                                            </select>
                                            <input type="button" class="btn btn-primary ms-1" onclick="" value="COPY" style="margin-left: 10px;">
                                        </div>
                                        <span style="color: red; display: block; margin-top: 5px;">*테마 COPY 시 해당테마의 모든 Resource가 복사되며<br>항목 별 수정이 가능합니다.</span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="countryTab" style="margin: auto;">메인 배경이미지</label>
                                    </div>
                                    <div class="px-2"></div>

                                    <div class="col-md-8"  style="margin-top: 20px; margin-bottom: 20px;">
                                        <div class="tab-content" id="countryTabContent">
                                            <div class="tab-pane fade show active" id="country1" role="tabpanel"
                                                 aria-labelledby="country1-tab">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div id="resourceList" class="d-flex flex-wrap"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex">
                                        <label for="" style="margin: auto;">배경 음악</label>
                                    </div>
                                    <div class="px-2"></div>

                                    <div class="col-md-8" style="margin-top: 20px; margin-bottom: 20px;">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="backgroundMusicName" style="margin: auto;">이름</label>
                                                <span style="border-bottom: 1px solid #000; display: block; margin-bottom: 10px"></span>
                                                <input type="text" class="form-control" id="backgroundMusicName" name="backgroundMusicName" placeholder="배경음악 이름" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="resourceBackgroundMusics" style="margin: auto;">File</label>
                                                <span style="border-bottom: 1px solid #000; display: block; margin-bottom: 10px"></span>
                                                <input type="file" class="form-control" id="resourceBackgroundMusics" onchange="previewImage(this, 'preResourceBackgroundMusic')" name="resourceBackgroundMusic" accept="audio/*" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="preResourceBackgroundMusic" style="margin: auto;">Play</label>
                                                <span style="border-bottom: 1px solid #000; display: block; margin-bottom: 10px"></span>
                                                <div id="preResourceBackgroundMusic"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex">
                                        <label for="" style="margin: auto;">로고</label>
                                    </div>
                                    <div class="px-2"></div>

                                    <div class="col-md-8" style="margin-top: 20px; margin-bottom: 20px;">
                                        <div class="row">
                                            <div class="col-md-4 mb-4">
                                                <label for="resourceLight">Light (1000x204)</label>
                                                <input type="file" class="form-control" id="resourceLight" style="width: 98%;  margin-bottom: 10px" name="resourceLightLogo"
                                                       onchange="previewImage(this, 'preResourceLightLogo')" required>
                                                <div class="preview-container">
                                                    <div class="preview-item">
                                                        <div id="preResourceLightLogo"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-4">
                                                <label for="resourceDark">Dark (1000x204)</label>
                                                <input type="file" class="form-control" id="resourceDark" style="width: 98%; margin-bottom: 10px" name="resourceDarkLogo"
                                                       onchange="previewImage(this, 'preResourceDarkLogo')" required>
                                                <div class="preview-container">
                                                    <div class="preview-item" style="background-color: darkgrey">
                                                        <div id="preResourceDarkLogo"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex">
                                        <label for="" style="margin: auto;">서브 배경이미지</label>
                                    </div>
                                    <div class="px-2"></div>

                                    <div class="col-md-8" style="margin-top: 20px; margin-bottom: 20px;">
                                        <div class="row">
                                            <div class="col-md-4 mb-4">
                                                <label for="resourceLight">Light (1920x1080)</label>
                                                <input type="file" class="form-control" id="resourceLightBackground" style="width: 98%;  margin-bottom: 10px" name="resourceLightBackGroundSub"
                                                       onchange="previewImage(this, 'preResourceLightBackgroundSub')" required>
                                                <div class="preview-container">
                                                    <div class="preview-item">
                                                        <div id="preResourceLightBackgroundSub"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-4">
                                                <label for="resourceDark">Dark (1920x1080)</label>
                                                <input type="file" class="form-control" id="resourceDarkBackground" style="width: 98%; margin-bottom: 10px" name="resourceDarkBackGroundSub"
                                                       onchange="previewImage(this, 'preResourceDarkBackgroundSub')" required>
                                                <div class="preview-container">
                                                    <div class="preview-item" style="background-color: darkgrey">
                                                        <div id="preResourceDarkBackgroundSub"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                    <div class="col-sm-6 offset-sm-3 text-center">
                        <input type="button" onclick="submitForm()"  class="btn btn-primary ms-2"
                               style="margin-right: 10px;width: 10%"
                               value="{{ !($themeInfo)? '등록':'수정' }}">
                        <input type="button" class="btn btn-primary ms-2" style="width: 10%"
                               onclick="back()" value="취소">
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
        // 메인 배경 이미지
        function addResource(resourceCount) {
            let addButtonHTML = '';

            if(resourceCount > 5){
                alert('최대 5개 등록 가능합니다.');
                return false;
            } else if(resourceCount < 5){
                addButtonHTML = `<input type="button" class="btn btn-primary ms-3" id="button${resourceCount}" onclick="addResource(${resourceCount + 1})" value="+">`;
            }

            const newResourceHTML = `
             <div class="col-md-4 mb-4">
                <label for="resource${resourceCount}">Sorting Num(${resourceCount})</label>
                ${addButtonHTML}
                <input type="file" class="form-control" id="resource${resourceCount}" style= "margin-bottom: 10px" name="resourceBackgroundImage${resourceCount}"
                       onchange="previewImage(this, 'preResource${resourceCount}')" required>
                <div class="preview-container">
                    <div class="preview-item">
                        <div id="preResource${resourceCount}"></div>
                    </div>
                </div>
            </div>`;

            const resourceList = document.getElementById('resourceList');
            resourceList.insertAdjacentHTML('beforeend', newResourceHTML);

            if (resourceCount > 1) {
                const buttonNum = resourceCount-1;
                const previousItem = resourceList.querySelector('#button'+buttonNum);
                if (previousItem) {
                    previousItem.style.display = 'none';
                }
            }

            if (resourceCount % 3 === 0) {
                const clearfixDiv = document.createElement('div');
                clearfixDiv.className = 'clearfix d-md-none'; // Hide clearfix on medium devices and up
                resourceList.appendChild(clearfixDiv);
            }

        }

        // 초기화 셋팅
        window.onload = function() {
            addResource(1); // 메인 배경 생성

            @if(!empty($themeInfo))
                {{--createPreview(<?php echo $themeInfo ?>, 'preResource1');--}}
                $('#nameCheckButton').prop('disabled', true);
                $('#nameCheck').val(0);
            @endif
        };

        // 한글 6자 영어 10자
        function checkNameForm(value){
            var regex = /^([가-힣]{1,6}|[a-zA-Z\s]{1,10})$/;
            if (regex.test(value)) {
                return 1;
            } else {
                return 0;
            }
        }

        // 등록
        function submitForm() {

            if ($('#nameCheck').val() == 1) {
                alert('테마 이름 중복 확인을 해주세요.');
                return false;
            }

{{--            @if(empty($themeInfo))--}}
{{--                if (!$('#resource1').val()) {--}}
{{--                    alert('배너를 입력해주세요.');--}}
{{--                    return false;--}}
{{--                }--}}
{{--            @endif--}}

            $('#form1').submit();
        }

        //뒤로가기
        function back() {
            window.history.back();
        }

        // 이름,영문이름 변경이 있을때 중복확인 초기화
        function changeCheck(target) {
            if (target === 'nameCheck') {
                if($('#nameCheck').val() == 0){
                    alert('테마 이름 변경 되어 다시 중복확인을 눌러주세요.');
                    $('#nameCheckButton').prop('disabled', false);
                }
            }

            $('#' + target).val(1);
        }

        // 테마이름 중복 체크
        function duplicateCheck(type) {

            if(type === 'name') {
                var text = $('#themeName').val();
                var checkPosition = 'nameCheck';
                var languageText ='테마 이름';
                var button  =  "nameCheckButton";
            }

            if(!text)
            {
                alert('입력 후 중복 확인 버튼을 눌러주세요');
                return false;
            } else if(!checkNameForm(text)){
                alert('테마 이름 형식이 맞지 않습니다.');
                return false;
            }

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/admin/platform/theme/exists-theme-name',
                type: 'POST',
                data: {themeName: text},
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

        // 테마 미리보기
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var fileType = file.type.split('/')[1]; // 파일의 확장자 가져오기
                var allowedExtensions = ["json", "jpg","jpeg", "png","mpeg","wav","mp3"];

                if (!allowedExtensions.includes(fileType)) {
                    alert("허용되지 않은 파일 확장자입니다.");
                    $('#'+previewId).val('');
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
                }  else if (file.type.includes('audio')) { // 오디오 파일인 경우
                    var audio = document.createElement('audio');
                    audio.controls = true;
                    audio.src = URL.createObjectURL(file);
                    var previewItem = document.getElementById(previewId);
                    previewItem.innerHTML = ''; // 기존에 있는 미리보기 삭제
                    previewItem.appendChild(audio);
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


    </script>
@endsection
