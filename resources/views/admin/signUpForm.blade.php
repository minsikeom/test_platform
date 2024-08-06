<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{  $translateConstants::SIGNUP_FORM[$lang]['title'] }}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
            max-width: 400px;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container label {
            font-weight: bold;
        }

        .form-container button {
            background-color: #3c8dbc;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            width: 100%;
        }

        .form-container button:hover {
            background-color: #218838;
        }

        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }

        .form-check-label {
            padding-left: 0;
        }

        .form-check-input {
            margin-top: 3px;
        }

        .email-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .email-container label {
            margin-right: 10px;
        }

        .email-container input {
            flex: 1;
        //margin-right: 3px;
        }

        .email-container button {
            width: auto;
        }

        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }

        .btn-lg {
            width: 50%;
            padding: 0.4rem 0.4rem;
            border-radius: 0.5rem;
        }

        /* 해상도에 따른 크기 변화
        /*@media only screen and (min-width: 576px) {*/
        /*    !* 화면 너비가 576px 이상인 경우에 적용될 스타일 *!*/
        /*    .container {*/
        /*        max-width: 600px; !* 예시로 너비를 600px로 설정 *!*/
        /*    }*/
        /*}*/

        /*@media only screen and (min-width: 768px) {*/
        /*    !* 화면 너비가 768px 이상인 경우에 적용될 스타일 *!*/
        /*    .container {*/
        /*        max-width: 800px; !* 예시로 너비를 800px로 설정 *!*/
        /*    }*/
        /*}*/

    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-4">
            {{  $translateConstants::SIGNUP_FORM[$lang]['title'] }}
        </h2>

        <div class="form-group">
            <label for="languageSelect">{{  $translateConstants::SIGNUP_FORM[$lang]['language'] }}</label>
            <select class="form-control" id="languageSelect" name="lang" onchange="changeLanguage(this.value)">
                <option value="en" {{ ($lang ==='en')?'selected':''  }} > English</option>
                <option value="ko" {{ ($lang ==='ko')?'selected':''  }} > Korean</option>
            </select>
        </div>

        <form id="signup-form" method="POST" action="{{ route('registerUser') }}" onsubmit="return formValidation();">
            {{ csrf_field() }}
            <input type="hidden" name="licenseType" value="{{ $licenseType }}">
            @if($licenseType == 3)
                @if(isset($agencyId))
                <!-- 단체에 속한 개인 회원 가입시에  -->
                <input type="hidden" name="agencyId" value="{{ ($agencyId) }}">
                <div class="form-group">
                    <label for="agency">{{ $translateConstants::SIGNUP_FORM[$lang]['agencyName'] }}</label>
                    <input type="text" class="form-control" id="agency" value="{{ ($agency_info->agency_name) }}" disabled>
                </div>

                <div class="form-group d-flex justify-content-between">
                    <input id="personalBtn" type="button" class="btn btn-primary btn-lg" onclick="changeButtonColor('personalBtn')" value="{{ $translateConstants::SIGNUP_FORM[$lang]['personalGroup'] }}">
                    &nbsp;
                    <input id="groupBtn" type="button" class="btn btn-primary btn-lg" onclick="changeButtonColor('groupBtn')" value="{{ $translateConstants::SIGNUP_FORM[$lang]['group'] }}">
                </div>

                <div id="hiddenSelectBox" style="display: none;" class="form-group">
                    <label for="groupSelect">{{ $translateConstants::SIGNUP_FORM[$lang]['group'] }}</label>
                    <select class="form-control" id="groupSelect" name="groupId" >
                        @foreach( $agency_info->getAgencyWithGroupInfo as $groups)
                                <option value='{{ $groups->group_id }}'>{{ $groups->group_name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            @endif
            @if($licenseType == 1)
                <div class="form-group">
                    <label for="agencyName-input">{{ $translateConstants::SIGNUP_FORM[$lang]['agencyName'] }}</label>
                    <input type="text" class="form-control" id="agencyName-input" name="agencyName" required>
                </div>
            @endif
                <input type="hidden" name="licenseId" value="{{ $licenseId }}">
            <!--여기까지 -->
            <div class="form-group">
                <label for="userId">{{ $translateConstants::SIGNUP_FORM[$lang]['loginId'] }}</label>
                <div class="input-group">
                    <input type="hidden" id="checkId" value="0">
                    <input type="text" class="form-control" id="loginId" name="loginId" onchange="loginIdCheck()"  required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" onclick="checkLoginId()">{{ $translateConstants::SIGNUP_FORM[$lang]['checkId'] }}</button>
                    </div>
                </div>
                <small id="loginIdMessage" style="color:green !important;" class="form-text text-muted"></small>
            </div>

            <div class="form-group">
                <label for="password">{{ $translateConstants::SIGNUP_FORM[$lang]['password'] }}</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="username">{{ $translateConstants::SIGNUP_FORM[$lang]['userName'] }}</label>
                <input type="text" class="form-control" id="userName" name="userName" required>
            </div>

            <div class="form-group">
                <label for="nickname">{{ $translateConstants::SIGNUP_FORM[$lang]['userNickName'] }}</label>
                <input type="text" class="form-control" id="nickName" name="nickName" required>
            </div>

            <div class="form-group">
                <label for="countryCode">{{ $translateConstants::SIGNUP_FORM[$lang]['phoneNum'] }}</label>
                <div class="input-group">
                    <select class="form-control" id="countryCode" name="countryNumber">
                        @foreach($countryInfo as $info)
                            <option value="{{ $info->country_number }}">{{ strtoupper($info->country_code) }} +{{ $info->country_number }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="form-control" id="phoneNums" name="phoneNum" style="width: 47%">
                </div>
            </div>

            @if(in_array($licenseType,[1, 2])) {{-- 기관,b2c 가입일때만 --}}
            <div class="form-group">
                <label for="email">{{ $translateConstants::SIGNUP_FORM[$lang]['email'] }}</label>
                <div class="form-group email-container">
                    <input type="text" class="form-control" id="email" name="email" required>
                    <button type="button" class="btn btn-primary" id="emailSendButton" onclick="emailSend()">{{ $translateConstants::SIGNUP_FORM[$lang]['sendVerificationEmail'] }}</button>
                </div>
            </div>

            <!-- 이메일 인증 -->
            <div class="form-group verification" style="display: none;">
                <input type="hidden" id="checkVerification" value="0">
                <div class="form-group email-container">
                    <input type="text" class="form-control" name='emailVerificationCode' id="emailVerificationCode">
                    <button type="button" class="btn btn-primary" id="emailCheckButton" onclick="emailCheck()">{{ $translateConstants::SIGNUP_FORM[$lang]['checkVerification'] }}</button>
                </div>
            </div>
            @endif

            <div class="form-group">
                <label for="birthday">{{ $translateConstants::SIGNUP_FORM[$lang]['birthday'] }}</label>
                <input type="date" class="form-control" id="birthday" name="birthday" >
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                <label class="form-check-label" for="agreeTerms" data-toggle="modal" data-target="#termsModal">{{ $translateConstants::SIGNUP_FORM[$lang]['agreeTerms'] }}</label>
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="agreePrivacy" required>
                <label class="form-check-label" for="agreePrivacy" data-toggle="modal" data-target="#privacyModal">{{ $translateConstants::SIGNUP_FORM[$lang]['agreePrivacy'] }}</label>
            </div>

            <button type="submit" class="btn btn-primary mt-3" >{{ $translateConstants::SIGNUP_FORM[$lang]['signUp'] }}</button>
        </form>
    </div>
</div>

<!-- 이용 약관 모달 -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">{{ $translateConstants::SIGNUP_FORM[$lang]['termsTitle'] }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ $translateConstants::SIGNUP_FORM[$lang]['terms'] }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('agreeTerms').checked = false" data-dismiss="modal">{{ $translateConstants::SIGNUP_FORM[$lang]['disagree'] }}</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('agreeTerms').checked = true" data-dismiss="modal">{{ $translateConstants::SIGNUP_FORM[$lang]['agree'] }}</button>
            </div>
        </div>
    </div>
</div>

<!-- 개인정보 처리방침 모달 -->
<div class="modal fade" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">{{ $translateConstants::SIGNUP_FORM[$lang]['privacyTitle'] }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ $translateConstants::SIGNUP_FORM[$lang]['privacy'] }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('agreePrivacy').checked = false" data-dismiss="modal">{{ $translateConstants::SIGNUP_FORM[$lang]['disagree'] }}</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('agreePrivacy').checked = true" data-dismiss="modal">{{ $translateConstants::SIGNUP_FORM[$lang]['agree'] }}</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // 기관소속 회원가입 링크 전달시 그룹도 선택 되게 하기
        @if(!empty($groupId))
            changeButtonColor('groupBtn')
            $('#personalBtn').hide();
            $('#groupBtn').css('margin','auto');
            $('#groupSelect').on('mousedown', function(event) {
                event.preventDefault(); // 선택을 막음
            });
        @endif

            // 기관 , b2c 가입만 이메일 인증
        @if($licenseType == 3)
            $('#checkVerification').val(1);
        @endif

    });


    // 언어 선택 추가
    function changeLanguage(language){
        const url = new URL(window.location.href);
        url.searchParams.set('lang', language); // 기존 'lang' 파라미터를 업데이트하거나 새로 추가합니다.
        window.location.href = url.toString(); // 업데이트된 URL로 이동합니다.
    }


    function changeButtonColor(buttonId) {
        // 클릭된 버튼에 스타일 추가
        if(buttonId === 'groupBtn'){
            $('#personalBtn').css('background-color', '#007bff');
            $('#personalBtn').css('color', '#fff');
            $('#groupBtn').css('background-color', '#28a745');
            $('#groupBtn').css('color', '#fff');
            $('#hiddenSelectBox').show();
        } else {
            $('#personalBtn').css('background-color', '#28a745');
            $('#personalBtn').css('color', '#fff');
            $('#groupBtn').css('background-color', '#007bff');
            $('#groupBtn').css('color', '#fff');
            $('#hiddenSelectBox').hide();
        }
    }

    // 아이디 중복 체크
    function checkLoginId() {
        var loginId = $('#loginId').val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url : '/admin/signup/exists-login-id',
            type : 'POST',
            data : { loginId : loginId },
            success : function (data){
                if(data == 1){ // 중복 ID
                    $('#loginIdMessage').text('{{ $translateConstants::SIGNUP_FORM[$lang]['duplicate'] }}');
                    $('#checkId').val(0);
                } else if(data == 0) { // 사용 가능
                    $('#loginIdMessage').text('{{ $translateConstants::SIGNUP_FORM[$lang]['available'] }}');
                    $('#checkId').val(1);
                }  else {
                    alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                }
            },
            error : function(request,status,error){
                alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
        })
    }

    // 회원 메일 인증 메일 보내기
    function emailSend()
    {
        var email = $('#email').val();
        var lang = '{{ $lang }}';
        var name = $('#userName').val();

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url : '/admin/signup/send-email',
            type : 'POST',
            data : {lang : lang, name: name, to:email },
            success : function (data){
                if(data == 1){ // 메일 인증 코드 메일 전송
                    $('.verification').show();
                    $('#email').prop('readonly', true);
                    $('#emailSendButton').prop('disabled', true);
                    alert('{{ $translateConstants::SIGNUP_FORM[$lang]['emailSend'] }}');
                } else {
                    alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                }
            },
            error : function(request,status,error){
                alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
        })

    }

    // 이메일 코드 인증
    function emailCheck()
    {
        var emailVerificationCode = $('#emailVerificationCode').val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url : '/admin/signup/exists-email-code',
            type : 'POST',
            data : {emailVerificationCode: emailVerificationCode },
            success : function (data){
                if(data == 1){ // 메일 인증 코드 메일 전송
                    $('#checkVerification').val(1);
                    $('#emailVerificationCode').prop('readonly', true);
                    $('#emailCheckButton').prop('disabled', true);
                    alert('{{ $translateConstants::SIGNUP_FORM[$lang]['verificationTrue'] }}');
                } else if(data == 0 ) {
                    alert('{{ $translateConstants::SIGNUP_FORM[$lang]['verificationFalse'] }}');
                } else{
                    alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                }
            },
            error : function(request,status,error){
                alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
        })
    }

    // 아이디 입력시 아이디 중복 체크 안한것으로 변경
    function loginIdCheck()
    {
        $('#checkId').val(0);
    }

    // 폼 전송
    function formValidation()
    {
        // 아이디 중복 체크 했는지
        if($('#checkId').val() == 0){
            alert('{{ $translateConstants::SIGNUP_FORM[$lang]['duplicate'] }}');
            return false;
        }

        // 이메일 인증을 했는지
        if($('#checkVerification').val() == 0 ){
            alert('{{ $translateConstants::SIGNUP_FORM[$lang]['noVerification'] }}');
            return false;
        }

        return true;
    }

</script>
</body>
</html>
