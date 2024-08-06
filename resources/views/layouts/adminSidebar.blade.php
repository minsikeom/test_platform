
<!-- 로딩 이미지 -->
{{--<div class="preloader flex-column justify-content-center align-items-center">--}}
{{--    <img class="animation__shake" src="/admin/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">--}}
{{--</div>--}}

<style>
    .change {
        background-color: rgba(255, 255, 255, .1);
        color: #fff;
    }

    .main-sidebar {
        width: 250px; /* 고정된 너비 */
    }

    .nav-sidebar .nav-item > .nav-link {
        white-space: nowrap; /* 텍스트 줄바꿈 방지 */
        overflow: hidden;
        text-overflow: ellipsis; /* 텍스트 넘칠 때 ...으로 표시 */
    }

    .nav-sidebar .nav-item {
        width: 100%; /* nav-item의 너비를 100%로 설정 */
    }

    .nav-sidebar .nav-item .nav-treeview .nav-item .nav-link {
        padding-left: 2rem; /* 서브 메뉴 항목의 들여쓰기 */
    }

    /* 추가된 부분: flexbox 설정을 명확히 함 */
    .nav-sidebar .nav-item > .nav-link {
        display: flex;
        align-items: center;
    }

    /* 추가된 부분: 아이콘과 텍스트 간의 간격 조정 */
    .nav-sidebar .nav-item > .nav-link > i {
        margin-right: 8px;
    }

    /* 추가된 부분: 아이콘과 텍스트 간의 간격 조정 (서브 메뉴) */
    .nav-sidebar .nav-treeview .nav-item > .nav-link > i {
        margin-right: 8px;
    }
</style>



<!-- Main Sidebar Container -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary">
    <!-- Brand Logo -->
    <a href="/admin/dashboard" class="brand-link">
        <img src="/admin/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">XR SPORTS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- 대쉬보드 -->
                <li class="nav-item dashboard">
                    <a href="/admin/dashboard" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <!-- 대쉬보드 끝 -->

                <!-- 사용자 관리 -->
                <li class="nav-item user">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="fa fa-fw fa-user"></i>
                        <p>
                            사용자 관리
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            @php
                                if(Auth::user()->ut_code === '00' ){  // 슈퍼 관리자
                                    $url = "/admin/user/main/list";
                                } else if(Auth::user()->ut_code === '20'){ // 기관 관리자
                                    $url = "/admin/user/agency/group/list?agency=".Auth::user()->agency_id;
                                } else if(Auth::user()->ut_code === '21') { // 그룹 관리자
                                    $url = "/admin/user/agency/group/user-list?agency=".Auth::user()->agency_id."&group=".Auth::user()->group_id;
                                }
                            @endphp
                            <a href='{{ $url }}' class="nav-link">
                                <p style="padding-left:8%"><i class="fa fa-fw fa-arrow-right"></i> 사용자 목록</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- 사용자 관리 끝 -->

                <!-- 콘텐츠 관리 -->
                @if(Auth::user()->ut_code === '00')
                    <li class="nav-item contents">
                        <a href="javascript:void(0)" class="nav-link">
{{--                            <i class="fa fa-fw fa-user"></i>--}}
                            <i class="fa fa-fw fa-gamepad"></i>
                            <p>
                                콘텐츠 관리
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/admin/contents/list" class="nav-link">
                                    <p style="padding-left:8%">
                                        <i class="fa fa-fw fa-arrow-right"></i> 콘텐츠 목록</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item platform">
                        <a href="javascript:void(0)" class="nav-link">
                            <i class="fa fa-fw fa-cogs"></i>
                            <p>
                                플렛폼 관리
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="/admin/platform/theme/list" class="nav-link">
                                    <p style="padding-left:8%"><i class="fa fa-fw fa-arrow-right"></i> 테마</p>
                                </a>
                            </li>

                            <li class="nav-item product">
                                <a href="/admin/platform/product/list" class="nav-link">
                                    <p style="padding-left:8%"><i class="fa fa-fw fa-arrow-right"></i> 상품</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/admin/platform/sensor/list" class="nav-link">
                                    <p style="padding-left:8%"><i class="fa fa-fw fa-arrow-right"></i> 센서</p>
                                </a>
                            </li>

                            <li class="nav-item genre">
                                <a href="/admin/platform/genre/list" class="nav-link">
                                    <p style="padding-left:8%"><i class="fa fa-fw fa-arrow-right"></i> 장르</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif
                <!-- 콘텐츠 관리 끝 -->

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
    $(document).ready(function () {
        // 현재 경로
        var currentPath = window.location.pathname;
        var selectMenu = $('.'+ currentPath.split('/')[2]); // user,contents 등등
        selectMenu.addClass('menu-open');

        if(currentPath.split('/')[2] === 'platform') {
            var specificDistinguish  = $('.'+ currentPath.split('/')[3]);
            specificDistinguish.find('.nav-link').addClass('change');
        } else {
            var subMenu = selectMenu.find('.nav-item').find('.nav-link');
            subMenu.addClass('change');
        }
    });

</script>

