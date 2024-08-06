@extends('layouts.adminLayout')

@section('title')
    사용자 수정 폼
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

        </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="col-md-6" style="justify-content: center;">
                <form class="form-horizontal  border rounded p-4" action="{{ route('updateUserInfo') }}" method="post">
                    @csrf
                <input type="hidden" name="userId" value="{{ $userInfo->user_id }}">
                <input type="hidden" name="utCode" value="{{ $userInfo->ut_code }}">
                <input type="hidden" name="referer" value="{{ $referer }}">
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        <div class="border-bottom-line mt-4 mb-4 pb-2">
                            <h2 class="underline">프로필</h2>
                        </div>
                        <div class="tab-pane active" id="settings">
                                <div class="form-group row">
                                    <label for="inputName" class="col-sm-3 col-form-label">사용자 이름</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" name="name" max="6" pattern="[a-zA-Z0-9가-힣]*" id="inputName" value="{{ ($userInfo->name)??'' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail" class="col-sm-3 col-form-label">비밀번호</label>
                                    <div class="col-sm-5">
                                        <input type="password" class="form-control" name="password" id="inputEmail">
                                    </div>
                                    <span style="color: red">*비밀번호 변경을 원하실 경우에만 입력창에 변경할 비밀번호를 입력 후 하단 수정 버튼을 입력해주세요.</span>
                                </div>

                                <div class="form-group row">
                                    <label for="inputName2" class="col-sm-3 col-form-label">사용자 별명</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" name="nickName" max="8" pattern="[a-zA-Z0-9가-힣]*" id="inputName2" value="{{ ($userInfo->nick_name)??'' }}">
                                    </div>
                                </div>

                                @if($utCode === '20')
                                    <input type="hidden" name="agencyId" value="{{ ($userInfo->getAgencyInfo->agency_id)??'' }}" >
                                    <div class="border-bottom-line mt-4 mb-4 pb-2">
                                        <h2>기관정보</h2>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName1" class="col-sm-3 col-form-label">기관 이름</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="agencyName" max="10" pattern="[a-zA-Z0-9가-힣]*" id="inputName1" value="{{ ($userInfo->getAgencyInfo->agency_name)??'' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName3" class="col-sm-3 col-form-label">기관 전화번호</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="phoneNum" pattern="[0-9]*" id="inputName3" value="{{ ($userInfo->phone_num)??'' }}">
                                        </div>
                                    </div>
                                @elseif($utCode ==='21')
                                    <input type="hidden" name="groupId" value="{{ ($userInfo->getGroupInfo->group_id)??'' }}" >
                                    <div class="border-bottom-line mt-4 mb-4 pb-2">
                                        <h2>그룹정보</h2>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName3" class="col-sm-3 col-form-label">그룹 이름</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="groupName" max="10" pattern="[a-zA-Z0-9가-힣]*" id="inputName3" value="{{ ($userInfo->getGroupInfo->group_name)??'' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName4" class="col-sm-3 col-form-label">그룹 전화번호</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="phoneNum" pattern="[0-9]*" id="inputName4" value="{{ ($userInfo->phone_num)??'' }}">
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <div class="button">
                                        <button type="submit" class="block btn-success btn-xs term btn-css">수정</button>
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
@endsection
@section('script')
    <script>
        function goBack(){
            window.history.back();
        }
    </script>
@endsection
