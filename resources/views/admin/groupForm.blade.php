@extends('layouts.adminLayout')

@section('title')
    그룹 등록/수정 폼
@endsection

@section('style')
        <style>
           .border-bottom-line{
               border-bottom: 2px solid black;
           }

           .border {
               border: 2px solid black !important;
           }

           .button {
               margin: 0 auto;
               display: inline-block;
           }
           .button .term {
               margin-right: 30px;
           }
           .btn-css {
               padding: 13px 30px;
               font-size: 15px;
               border-radius: 10px;
               border: none;
           }

           .font-color {
              color: red !important;
              margin:auto;
           }

        </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="col-md-5" style="justify-content: center;">
                <form class="form-horizontal  border rounded p-4" id="submitForm" action="{{ route('insertGroup') }}" method="post" onsubmit="formSubmits();return false; ">
                    @csrf
                <input type="hidden" name="agency" value="{{ $agencyId }}">
                <input type="hidden" name="referer" value="{{ $referer }}">
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        <div class="tab-pane active" id="settings">
                            <div class="border-bottom-line mt-4 mb-4 pb-2">
                                <h2>그룹 추가</h2>
                            </div>
                            <div class="form-group row">
                                <label for="groupNames" class="col-sm-3 col-form-label">그룹 이름</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="groupName" maxlength ="10" pattern="[a-zA-Z0-9가-힣]*" id="groupNames" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputName2" class="col-sm-3 col-form-label">사용자 별명</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="nickName" maxlength ="10" pattern="[a-zA-Z0-9가-힣]*" id="inputName2">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputName4" class="col-sm-3 col-form-label">전화번호</label>
                                <div class="col-sm-8 d-flex align-items-center" style="width: 100%">
                                    <select class="form-control mr-1" style="width: 40%" id="countryCode" name="countryNumber">
                                        @foreach($countryInfo as $info)
                                            <option value="{{ $info->country_number }}">{{ strtoupper($info->country_code) }} +{{ $info->country_number }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control" name="phone_num" id="phone_num" pattern="[a-zA-Z0-9가-힣]*">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="names" class="col-sm-3 col-form-label">관리자 이름</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="name" maxlength ="6" pattern="[a-zA-Z0-9가-힣]*" id="names" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <input type="hidden" id="checkId" value="0">
                                <label for="loginIds" class="col-sm-3 col-form-label">{{ $translateConstants::SIGNUP_FORM[$lang]['loginId'] }}</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="loginId" maxlength ="10" id="loginIds" pattern="[a-zA-Z0-9가-힣]*"  >
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" class="btn btn-primary" onclick="checkLoginId()">{{ $translateConstants::SIGNUP_FORM[$lang]['checkId'] }}</button>
                                </div>
                                <small id="loginIdMessage" class="form-text text-muted font-color"></small>
                            </div>
                            <div class="form-group row">
                                <label for="passwords" class="col-sm-3 col-form-label">비밀번호</label>
                                <div class="col-sm-5 input-group">
                                    <input type="password" class="form-control" id="passwords" name="password">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="button">
                                    <button type="submit" class="block btn-success btn-xs term btn-css" onclick="formSubmit()" >확인</button>
                                    <button type="submit" class="block btn-danger btn-xs btn-css" onclick="goBack()">취소</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
@section('script')
    <script>

        // 취소시 뒤로가기
        function goBack(){
            window.history.back();
        }

        // 패스워드 토글버튼
        $(document).ready(function(){
            $('.toggle-password').click(function(){
                var passwordInput = $('#passwords');
                var type = passwordInput.attr('type');
                if(type === 'password'){
                    passwordInput.attr('type', 'text');
                } else {
                    passwordInput.attr('type', 'password');
                }
            });
        });

        // 알파밧 제외 입력 방지
        function checkSpecialCharacters(inputString) {
            var regex = /[^a-zA-Z]/g; // 알파벳을 제외한 모든 문자를 찾는 정규 표현식
            return regex.test(inputString); // 정규 표현식과 문자열 비교하여 여부 반환
        }


        // 아이디 입력시 아이디 중복 체크 안한것으로 변경
        function loginIdCheck()
        {
            $('#checkId').val(0);
        }

        // 아이디 중복 체크
        function checkLoginId() {
            var loginId = $('#loginIds').val();

            if(checkSpecialCharacters(loginId)){
                alert('아이디는 영문만 입력 가능합니다.');
                return false;
            }

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

        // 폼 벨리데이션
        function formSubmits(){
            if($('#groupNames').val() === ''){
                alert('{{ $translateConstants::GROUP_FORM[$lang]['checkGroupName'] }}');
                return false;
            }else if($('#loginIds').val() === '') {
                alert('{{ $translateConstants::GROUP_FORM[$lang]['checkLoginId'] }}');
                return false;
            } else if($('#checkId').val() == 0){  // 아이디 중복 체크 했는지
                alert('{{ $translateConstants::GROUP_FORM[$lang]['checkIdMessage'] }}');
                return false;
            } else if($('#passwords').val() === '') {
                alert('{{ $translateConstants::GROUP_FORM[$lang]['checkPassword'] }}');
                return false;
            }
            $('#submitForm').submit();
        }
    </script>
@endsection
