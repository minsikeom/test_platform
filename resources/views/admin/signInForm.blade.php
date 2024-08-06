<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{  $translateConstants::SIGNIN_FORM[$lang]['title'] }}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            max-width: 400px;
            margin-top: -50px; /* 조정할 마진 값 */
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

        span.message {
            color: #FF0000;
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
            {{  $translateConstants::SIGNIN_FORM[$lang]['title'] }}
        </h2>

        <form id="signIn-form" method="POST" action="{{ route('isLogin') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="userId">{{ $translateConstants::SIGNIN_FORM[$lang]['loginId'] }}</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="loginId" name="loginId" value="{{ old('loginId') }}"  required>
                </div>
                <small id="loginIdMessage" class="form-text text-muted"></small>
            </div>

            <div class="form-group">
                <label for="password">{{ $translateConstants::SIGNIN_FORM[$lang]['password'] }}</label>
                <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" required>
            </div>
            {{-- 에러 메세지 --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $translateConstants::SIGNIN_FORM[$lang][$errors->first('error')] }}
                </div>
            @endif
            <button type="submit" class="btn btn-primary mt-3" >{{ $translateConstants::SIGNIN_FORM[$lang]['signIn'] }}</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
