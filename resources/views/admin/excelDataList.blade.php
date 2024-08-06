<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>엑셀 데이터 유저 확인</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="/admin/plugins/fontawesome-free/css/all.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/admin/css/adminlte.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            /* 공통 스타일 */
            body, html {
                height: 100%;
                margin: 0;
                padding: 0;
            }

            /* Full screen layout styles */
            .full-screen-container {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100%;
            }

            .form-container {
                max-width: 100%;
                width: 100%;
                margin: auto;
                padding: 10px;
            }

            .table-responsive {
                width: 100%;
                overflow-x: auto;
            }

            /* 폼 요소 스타일 */
            .form-horizontal {
                background-color: #fff;
                border-radius: 10px;
                padding: 20px;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .input-hide {
                border: none;
                outline: none;
                width: 100%;
            }

            .btn-css {
                padding: 13px 30px;
                font-size: 15px;
                border-radius: 10px;
                border: none;
            }

            .button {
                margin: 0 auto; /* 수평 가운데 정렬을 위한 마진 설정 */
                text-align: center; /* 내부 요소를 가로로 중앙 정렬 */
            }

            .button .term {
                margin-right: 30px; /* 오른쪽 마진으로 간격을 줌 */
            }

            /* 작은 화면에 대한 스타일 */
            @media screen and (max-width: 768px) {
                .full-screen-container {
                    padding: 10px;
                }

                .form-container {
                    width: 100%;
                }

                .input-hide {
                    width: 100%;
                }

                .btn-css {
                    width: 100%;
                }
            }

            /* 중간 화면에 대한 스타일 */
            @media screen and (min-width: 769px) and (max-width: 1024px) {
                .form-container {
                    max-width: 90%;
                }
            }

            /* 큰 화면에 대한 스타일 */
            @media screen and (min-width: 1025px) {
                .form-container {
                    max-width: 80%;
                }
            }
        </style>
</head>
<body>
<div class="full-screen-container">
        <section class="content">
            <div class="form-container" >
                <form class="form-horizontal  border rounded p-4" id="submitForm" action="{{ route('insertExcelData') }}" method="post">
                    @csrf
                <h2>
                    Excel 아이디 중복 Check
                </h2>
                    <p style="color:red;font-size: 18px;font-weight: bold">
                        1.ID 중복체크 단계입니다. 중복되어 재확인이 필요한 ID는 재입력 후 중복되지 않는 ID로 확인 부탁드립니다.<br>
                        2.단 한명의 사용자도 중복된 ID가 있어 재확인이 필요하다면 해당 List는 등록 할 수 없습니다.
                    </p>
                <input type="hidden" name="agency" value="{{ $agencyId }}">
                <input type="hidden" name="group" value="{{ $groupId }}">
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        <div class="tab-pane active" id="settings">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr style="text-align: center;">
                                    <th>이름</th>
                                    <th>별명</th>
                                    <th>전화번호</th>
                                    <th>생년월일</th>
                                    <th>아이디</th>
                                    <th>확인</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($uploadUserList  as $list)
                                    <tr style="text-align: center;">
                                        <input type="hidden" name="password[]" value="{{ ($list['birthday'])? $list['birthday']->format('md') : '123' }}">
                                        <td style="width:15%"><input type="hidden" name="name[]" value="{{ $list['name'] }}">{{ $list['name'] }}</td>
                                        <td style="width:12%"><input type="hidden" name="nickName[]" value="{{ $list['nick_name'] }}">{{ $list['nick_name'] }}</td>
                                        <td style="width:12%"><input type="hidden" name="phoneNum[]" value="{{ $list['phone_num'] }}">{{ $list['phone_num'] }}</td>
                                        <td style="width:15%"><input type="hidden" name="birthday[]" value="{{ ($list['birthday'])? $list['birthday']->format('Y-m-d') : '' }}">{{ ($list['birthday'])? $list['birthday']->format('Y-m-d') : '' }}</td>
                                        @if($list['loginIdDuplicate'] === 'Y')
                                            <td style="width:16%"><input type="text" id="text_{{ $list['login_id'] }}" name="loginId[]" maxlength="10" value="{{ $list['login_id'] }}"></td>
                                        @else
                                            <td style="width:16%"><input type="hidden"  name="loginId[]" value="{{ $list['login_id'] }}">{{ $list['login_id'] }}</td>
                                        @endif
                                        <td style="display: flex;gap: 10px;">
                                            @if($list['loginIdDuplicate'] === 'Y')
                                                <input type="hidden" name="checkId[]" id="check_{{ $list['login_id'] }}" value="1"> {{-- 0: 사용가능 , 1: 중복 --}}
                                                <input type="button" class="btn btn-block btn-success btn-xs" id="button_{{ $list['login_id'] }}" onclick="checkLoginId('{{ $list['login_id'] }}')" value="재확인">
                                            @else
                                                <input type="button" class="btn btn-block btn-default btn-xs" value="확인완료" readonly>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="form-group row">
                                <div class="button">
                                    <button type="button" class="block btn-success btn-xs term btn-css" onclick="submitForm()">등록</button>
                                    <button type="button" class="block btn-danger btn-xs btn-css" onclick="goBack()">취소</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
</body>
</html>

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="/admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="/admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="/admin/js/adminlte.js"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="/admin/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="/admin/plugins/raphael/raphael.min.js"></script>
<script src="/admin/plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="/admin/plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="/admin/plugins/chart.js/Chart.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="/admin/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="/admin/js/pages/dashboard2.js"></script>

    <script>

        // 취소시 창 종료
        function goBack(){
            window.close();
        }

        // 알파밧 제외 입력 방지
        function checkSpecialCharacters(inputString) {
            var regex = /[^a-zA-Z]/g; // 알파벳을 제외한 모든 문자를 찾는 정규 표현식
            return regex.test(inputString); // 정규 표현식과 문자열 비교하여 여부 반환
        }

        // 아이디 중복 체크
        function checkLoginId(loginId) {
            var textId = 'text_'+loginId;
            var checkId = 'check_'+loginId;
            var buttonId = 'button_'+loginId;
            var checkLoginId =  $('#'+textId).val();
            if(checkSpecialCharacters(checkLoginId)){
                alert('아이디는 영문만 입력 가능합니다.');
                return false;
            }

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url : '/admin/signup/exists-login-id',
                type : 'POST',
                data : { loginId : checkLoginId },
                success : function (data){
                    console.log(data);
                    if(data == 1){ // 중복 ID
                        alert('{{ $translateConstants::SIGNUP_FORM[$lang]['duplicate'] }}');
                        $('#'+checkId).val(1);
                    } else if(data == 0) { // 사용 가능
                        alert('{{ $translateConstants::SIGNUP_FORM[$lang]['available'] }}');
                        $('#'+checkId).val(0);
                        $('#'+textId).prop('readonly', true);
                        // 기존 클래스 제거 및 확인 완료로 변경
                        $('#'+buttonId).removeClass();
                        $('#'+buttonId).addClass('btn btn-block btn-default btn-xs');
                        $('#'+buttonId).prop('readonly', true);
                        $('#'+buttonId).val('확인완료');
                    }  else {
                        alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                    }
                },
                error : function(request,status,error){
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                },
            })
        }

        function submitForm(){
            var values = $('input[name="checkId[]"]').map(function() {
               return $(this).val(); // 각 요소의 값만 반환
            }).get();

            if(values.indexOf(1) == 1){
                alert('{{ $translateConstants::SIGNUP_FORM[$lang]['checkIdMessage'] }}');
            } else {
                $('#submitForm').submit();
            }
        }

    </script>

