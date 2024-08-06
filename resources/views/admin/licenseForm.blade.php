<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{  $translateConstants::LICENSE_FORM[$lang]['title'] }}</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
           // background: url({{ env('NCLOUD_OBJECT_STORAGE_URL').'/uploads/agrs/9733a2f26ec7437e403946a2b9e04aa6.png' }}) no-repeat center center fixed;
           // background-size: cover;
            background-color: #f5f5f5; /* 전체 배경 색상 */
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .overlay {
            // background: #F3E5AB;
            background: #fff; /* 폼 배경 색상 */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 350px;
            width: 80%;
            position: relative; /* 이미지를 포함한 부모 요소에 대한 상대 위치 설정 */
        }


        img {
            max-width: 100%; /* 이미지가 부모 요소에 맞게 최대 너비를 가지도록 설정 */
            height: auto; /* 이미지의 세로 비율을 자동으로 유지 */
            margin-bottom: 20px; /* 이미지와 폼 간의 간격 조절 */
        }

        .header {
            text-align: center;
            padding: 20px;
            background-color: black;
            color: white;
            border-radius: 10px 10px 0 0;
            margin-bottom: 20px;
        }

        .serial-code-form {
            text-align: center;
        }

        fieldset {
            border: none;
            padding: 0;
            margin: 0;
        }

        .serial-code-input {
            width:100%;
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
        }

        .serial-code-input input {
            width: calc(33% - 5px);
            margin-bottom: 10px;
            padding: 11px;
            border: 2px solid black;
            border-radius: 5px;
            font-size: 16px;
        }

        .serial-code-input .hyphen {
            width: 4%;
            text-align: center;
            padding-top: 10px; /* 조정 가능 */
            font-size: 16px;
            margin-bottom: 15px; /* 시리얼 코드 입력란과의 간격 조정 */
        }

        @media (max-width: 600px) {
            .serial-code-input input {
                width: calc(50% - 5px);
            }
        }

        @media (max-width: 400px) {
            .serial-code-input input {
                width: 100%;
            }
        }

        .serial-code-input input:not(:last-child) {
            margin-right: 5px;
        }

        input[type="button"] {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="button"]:hover {
            background-color: rgb(255 180 8);
        }
    </style>
</head>
<body>
<div class="overlay">
    <img src="/uploads/serial_logo.png">
    <div class="header">
        <h2>
            {!! nl2br($translateConstants::LICENSE_FORM[$lang]['welcome']) !!}
        </h2>
    </div>

    <div class="serial-code-form">
        <form>
            <fieldset>
                <div class="serial-code-input">
                    <input type="text" id="serialCode1" name="serialCode1" maxlength="4" autocomplete="" required  >
                    &nbsp; <div class="hyphen">-</div>&nbsp;
                    <input type="text" id="serialCode2" name="serialCode2" maxlength="4" autocomplete="" required>
                    &nbsp; <div class="hyphen">-</div>&nbsp;
                    <input type="text" id="serialCode3" name="serialCode3" maxlength="4" autocomplete="" required>
                    &nbsp; <div class="hyphen">-</div>&nbsp;
                    <input type="text" id="serialCode4" name="serialCode4" maxlength="4" autocomplete="" required>
                </div>
            </fieldset>
            <br>
            <input type="button" onclick="check_serial()" value=" {{  $translateConstants::LICENSE_FORM[$lang]['submit'] }}">
        </form>
    </div>
</div>
</body>
</html>

<script>
    // 설정 언어와 다를경우 언어 수정해주기
    window.onload = function() {
        var userLanguage = navigator.language || navigator.userLanguage;
        // alert(userLanguage+'//'+'{{ $lang }}');
        if (userLanguage !== '{{ $lang }}') {
            window.location.href = "?lang=" + userLanguage.split('-')[0];
        }
    }

    function check_serial()
    {
        var licenseCode = $('#serialCode1').val()+'-'+$('#serialCode2').val()+'-'+$('#serialCode3').val()+'-'+$('#serialCode4').val()
        console.log(licenseCode)
        if($('#serialCode1').val() ==='' || $('#serialCode2').val() ==='' || $('#serialCode3').val() ==='' || $('#serialCode4').val() ==='' ){
            alert('라이센스를 입력해주세요');
            return false;
        }
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type:'post',
            url:'/admin/license/exists-license-code',
            data:{licenseCode:licenseCode},
            dataType:'json',
            success: function(result){
                if(result['code'] == 0){ // 사용 가능
                    alert('{{ $translateConstants::LICENSE_FORM[$lang]['confirm'] }}');
                    location.href='/admin/signup/form?licenseId='+result['licenseId']+'&licenseType='+result['licenseType'];
                } else if(result['code'] == 1){ // 이미 사용
                    alert('{{ $translateConstants::LICENSE_FORM[$lang]['already'] }}');
                } else if(result['code'] == -1){ // 중지
                    alert('{{ $translateConstants::LICENSE_FORM[$lang]['pause'] }}');
                } else if(result['code'] == -3 ){ // 없는
                    alert('{{ $translateConstants::LICENSE_FORM[$lang]['invalid'] }}');
                } else {
                    alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}');
                }
            },
            error:function(e){
                alert('{{ $translateConstants::ERROR_MESSAGE[$lang]['error'] }}'+e);
            }
        })
    }

</script>
