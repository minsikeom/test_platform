@extends('layouts.adminLayout')

@section('title')
    콘텐츠 등록/수정 폼
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
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* 이미지 사이 간격 */
        }

        .preview-item {
            width: calc(50% - 10px); /* 2개씩 배치하고 간격을 뺌 */
            box-sizing: border-box; /* 테두리와 패딩 포함 */
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }

        .preview-item img {
            max-width: 80%;
            height: auto;
        }

        .preview-item video {
            max-width: 80%;
            height: auto;
        }

        .form-check-input {
            width: 25px; /* 너비를 원하는 크기로 조절 */
            height: 25px; /* 높이를 원하는 크기로 조절 */
            margin-right: 10px; /* 오른쪽 여백 조절 */
        }

        .nav-item{
            width: 12%;
            text-align: center;
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
                            <form id="form1"
                                  action="{{ isset($contentsInfo)? route('updateContentsInfos'):route('insertContentsInfos') }}"
                                  method="post" enctype="multipart/form-data" onsubmit="submitForm();return false;">
                                @csrf
                                <input type="hidden" name="contentsId" value={{ ($contentsId)??'' }}>

                                <div class="form-group row">
                                    <div class="col-sm-12 text-center label-color">
                                        <h3>{{ isset($contentsId)? '콘텐츠 정보수정':'콘텐츠 추가'  }}</h3>
                                    </div>
                                </div>

                                <div class="form-group row ">
                                    <label for="content_code" class="col-sm-3 col-form-label label-color">콘텐츠 코드</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" style="color:red;text-align: center"  id="content_code" name="contentCode" value="{{ ($contentsInfo->contents_code)??'' }}" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="genre" class="col-sm-3 col-form-label label-color" style="margin: auto;">장르 선택</label>
                                    </div>
                                    <div class="col-sm-9"  style="margin-top: 12px; margin-bottom: 12px;">
                                        <div class="form-check form-check-inline">
                                            @foreach($genreList as $key => $genre)
                                                @php
                                                    $selected='';
                                                    if(empty($contentsInfo->getContentsGenreGroup)){
                                                        if($key == 0){
                                                            $selected='checked';
                                                        }
                                                    }else{
                                                        if($contentsInfo->getContentsGenreGroup->contents_genre_id === $genre->contents_genre_id ){
                                                            $selected='checked';
                                                        }
                                                    }
                                                @endphp
                                                <div class="px-2"></div> <!-- 여백 추가 -->
                                                <input class="form-check-input" type="radio" name="genre" id="{{ $genre->getContentsGenreGroupTexts->text }}"
                                                       value={{ $genre->contents_genre_id }}  {{ $selected }} >
                                                <label class="form-check-label"
                                                       for="{{ $genre->getContentsGenreGroupTexts->text }}">{{ $genre->getContentsGenreGroupTexts->text }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="" class="col-sm-3 col-form-label label-color" style="margin: auto;">테마</label>
                                    </div>
                                    <div class="col-sm-9"  style="margin-top: 12px; margin-bottom: 12px;">
                                        <div class="form-check form-check-inline">
                                            @foreach($themeList as $key => $theme)
                                                @php
                                                    $checked='';
                                                    if(empty($contentsInfo->getThemeContents)){
                                                        if($key == 0){
                                                            $checked='checked';
                                                        }
                                                    }else{
                                                        foreach($contentsInfo->getThemeContents as $selectedTheme)
                                                        {
                                                            if($selectedTheme->theme_id === $theme->theme_id ){
                                                                $checked='checked';
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <div class="px-2"></div>
                                                <input class="form-check-input" type="checkbox"
                                                       id="{{ $theme->theme_id }}" name="theme[]"
                                                       {{ $checked }}  value={{ $theme->theme_id }} >
                                                <label class="form-check-label"
                                                       for="{{ $theme->theme_id }}">{{ $theme->theme_name }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="" class="col-sm-3 col-form-label label-color" style="margin: auto;">센서</label>
                                    </div>
                                    <div class="px-2"></div>
                                    <div class="col-sm-5"  style="margin-top: 12px; margin-bottom: 12px;"> <!-- 변경된 클래스 -->
                                        <input type="button" class="btn btn-primary ms-3" style="margin-bottom: 10px" onclick="addSensor()"
                                               value="add Available Sensor+">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="developmentalElement" class="col-sm-3 col-form-label label-color" style="margin: auto;">발달 요소</label>
                                    </div>
                                    <div class="col-sm-9"  style="margin-top: 12px; margin-bottom: 12px;">
                                        <div class="form-check form-check-inline">
                                            @foreach($developmentalElementList as $key => $element)
                                                @php
                                                    $checked='';
                                                    if(empty($contentsInfo->getDevelopmentalElementGroup)){
                                                        if($key == 0){
                                                            $checked='checked';
                                                        }
                                                    }else{
                                                        foreach($contentsInfo->getDevelopmentalElementGroup as $selectedElement)
                                                        {
                                                            if($selectedElement->type_id === $element->type_id ){
                                                                $checked='checked';
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <div class="px-2"></div>
                                                <input class="form-check-input" type="checkbox"
                                                       id="{{ $element->type_id }}"
                                                       name="developmentalElement[]"
                                                       value={{ $element->type_id }} {{ $checked }}>
                                                <label class="form-check-label"
                                                       for="{{ $element->type_id }}">{{ $element->text }}</label>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="content_name" class="col-sm-3 col-form-label text-sm-end label-color">콘텐츠 이름</label>
                                    <div class="px-2"></div>
                                    @if(!isset($contentsInfo->getContentsText[1]->text_title))
                                        <div class="col-sm-4">
                                            <input type="hidden" id="nameCheck" value="1">
                                            <input type="text" class="form-control" id="content_name"
                                                   style="text-align: center;"
                                                   pattern="[A-Za-z0-9]+" name="contentsName"
                                                   onchange="changeCheck('nameCheck')"
                                                   value="{{ ($contentsInfo->getContentsText[0]->text_title)??''  }}"
                                                   required>
                                            <span id="name" style="display: flex; justify-content: center;"></span>
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="button" class="btn btn-primary ms-2"
                                                   onclick="duplicateCheck('name')" value="중복 확인">
                                        </div>
                                    @else
                                        <div class="col-sm-4 align-items-center d-flex">
                                            <input type="hidden" id="nameCheck" value="0">
                                            <input type="hidden" name="contentsName" value="{{ $contentsInfo->getContentsText[0]->text_title }}">
                                            {{ $contentsInfo->getContentsText[0]->text_title }}
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group row">
                                    <label for="content_name_en" class="col-sm-3 col-form-label text-sm-end label-color">영문 이름</label>
                                    <div class="px-2"></div>
                                    @if(!isset($contentsInfo->getContentsText[1]->text_title))
                                        <div class="col-sm-4">
                                            <input type="hidden" id="enNameCheck" value="1">
                                            <input type="text" class="form-control" id="content_name_en"
                                                   pattern="[A-Za-z0-9]+" name="contentsEnName"
                                                   style="text-align: center;"
                                                   onchange="changeCheck('enNameCheck')"
                                                   value="{{ ($contentsInfo->getContentsText[1]->text_title)??''  }}"
                                                   required>
                                            <span id="enName" style="display: flex; justify-content: center;"></span>
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="button" class="btn btn-primary ms-2"
                                                   onclick="duplicateCheck('enName')" value="중복 확인">
                                        </div>
                                    @else
                                        <div class="col-sm-4 align-items-center d-flex">
                                            <input type="hidden" id="enNameCheck" value="0">
                                            <input type="hidden" name="contentsEnName" value="{{ $contentsInfo->getContentsText[1]->text_title }}">
                                            {{ $contentsInfo->getContentsText[1]->text_title }}
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group row">
                                    <label for="" class="col-sm-3 col-form-label label-color">실행 파일</label>
                                    <div class="px-2"></div>
                                    <div class="col-sm-4 align-items-center d-flex">
                                        @if(!isset($contentsInfo->f_exe_name))
                                            <input type="file" class="form-control" id="content_executable"
                                                   name="contentExeFile" onchange="checkFileWithEnName()" required>
                                        @else
                                            {{ $contentsInfo->f_exe_name }}
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="version" class="col-sm-3 col-form-label label-color" style="margin: auto;">버전(Version)</label>
                                    </div>
                                    <div class="px-2"  ></div>
                                    @if(!isset($contentsInfo->version))
                                        <div class="col-sm-8" style="margin-top: 10px; margin-bottom: 10px;">
                                            Ver <span id="version">1.0.0</span>
                                            <input type="hidden" class="form-control" id="versionValue" name="version"
                                                   value="1.0.0">
                                            <label for="selectBox1" class="col-sm-1 col-form-label" style="font-size: 18px;">MAJOR:</label>
                                            <select class="form-select me-2" id="selectBox1" onchange="updateVersion()">
                                                @for($i=1; $i<10; $i++)
                                                    <option value={{ $i }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            /
                                            <label for="selectBox2" class="col-sm-1 col-form-label" style="font-size: 18px;">MINOR:</label>
                                            <select class="form-select me-2" id="selectBox2" onchange="updateVersion()">
                                                @for($i=0; $i<10; $i++)
                                                    <option value={{ $i }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            /
                                            <label for="selectBox3" class="col-sm-1 col-form-label" style="font-size: 18px;">PATCH:</label>
                                            <select class="form-select me-2" id="selectBox3" onchange="updateVersion()">
                                                @for($i=0; $i<10; $i++)
                                                    <option value={{ $i }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    @else
                                        <div class="col-sm-3 align-items-center d-flex ">
                                            <span>Ver</span>&nbsp;<span id="version">{{ $contentsInfo->version }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label label-color align-items-center d-flex ">
                                        <label for="countryTab" style="margin: auto;">국가별 리소스 & 설명<br>(Resource&Description
                                            )</label>
                                    </div>
                                    <div class="px-2"></div>
                                    <div class="col-md-8"  style="margin-top: 12px; margin-bottom: 12px;">
                                        <ul class="nav nav-tabs" id="countryTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="country1-tab" data-toggle="tab"
                                                   href="#country1" role="tab" aria-controls="country1"
                                                   aria-selected="true">한글</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="country2-tab" data-toggle="tab" href="#country2"
                                                   role="tab" aria-controls="country2" aria-selected="false">영어</a>
                                            </li>
                                        </ul>

                                        {{--한글--}}
                                        <div class="tab-content" id="countryTabContent">
                                            <div class="tab-pane fade show active" id="country1" role="tabpanel"
                                                 aria-labelledby="country1-tab">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <div class="col-md-12 text-center">
                                                            <span class="red-underline font-weight-bold">파일 업로드(FileUpload)</span>
                                                        </div>
                                                        <label for="resource1">Thumbnail(0)</label>
                                                        <input type="file" class="form-control" id="resource1"
                                                               name="resource1"
                                                               onchange="previewImage(this, 'preResource1')" {{ !isset($contentsInfo)??'required' }}>
                                                        <label for="resource2">Sub(1)</label>
                                                        <input type="file" class="form-control" id="resource2"
                                                               name="resource2"
                                                               onchange="previewImage(this, 'preResource2')" {{ !isset($contentsInfo)??'required' }}>
                                                        <label for="resource3">Sub(2)</label>
                                                        <input type="file" class="form-control" id="resource3"
                                                               name="resource3"
                                                               onchange="previewImage(this, 'preResource3')" {{ !isset($contentsInfo)??'required' }}>
                                                        <label for="resource4">Sub(3)</label>
                                                        <input type="file" class="form-control" id="resource4"
                                                               name="resource4"
                                                               onchange="previewImage(this, 'preResource4')" {{ !isset($contentsInfo)??'required' }}>
                                                    </div>

                                                    <div class="col-md-3 mb-4">
                                                        <div class="col-md-11 text-center">
                                                            <span
                                                                class="red-underline font-weight-bold">미리보기(Preview)</span>
                                                        </div>
                                                        <div class="preview-container">
                                                            @if(!isset($contentsInfo->getContentsResource))
                                                                <div class="preview-item">
                                                                    <label for="preResource1">Thumbnail(0)</label>
                                                                    <div id="preResource1" style="margin-bottom: 10px">
                                                                    </div>
                                                                </div>
                                                                <div class="preview-item">
                                                                    <label for="preResource2">Sub(1)</label>
                                                                    <div id="preResource2" style="margin-bottom: 10px">
                                                                    </div>
                                                                </div>
                                                                <div class="preview-item">
                                                                    <label for="preResource3">Sub(2)</label>
                                                                    <div id="preResource3" style="margin-bottom: 10px">
                                                                    </div>
                                                                </div>
                                                                <div class="preview-item">
                                                                    <label for="preResource4">Sub(3)</label>
                                                                    <div id="preResource4" style="margin-bottom: 10px">
                                                                    </div>
                                                                </div>
                                                            @else
                                                                @foreach($contentsInfo->getContentsResource as $resource)
                                                                    @continue($resource->country_id == 2)
                                                                    <div class="preview-item">
                                                                        <label
                                                                            for="preResource{{ $resource->sort }}">{{ ($resource->sort == 1)? 'Thumbnail' :'Sub' }}
                                                                            ({{ ($resource->sort - 1) }})</label>
                                                                        <div id="preResource{{ $resource->sort }}" style="margin-bottom: 10px">
                                                                            @if($resource->f_format === 'image')
                                                                                <img
                                                                                    src="{{ env('NCLOUD_OBJECT_STORAGE_URL')."/".$resource->f_path.'/'.$resource->f_id }}">
                                                                            @else
                                                                                <video
                                                                                    src="{{ env('NCLOUD_OBJECT_STORAGE_URL')."/".$resource->f_path.'/'.$resource->f_id }}">
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="col-md-11 text-center">
                                                            <label for="description" class="red-underline">콘텐츠 한글
                                                                설명(Description)</label>
                                                        </div>
                                                        <textarea class="form-control" id="description"
                                                                  name="description" rows="3" required
                                                                  style="width: 100%;height: 60%">{{ ($contentsInfo->getContentsText[0]->text_desc)??'' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            {{--영어 --}}
                                            <div class="tab-pane fade" id="country2" role="tabpanel"
                                                 aria-labelledby="country2-tab">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <div class="col-md-12 text-center">
                                                            <span class="red-underline font-weight-bold">파일 업로드(FileUpload)</span>
                                                        </div>
                                                        <label for="resource1_eng">Thumbnail(0)</label>
                                                        <input type="file" class="form-control" id="resource1_eng"
                                                               name="resource1Eng"
                                                               onchange="previewImage(this, 'preResource1Eng')">
                                                        <label for="resource2_eng">Sub(1)</label>
                                                        <input type="file" class="form-control" id="resource2_eng"
                                                               name="resource2Eng"
                                                               onchange="previewImage(this, 'preResource2Eng')">
                                                        <label for="resource3_eng">Sub(2)</label>
                                                        <input type="file" class="form-control" id="resource3_eng"
                                                               name="resource3Eng"
                                                               onchange="previewImage(this, 'preResource3Eng')">
                                                        <label for="resource4_eng">Sub(3)</label>
                                                        <input type="file" class="form-control" id="resource4_eng"
                                                               name="resource4Eng"
                                                               onchange="previewImage(this, 'preResource4Eng')">
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <div class="col-md-11 text-center">
                                                            <span
                                                                class="red-underline font-weight-bold">미리보기(Preview)</span>
                                                        </div>
                                                        <div class="preview-container">
                                                            <div class="preview-item">
                                                                <label for="preResource1Eng">
                                                                    <span style="font-size:13px">Thumbnail(0)</span>
                                                                    @if(isset($contentsInfo->getContentsResource[4])) <i class="fa fa-fw fa-trash" onclick="deleteResource({{ $contentsInfo->getContentsResource[4]->contents_resource_id }} )"></i> @endif
                                                                </label>
                                                                <div id="preResource1Eng" style="margin-bottom: 10px">
                                                                    @if(isset($contentsInfo->getContentsResource[4]))
                                                                        @if($contentsInfo->getContentsResource[4]->f_format === 'image')
                                                                            <img
                                                                                src="{{ env('NCLOUD_OBJECT_STORAGE_URL').'/'.$contentsInfo->getContentsResource[4]->f_path.'/'.$contentsInfo->getContentsResource[4]->f_id }}">
                                                                        @else
                                                                            <video
                                                                                src="{{  env('NCLOUD_OBJECT_STORAGE_URL').'/'.$contentsInfo->getContentsResource[4]->f_path.'/'.$contentsInfo->getContentsResource[4]->f_id }}">
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="preview-item">
                                                                <label for="preResource2Eng">
                                                                    Sub(1)
                                                                    @if(isset($contentsInfo->getContentsResource[5])) <i class="fa fa-fw fa-trash" onclick="deleteResource({{ $contentsInfo->getContentsResource[5]->contents_resource_id }} )"></i> @endif
                                                                </label>
                                                                <div id="preResource2Eng" style="margin-bottom: 10px">
                                                                    @if(isset($contentsInfo->getContentsResource[5]))
                                                                        @if($contentsInfo->getContentsResource[5]->f_format === 'image')
                                                                            <img
                                                                                src="{{ env('NCLOUD_OBJECT_STORAGE_URL').'/'.$contentsInfo->getContentsResource[5]->f_path.'/'.$contentsInfo->getContentsResource[5]->f_id }}">
                                                                        @else
                                                                            <video
                                                                                src="{{ env('NCLOUD_OBJECT_STORAGE_URL').'/'.$contentsInfo->getContentsResource[5]->f_path.'/'.$contentsInfo->getContentsResource[5]->f_id }}">
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="preview-item">
                                                                <label for="preResource3Eng">
                                                                    Sub(2)
                                                                    @if(isset($contentsInfo->getContentsResource[6])) <i class="fa fa-fw fa-trash" onclick="deleteResource({{ $contentsInfo->getContentsResource[6]->contents_resource_id }} )"></i> @endif
                                                                </label>
                                                                <div id="preResource3Eng" style="margin-bottom: 10px">
                                                                    @if(isset($contentsInfo->getContentsResource[6]))
                                                                        @if($contentsInfo->getContentsResource[6]->f_format === 'image')
                                                                            <img
                                                                                src="{{ env('NCLOUD_OBJECT_STORAGE_URL').'/'.$contentsInfo->getContentsResource[6]->f_path.'/'.$contentsInfo->getContentsResource[6]->f_id }}">
                                                                        @else
                                                                            <video
                                                                                src="{{  env('NCLOUD_OBJECT_STORAGE_URL').'/'.$contentsInfo->getContentsResource[6]->f_path.'/'.$contentsInfo->getContentsResource[6]->f_id }}">
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="preview-item">
                                                                <label for="preResource4Eng">
                                                                    Sub(3)
                                                                    @if(isset($contentsInfo->getContentsResource[7])) <i class="fa fa-fw fa-trash" onclick="deleteResource({{ $contentsInfo->getContentsResource[7]->contents_resource_id }} )"></i> @endif
                                                                </label>
                                                                <div id="preResource4Eng" style="margin-bottom: 10px">
                                                                    @if(isset($contentsInfo->getContentsResource[7]))
                                                                        @if($contentsInfo->getContentsResource[7]->f_format === 'image')
                                                                            <img
                                                                                src="{{ env('NCLOUD_OBJECT_STORAGE_URL').'/'.$contentsInfo->getContentsResource[7]->f_path.'/'.$contentsInfo->getContentsResource[7]->f_id }}">
                                                                        @else
                                                                            <video
                                                                                src="{{ env('NCLOUD_OBJECT_STORAGE_URL').'/'.$contentsInfo->getContentsResource[7]->f_path.'/'.$contentsInfo->getContentsResource[7]->f_id }}">
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="col-md-11 text-center">
                                                            <label for="description1" class="red-underline">콘텐츠 영어
                                                                설명(Description)</label>
                                                        </div>
                                                        <textarea class="form-control" id="description1"
                                                                  name="descriptionEng" rows="3"
                                                                  style="width: 100%;height: 60%"> {{ ($contentsInfo->getContentsText[1]->text_desc)??'' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="sortCount" class="col-sm-3 col-form-label label-color">점수 단위</label>
                                    <div class="px-2"></div>
                                    <div class="col-sm-3">
                                        <select class="col-sm-3 form-control" name="scoreType">
                                            @foreach($scoreType as $type)
                                                <option
                                                    value={{ $type->contents_score_type_id }} {{ ( isset($contentsInfo->contents_score_type_id) && $type->contents_score_type_id == $contentsInfo->contents_score_type_id)? 'selected':'' }}>{{ $type->score_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="sortCount" class="col-sm-3 col-form-label label-color">노출 정렬순서</label>
                                    <div class="px-2"></div>
                                    <div class="col-sm-1">
                                        <input type="text" class="form-control" id="sortCount" name="sort" style="text-align: center"
                                               value='{{ ($contentsInfo->sort)??$contentsMaxSort }}' readonly >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="genre" class="col-sm-3 col-form-label label-color">스코어 정렬기준</label>
                                    <div class="px-2"></div>
                                    <div class="col-sm-8">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sortBy"
                                                   value="desc" {{ (isset($contentsInfo) && $contentsInfo->sort_by === 'desc')? 'checked':'' }} {{ ($contentsInfo)??'checked'  }} >
                                            <label class="form-check-label" for="action">내림차순(Desc)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sortBy"
                                                   {{ (isset($contentsInfo) && $contentsInfo->sort_by === 'asc')? 'checked':'' }}  value="asc">
                                            <label class="form-check-label" for="adventure">오름차순(Asc)</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 offset-sm-3 text-center">
                                    <input type="submit" class="btn btn-primary ms-2"
                                           style="margin-right: 10px;width: 10%"
                                           value="{{ isset($contentsInfo)? '수정':'등록' }}">
                                    <input type="button" class="btn btn-primary ms-2" style="width: 10%"
                                           onclick="back()" value="취소">
                                </div>
                            </form>
                        </div>
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
        // 등록
        function submitForm() {
            @if(!isset($contentsInfo))
            if ($('#nameCheck').val() == 1) {
                alert('콘텐츠 이름 중복 확인을 해주세요.');
                return false;
            } else if ($('#enNameCheck').val() == 1) {
                alert('영문 이름 중복 확인을 해주세요.');
                return false;
            } else if (!$('#resource1').val()) {
                alert('한글 섬네일을 입력해주세요.');
                return false;
            } else if (!$('#resource2').val()) {
                alert('한글 리소스 Sub(1)를 입력해주세요.');
                return false;
            } else if (!$('#resource3').val()) {
                alert('한글 리소스 Sub(2) 입력해주세요.');
                return false;
            } else if (!$('#resource4').val()) {
                alert('한글 리소스 Sub(3) 입력해주세요.');
                return false;
            } else if (!$('#description').val()) {
                alert('한글 컨텐츠 설명 입력해주세요.');
                return false;
            }
            @endif

            $('#form1').submit();
        }

        //뒤로가기
        function back() {
            window.history.back();
        }


        // 실행 파일 입력시 영문 이름과 비교
        function checkFileWithEnName() {
            var enName = $('#content_name_en').val(); // 영문 이름
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

        // 이름,영문이름 변경이 있을때 중복확인 초기화
        function changeCheck(target) {
            if (target === 'enNameCheck') { // 영문 이름을 변경시에 콘텐츠 코드 변경
                $('#content_code').val('')
                if ($('#enNameCheck').val() == 0) {
                    alert('영문 이름을 다시 입력하여 실행 파일을 다시 올려주세요.');
                    $('#content_executable').val('');
                }
            }

            $('#' + target).val(1);
        }

        // 콘텐츠 이름 , 영문 이름 중복 체크
        function duplicateCheck(type) {
            if (type === 'enName') {
                var name = $('#content_name_en').val();
                var checkPosition = 'enNameCheck';
                var spanText = '영문 이름'
            } else {
                var name = $('#content_name').val();
                var checkPosition = 'nameCheck';
                var spanText = '이름'
            }

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/admin/contents/exists-contents-name',
                type: 'POST',
                data: {name: name},
                success: function (data) {
                    if (data == 1) { // 중복
                        $('#' + type).text('중복된 '+spanText+' 입니다. 다시 입력해 주세요!');
                        $('#' + type).css('color', 'red');
                        $('#' + checkPosition).val(1);
                    } else if (data == 0) { // 사용가능
                        $('#' + type).text('사용하실 수 있는 '+spanText+' 입니다.');
                        $('#' + type).css('color', 'green');
                        $('#' + checkPosition).val(0);
                        if (checkPosition === 'enNameCheck') {
                            $('#content_code').val(name); // 영문이름 사용 가능이면 코드로 변경
                        }
                    } else {  // 에러
                        alert('에러가 발생 하였습니다.');
                    }

                },
                error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                },
            })
        }

        // 이미지,동영상 미리보기
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                if (file.type.includes('image')) {  // 이미지 파일인 경우
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById(previewId).innerHTML = '<img src="' + e.target.result + '" alt="미리보기 이미지">';
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.includes('video')) {  // 동영상 파일인 경우
                    var videoUrl = URL.createObjectURL(file); // 동영상 파일의 URL 생성
                    var videoElement = document.createElement('video');
                    videoElement.src = videoUrl;
                    videoElement.controls = true; // 재생 컨트롤을 표시하도록 설정
                    videoElement.style.maxWidth = '80%'; // 최대 너비 설정
                    document.getElementById(previewId).innerHTML = ''; // 이전 내용 초기화
                    document.getElementById(previewId).appendChild(videoElement); // 동영상 요소를 미리보기 컨테이너에 추가
                }
            }
        }

        // 센서 셀렉트박스 생성
        var sensorList = <?php echo $sensorList ?>;

        function addSensor(sensorId, defaultSensorId = null) {
            var sensorCount = document.querySelectorAll('.form-control[id^="sensor"]').length;
            if (sensorCount >= 3) {
                alert('최대 3개까지만 추가할 수 있습니다.');
                return;
            }
            if (sensorId !== 'sensor1') {
                sensorId = 'sensor' + (sensorCount + 1);
            }
            var container = document.createElement('div');
            container.className = 'd-flex align-items-center mb-2'; // 새로운 센서를 감싸는 div

            var label = document.createElement('label');
            label.className = 'col-sm-2 col-form-label';
            label.textContent = sensorId === 'sensor1' ? '1순위' : sensorId === 'sensor2' ? '2순위' : '3순위'; // 라벨 수정

            var select = document.createElement('select');
            select.className = 'col-sm-3 form-control';
            select.id = sensorId; // 센서 ID
            select.name = 'sensor[]'; // 센서 ID

            sensorList.forEach(function (sensor) {
                if (sensorId === 'sensor2') {
                    if (sensor['sensor_id'] == $('#sensor1').val()) {
                        return;
                    }
                }
                if (sensorId === 'sensor3') {
                    if (sensor['sensor_id'] == $('#sensor1').val() || sensor['sensor_id'] == $('#sensor2').val()) {
                        // 센서1과 센서2에서 선택한 값은 추가하지 않음
                        return;
                    }
                }
                var option = document.createElement('option');
                option.textContent = sensor['sensor_name'];
                option.value = sensor['sensor_id'];
                if (sensor['sensor_id'] === defaultSensorId) {
                    option.selected = true;
                }
                select.appendChild(option);
            });

            container.appendChild(label);
            container.appendChild(select);

            if (sensorId !== 'sensor1') { // 1순위 이외에만 - 버튼 추가
                var removeButton = document.createElement('input');
                removeButton.type = 'button';
                removeButton.className = 'btn btn-danger ms-2';
                removeButton.value = '-';
                removeButton.onclick = function () {
                    container.remove(); // 해당 센서 제거
                };
                container.appendChild(removeButton);
            }

            document.querySelector('.col-sm-5').appendChild(container);
        }

        // 초기에 1순위 센서 생성
            @if(!isset($contentsInfo->getContentsSensor)){
            addSensor('sensor1');
        }
            @else{
            @foreach($contentsInfo->getContentsSensor as $c => $sensorInfo)
            @php $c++; @endphp
            addSensor('sensor{{ $c }}', {{ $sensorInfo->sensor_id }});
            @endforeach
        }
        @endif


        $(document).ready(function () {
            $('#countryTab a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });

        // 버전 변경
        function updateVersion() {
            const major = document.getElementById('selectBox1').value;
            const minor = document.getElementById('selectBox2').value;
            const patch = document.getElementById('selectBox3').value;
            const versionSpan = document.getElementById('version');
            versionSpan.innerText = `${major}.${minor}.${patch}`;

            $('#versionValue').val(`${major}.${minor}.${patch}`);
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
