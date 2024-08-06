<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\SignInRepository;
use App\Services\MakeRandCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PDOException;

class SignInController extends Controller
{
    public function __construct(SignInRepository $signIn)
    {
        $this->signIn = $signIn;
        $this->code = app(MakeRandCode::class);
    }

    /**
     * 로그인 폼
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function moveSignInForm()
    {
        return view('admin/signInForm');
    }

    /**
     * 로그인(웹페이지)
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function isLogin(Request $request)
    {
        if(!$request->input('loginId') || !$request->input('password')){
            return back()->withErrors('inputConfirm')->withInput();
        }

        if(Auth::attempt(['login_id'=> $request->input('loginId') , 'password' => $request->input('password')])) {
            if( Auth::user()['ut_code'] === '20' && (Auth::user()['verification_flag'] !== 'Y') ){
                return back()->withErrors(['error'=>'verification'])->withInput();
            } else if(Auth::user()['use_flag'] === 'N'){
                return back()->withErrors(['error'=>'useFlag'])->withInput();
            } else if(Auth::user()['ut_code'] === '23'){
                return back()->withErrors(['error'=>'guest'])->withInput();
            }
            return redirect('/admin/dashboard');
        }  else {
            return back()->withErrors((['error' => 'wrongUser']))->withInput();
        }
    }

    /**
     * 로그인(런처) API
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isLoginFromLauncher(Request $request): \Illuminate\Http\JsonResponse
    {
        $result=[];
        if(!$request->input('userId')){
            $result['code'] = -100;
            $result['userInfo'] = '';
            $result['message'] = '키값이 없습니다.';
            return response()->json($result,200,[],JSON_UNESCAPED_UNICODE);
        }

        // 유저 정보 조회
        $userInfo = $this->signIn->getUserInfo($request->input('userId'));

        if(!$userInfo){  // 해당 ID가 없을떄
            $result['code'] = -101;
            $result['userInfo'] = [];
            $result['message'] = '해당 ID의 유저는 존재하지 않습니다.';
        } else if($userInfo->use_flag === 'N'){ // 정지된 회원
            $result['code'] = -201;
            $result['userInfo'] = [];
            $result['message'] = '사용 정지된 회원 입니다.';
        } else if($userInfo->ut_code === '20' && $userInfo->verification_flag !== 'Y') { // 단체 관리자 승인 X 일때
            $result['code'] = -202;
            $result['userInfo'] = [];
            $result['message'] = '단체 관리자 회원은 관리자의 승인이 필요합니다.';
        } else if($userInfo->ut_code === '23'){
            $result['code'] = 200;
            $result['userInfo'] = $userInfo;
            $result['message'] = '게스트 회원 입니다.';
            if(!$this->signIn->isToken($userInfo->login_id)) {
                $this->makeToken($userInfo->login_id);
            }
        } else if (Hash::check($request->password , $userInfo->password)) { // 패스워드 비교
            $result['code'] = 200;
            $result['userInfo'] = $userInfo;
            $result['message'] = '유저 정보 확인 되었습니다.';
            if(!$this->signIn->isToken($userInfo->login_id)) {
                $this->makeToken($userInfo->login_id);
            }
        } else {
            $result['code'] = 0;
            $result['userInfo'] = [];
            $result['message'] = ' 가입된 회원이 아니거나 패스워드가 틀립니다.';
        }

        return response()->json($result,200,[],JSON_UNESCAPED_UNICODE);
    }

    /**
     * 토큰 생성
     * @param string $loginId
     * @return void
     */
    public function makeToken(string $loginId): void
    {
        $insertParam['login_id']   = $loginId;
        $insertParam['token_code'] = $this->code->getRandCode(10);
        $insertParam['expried_date'] = Carbon::now()->addHour(3);
        $this->signIn->makeToken($insertParam);
    }

    /**
     * 로그아웃
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Auth::logout();
        return redirect(route('signInForm'));
    }

}
