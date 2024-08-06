@extends('layouts.adminLayout')

@section('title')
    상품 등록/수정 폼
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

        /* 셀렉트 박스 스타일링 */
        #sensorTypeSelect {
            width: 20%; /* 너비를 50%로 설정 */
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }


        input[type="checkbox"] {
            /* 크기 조정 */
            transform: scale(1.5); /* 크기를 1.5배로 확대 */
            margin-right: 10px; /* 여백 추가 */
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

                            <form id="form1" action="{{ !($productInfo)?  route('insertProductInfo'): route('updateProductInfo')  }}" method="post">
                                @csrf
                                <input type="hidden" name="productId" value={{ ($productInfo->product_id)??'' }}>

                                <div class="form-group row">
                                    <div class="col-sm-12 text-center label-color">
                                        <h3>{{ !($productInfo)? '상품 등록':'상품 수정'  }}</h3>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="" style="margin: auto;">상품 이름</label>
                                    </div>
                                    <div class="px-2"></div>
                                        <div class="col-sm-2" style="margin-top: 20px; margin-bottom: 20px;" >
                                            <input type="hidden" id="nameCheck" value="1">
                                            <input type="text" class="form-control" id="productName"
                                                   name="productName"
                                                   style="text-align: center;"
                                                   maxlength="10"
                                                   onchange="changeCheck('nameCheck')"
                                                   value="{{ ($productInfo->product_name)??''  }}"
                                                   required>
                                            <span style="color: red">*한글 6글자 제한,영문 10글자 제한(한글,영문 입력가능)</span><br>
                                            <span id="name" style="font-size: 14px"></span>
                                        </div>
                                        <div class="col-sm-1" style="margin-top: 20px; margin-bottom: 20px;">
                                            <input type="button" class="btn btn-primary ms-1"  id="nameCheckButton"
                                                   onclick="duplicateCheck('name')" value="중복 확인">
                                        </div>
                                </div>

                                <div class="form-group row" >
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex">
                                        <label for="sensorTypeSelect" style="margin: auto;">상품 구성(센서)</label>
                                    </div>
                                    <div class="px-2"></div>
                                    <div class="col-sm-8" style="margin-top: 20px; margin-bottom: 20px;">
                                        <input type="hidden" id="selectedSensorIdsInput" name="selectedSensorIds">
                                        <div class="d-flex align-items-center" >
                                            <label for="sensorTypeSelect"  id="sensorTypeLabel" class="col-sm-1 col-form-label" style="margin-right: 25px">센서 타입:</label>
                                            <select id="sensorTypeSelect" onchange="populateSensorList()" class="form-control">
                                                @if(!isset($productInfo->getSensorGroupWithSensorInfo))
                                                <option value="">Select</option>
                                                @endif
                                                <?php foreach ($sensorTypeList as $item): ?>
                                                <option value="{{ $item->sensor_type_id }}" {{ (isset($productInfo->getSensorGroupWithSensorInfo) && $productInfo->getSensorGroupWithSensorInfo[0]->sensor_type_id === $item->sensor_type_id  )? 'selected':'' }}>
                                                    {{ $item->sensor_type_name }}
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group row" id="listArea" style="margin-top: 20px">
                                            <div class="col-sm-9 offset-sm-3 p-0">
                                                <div id="sensorListArea" class="border-0" style="margin-left: -31%;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <label for="selectedItemsInput" class="col-sm-1 col-form-label" style="margin-right: 20px; white-space: nowrap;">선택한 내역:</label>
                                            <div class="col-sm-11" style="margin-right: 10px;">
                                                <input type="text" id="selectedItemsInput" name="selectedItemsInput" class="form-control mt-3" style="width: 100%;font-weight: bold" placeholder="선택한 내역을 여기에 저장합니다." readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex">
                                        <label for="productDescription" style="margin: auto;">상품 설명</label>
                                    </div>
                                    <div class="px-2"></div>
                                    <div class="col-sm-8" style="margin-top: 10px; margin-bottom: 20px;">
                                        <textarea id="productDescription" name="productDescription" class="form-control mt-3" style="width: 100%" placeholder="설명을 입력하세요." rows="5">@if(isset($productInfo->product_desc)) {!! nl2br(e($productInfo->product_desc)) !!} @endif</textarea>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="col-sm-6 offset-sm-3 text-center">
                        <input type="button" onclick="submitForm()"  class="btn btn-primary ms-2"
                               style="margin-right: 10px;width: 10%"
                               value="{{ !($productInfo)? '등록':'수정' }}">
                        <input type="button" class="btn btn-primary ms-2" style="width: 10%"
                               onclick="back()" value="취소">
                    </div>
                </div>
            </div>
        </div>
{{--        <?php echo json_encode($sensorTypeList) ?>--}}
    </section>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
@endsection

@section('script')
    <script>

        var checkArray = []

        @if(isset($productInfo->getSensorGroupWithSensorInfo))
            var productSensorData = <?php echo json_encode($productInfo->getSensorGroupWithSensorInfo) ?>;
            productSensorData.some(function(productSensor) {
                var productSensorSelectedTypeName =<?php echo json_encode($sensorTypeList) ?>.filter(function (item) {
                    return item.sensor_type_id == productSensor.sensor_type_id;
                })[0].sensor_type_name;
                checkArray.push(productSensor.sensor_id);
                checkSensorBox(productSensor,productSensorSelectedTypeName)
            });
        @endif

        // 센서 리스트
        function populateSensorList() {
            var selectedSensorTypeId = document.getElementById("sensorTypeSelect").value;

            if (selectedSensorTypeId === "") {
                $('#listArea').hide();
                return;
            }

            $('#listArea').show();

            var selectedSensorList = <?php echo json_encode($sensorTypeList) ?>.filter(function (item) {
                return item.sensor_type_id == selectedSensorTypeId;
            })[0].get_sensor_list;

            var selectedSensorTypeName =<?php echo json_encode($sensorTypeList) ?>.filter(function (item) {
                return item.sensor_type_id == selectedSensorTypeId;
            })[0].sensor_type_name;

            // 체크 박스 영역 초기화
            var sensorListArea = document.getElementById("sensorListArea");
            sensorListArea.innerHTML = "";

            // 가져온 센서 리스트를 기반으로 체크 박스 생성
            var rowDiv; // 한 줄에 5개씩 체크 박스를 담을 div 요소
            selectedSensorList.forEach(function(sensor, index) {
                // 한 줄에 5개씩 체크 박스를 담을 div 요소 생성
                if (index % 5 === 0) {
                    rowDiv = document.createElement("div");
                    rowDiv.classList.add("row");
                    sensorListArea.appendChild(rowDiv);
                }

                // 각 체크 박스와 라벨을 감싸는 div 요소 생성
                var checkboxWrapper = document.createElement("div");
                checkboxWrapper.classList.add("col-sm-2");
                checkboxWrapper.style.marginTop = "10px";
                checkboxWrapper.style.marginBottom = "10px";

                // 체크 박스 생성
                var checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "sensorId[]";
                checkbox.value = sensor.sensor_id;
                checkbox.id = "sensorId_" + sensor.sensor_id;

                // 체크박스가 체크되어야 하는지 확인하고 체크
                if (checkArray.includes(sensor.sensor_id)) {
                    checkbox.checked = true;
                }

                checkbox.onclick = function() {
                    if (checkbox.checked) {
                        checkArray.push(sensor.sensor_id);
                        checkSensorBox(sensor, selectedSensorTypeName); // 체크되었을 때 함수 호출하여 추가
                    } else {
                        var index = checkArray.indexOf(sensor.sensor_id);
                        if (index !== -1) {
                            checkArray.splice(index, 1);
                            removeSensorFromInput(sensor, selectedSensorTypeName); // 체크 해제 시 함수 호출하여 제거
                        }
                    }
                };

                // 라벨 생성
                var label = document.createElement("label");
                label.htmlFor = "sensorId_" + sensor.sensor_id;
                label.style.marginLeft = "10px";
                label.appendChild(document.createTextNode(sensor.sensor_name));

                // 생성한 요소들을 checkboxWrapper에 추가
                checkboxWrapper.appendChild(checkbox);
                checkboxWrapper.appendChild(label);

                // checkboxWrapper를 rowDiv에 추가
                rowDiv.appendChild(checkboxWrapper);
            });
        }

        // 체크 박스 선택 내역
        function checkSensorBox(sensor, selectedSensorTypeName) {
            var currentValue = $('#selectedItemsInput').val();
            var newValue = sensor.sensor_name + '(' + selectedSensorTypeName + ')';

            if (currentValue.length > 0) {
                // 현재 입력값이 있는 경우 쉼표와 함께 추가
                newValue = currentValue + ', ' + newValue;
            }

            $('#selectedItemsInput').val(newValue);

            // id 배열
            var currentIdArray =  $('#selectedSensorIdsInput').val();
            var newId = sensor.sensor_id;

            if(currentIdArray.length > 0){
                newId = currentIdArray + ', ' + newId;
            }
            $('#selectedSensorIdsInput').val(newId);
        }

        // 체크 박스 해제시 인풋 박스 제거
        function removeSensorFromInput(sensor, selectedSensorTypeName) {
            var currentValue = $('#selectedItemsInput').val();
            var removeValue = sensor.sensor_name + '(' + selectedSensorTypeName + ')';
            var newValue = currentValue.replace(removeValue, ''); // 해당 정보 제거

            // ','를 ', '로 대체하고 다시 trim
            newValue = newValue.replace(/,\s*,/g, ',').trim();
            // 맨 앞이 , 으로 끝나는 경우 제거
            if (newValue.startsWith(',')) {
                newValue = newValue.slice(1).trim();
            }
            // 맨 끝이 ','로 끝나는 경우 제거
            if (newValue.endsWith(',')) {
                newValue = newValue.slice(0, -1);
            }

            $('#selectedItemsInput').val(newValue);

            // id 배열
            var currentIdArray =  $('#selectedSensorIdsInput').val();
            var newId = currentIdArray.replace(sensor.sensor_id, '');
            // ','를 ', '로 대체하고 다시 trim
            newId = newId.replace(/,\s*,/g, ',').trim();
            // 맨 앞이 , 으로 끝나는 경우 제거
            if (newId.startsWith(',')) {
                newId = newId.slice(1).trim();
            }
            // 맨 끝이 ','로 끝나는 경우 제거
            if (newId.endsWith(',')) {
                newId = newId.slice(0, -1);
            }

            $('#selectedSensorIdsInput').val(newId);

        }

        // 초기화 셋팅
        window.onload = function() {
            $('#listArea').hide();
            @if(!empty($productInfo))
                populateSensorList()
                $('#nameCheckButton').prop('disabled', true);
                $('#nameCheck').val(0);
                // 센서타입별 센서 수정으로 다시 변경시 아래 부분 주석 해제
                $('#sensorTypeSelect').hide(); // 센서 타입 셀렉트 박스 숨기기
                $('#sensorTypeLabel').hide(); // 센서 타입 라벨 숨기기
                $('#listArea').hide(); // 센서 타입별 리스트 체크박스 가리기
            @endif
        };

        // 등록
        function submitForm() {

            if (!$('#productName').val()) {
                alert('상품 이름을 입력 해주세요.');
                return false;
            }

            if ($('#nameCheck').val() == 1) {
                alert('상품 이름 중복 확인을 해주세요.');
                return false;
            }

            if(!$('#selectedItemsInput').val()) {
                alert('상품 구성을 선택 해주세요.');
                return false;
            }

            if(!$('#productDescription').val()){
                alert('상품 설명을 입력 해주세요.');
                return false;
            }

            $('#form1').submit();
        }

        //뒤로가기
        function back() {
            window.history.back();
        }

        // 이름 변경이 있을때 중복확인 초기화
        function changeCheck(target) {
            if (target === 'nameCheck') {
                if($('#nameCheck').val() == 0){
                    alert('상품 이름 변경 되어 다시 중복확인을 눌러주세요.');
                    $('#nameCheckButton').prop('disabled', false);
                }
            }
            $('#' + target).val(1);
        }

        // 한글 6자 영어 10자
        function checkNameForm(value){
            var regex = /^([가-힣]{1,6}|[a-zA-Z\s]{1,10})$/;
            if (regex.test(value)) {
                return 1;
            } else {
                return 0;
            }
        }

        // 장르 이름 , 영문 이름 중복 체크
        function duplicateCheck(type) {
            if (type === 'name') {
                var text = $('#productName').val();
                var checkPosition = 'nameCheck';
                var languageText ='상품 이름';
                var button  =  "nameCheckButton";
            }

            if(!text) {
                alert('입력 후 중복 확인 버튼을 눌러주세요');
                return false;
            } else if(!checkNameForm(text)){
                alert('상품 이름 형식이 맞지 않습니다.');
                return false;
            }

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/admin/platform/product/exists-product-name',
                type: 'POST',
                data: {productName: text},
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
    </script>
@endsection
