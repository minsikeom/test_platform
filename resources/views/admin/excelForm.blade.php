<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>엑셀 데이터 업로드</title>

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

        /* Full screen layout styles */
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .full-screen-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .form-container {
            width: 100vw;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .btn-block {
            width: 100%;
        }

    </style>
</head>
<body>
<div class="full-screen-container">
    <section class="content width">
        <div class="form-container" >
            <form class="form-horizontal  border rounded p-4" id="submitForm" action="{{ route('getExcelDataList') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="agency" value="{{ $agencyId }}">
                <input type="hidden" name="group" value="{{ $groupId }}">
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        <div class="tab-pane active" id="settings">
                            <div class="border-bottom-line mt-4 mb-4 pb-2">
                                <h2>사용자 일괄 추가</h2>
                            </div>
                            <div class="mt-4 mb-4 pb-2 text-center">
                                <h3>일괄 업로드 주의사항</h3>
                            </div>
                            <p class="" style="color:red;font-size: 18px;font-weight: bold">
                                1. 아래 샘플과 꼭 동일한 양식으로 데이터를 입력 하셔야 합니다. (양식 임의 변경시 에러 발생)<br>
                                2. 업로드 되는 회원의 비밀번호는 생일(5월16 -> 0516)입니다. 변경 요청 시 그룹 관리자에게 문의하세요.<br>
                                3. 생년월일 미 입력 시 임시 비밀번호는 1234 입니다. 이후 관리자에게 문의하여 비밀번호 변경 요청 부탁드립니다.<br>
                                4. 일괄등록은 최대 30명까지 업로드 가능합니다. 추가로 더 업로드가 필요하시면 한번 더 업로드 부탁드립니다.<br>
                            </p>
                            <div class="form-group row">
                                <label for="groupNames" class="col-sm-3 col-form-label">샘플 양식 다운로드</label>
                                <div class="col-sm-5">
                                    <a href="/uploads/sample.xlsx?v={{ time() }}"><input type="button" class="btn btn-block btn-primary" value="샘플 양식(Excel)"></a>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="excelFile" class="col-sm-3 col-form-label">파일 추가</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control" name="files" id="excelFile" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="button">
                                    <button type="submit" class="block btn-success btn-xs term btn-css">확인</button>
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
@if (session('error'))
    <script>
        alert('{{ session('error') }}');
    </script>
@endif
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

    // 취소시 뒤로가기
    function goBack(){
        window.close();
    }


</script>

