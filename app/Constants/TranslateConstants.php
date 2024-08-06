<?php

namespace App\Constants;

class TranslateConstants
{
    // 회원가입 폼
    public const SIGNUP_FORM = [
        'ko' => array(
            'title' => '회원가입',
            'language' =>'언어 선택',
            'loginId' => '사용자 ID',
            'checkId' => 'ID 중복 체크',
            'checkIdMessage' => 'ID 중복 체크를 해주세요',
            'available' => '해당 ID는 사용 가능 합니다',
            'duplicate' => 'ID는 이미 사용중 입니다.',
            'password' => '비밀번호',
            'userName' => '사용자 이름',
            'userNickName' => '사용자 별명',
            'phoneNum' => '휴대폰 번호',
            'email' => '이메일',
            'emailSend' => '해당 이메일주소로 이메일을 발송 하였습니다.',
            'sendVerificationEmail' => '인증코드',
            'checkVerification' => '인증',
            'verificationTrue' => '인증 완료 되었습니다.',
            'verificationFalse' => '인증 코드가 올바르지 않습니다.',
            'noVerification' => '메일 인증을 해주세요.',
            'birthday' => '생년월일',
            'termsTitle' => '이용 약관',
            'agreeTerms' => '이용 약관에 동의 합니다.',
            'terms' => '이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.
                            이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.이용 약관 내용을 입력하세요.',
            'privacyTitle' => '개인정보 처리방침',
            'agreePrivacy' => '개인정보 처리방침에 동의합니다.',
            'privacy' => '개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.
                            개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.개인정보 처리방침 내용을 입력하세요.',
            'signUp' => '가입하기',
            'agree' => '동의하기',
            'disagree' => '동의안함',
            'agencyName' => '기관명',
            'personalGroup' => '개인',
            'group' => '그룹',
        ),
        'en' => array(
            'title' => 'Sign Up',
            'language' =>'Select Language',
            'loginId' => 'Login Id',
            'checkId' => 'Duplicate Check',
            'checkIdMessage' => 'Please Duplicate Check',
            'available' => 'available ID',
            'duplicate' => 'Duplicate ID',
            'password' => 'Password',
            'userName' => 'User Name',
            'userNickName' => 'User NickName',
            'phoneNum' => 'Phone Number',
            'email' => 'Email',
            'emailSend' => 'Email Has Been Sent To That Email Address.',
            'sendVerificationEmail' => 'Certify Code',
            'checkVerification' => 'Verification',
            'verificationTrue' => 'Verification Completed',
            'verificationFalse' => 'Verification Code does Not Match.',
            'noVerification' => 'Please Verify Your Email.',
            'birthday' => 'Birthday',
            'termsTitle' => 'Terms',
            'agreeTerms' => 'I agree to the terms and conditions.',
            'terms' => 'Last Updated: August 11, 2023
                            Welcome to the Amazon Web Services site (the “AWS Site”). Amazon Web Services, Inc. and/or its affiliates (“AWS”) provides the AWS Site to you subject to the following terms of use (“Site Terms”). By visiting the AWS Site, you accept the Site Terms. Please read them carefully. In addition, when you use any current or future AWS services, content or other materials, you also will be subject to the AWS Customer Agreement or other agreement governing your use of our services (the “Agreement”).
                            PRIVACY
                            Please review our Privacy Policy, which also governs your visit to the AWS Site, to understand our practices.
                            ELECTRONIC COMMUNICATIONS
                            When you visit the AWS Site or send e-mails to us, you are communicating with us electronically. You consent to receive communications from us electronically. We will communicate with you by e-mail or by posting notices on the AWS Site. You agree that all agreements, notices, disclosures and other communications that we provide to you electronically satisfy any legal requirement that such communications be in writing.
                            COPYRIGHT
                            All content included on the AWS Site, such as text, graphics, logos, button icons, images, audio clips, digital downloads, data compilations, and software, is the property of AWS or its content suppliers and protected by United States and international copyright laws. The compilation of all content on the AWS Site is the exclusive property of AWS and protected by U.S. and international copyright laws. All software used on the AWS Site is the property of AWS or its software suppliers and protected by United States and international copyright laws.
                            TRADEMARKS
                            “Amazon Web Services”, “AWS”, “Amazon EC2”, “EC2”, “Amazon Elastic Compute Cloud”, “Amazon Virtual Private Cloud”, “Amazon VPC”, “Amazon SimpleDB”, “SimpleDB”, “Amazon S3”, “Amazon Simple Storage Service”, “Amazon CloudFront”, “CloudFront”, “Amazon SQS”, “SQS”, “Amazon Simple Queue Service”, “Amazon Simple Email Service”, “Amazon Elastic Beanstalk”, “Amazon Simple Notification Service”, “Amazon Route 53”, “Amazon RDS”, “Amazon Relational Database”, “Amazon CloudWatch”, “AWS Premium Support”, “AWS Import/Export”, “Amazon FPS”, “Amazon Flexible Payments Service”, “Amazon DevPay”, “DevPay”, “Amazon Mechanical Turk”, “Mechanical Turk”, “Alexa Web Search”, “Alexa Web Information Service”, “Alexa Top Sites”, “Alexa Site Thumbnail”, “Amazon FWS”, “Amazon Fulfillment Web Service”, “Amazon Associates Web Service”, and other AWS graphics, logos, page headers, button icons, scripts, and service names are trademarks, registered trademarks or trade dress of AWS in the U.S. and/or other countries. AWS’s trademarks and trade dress may not be used in connection with any product or service that is not AWS’s, in any manner that is likely to cause confusion among customers, or in any manner that disparages or discredits AWS. All other trademarks not owned by AWS that appear on this Site are the property of their respective owners, who may or may not be affiliated with, connected to, or sponsored by AWS.
                            LICENSE AND SITE ACCESS
                            AWS grants you a limited license to access and make personal use of the AWS Site and not to download (other than page caching) or modify it, or any portion of it, except with express written consent of AWS. This license does not include any resale or commercial use of the AWS Site or its contents; any derivative use of the AWS Site or its contents; any downloading or copying of account information; or any use of data mining, robots, or similar data gathering and extraction tools. Unless otherwise specified by AWS in a separate license, your right to use any software, data, documentation or other materials that you access or download through the AWS Site is subject to these Site Terms or, if you have an AWS account, the Agreement. The materials hosted on docs.aws.amazon.com are licensed as follows: documentation (e.g., user guides, developer guides, other publications) is licensed under CC-BY-SA-4.0, while any code therein is licensed under MIT-0.
                            The AWS Site or any portion of the AWS Site may not be reproduced, duplicated, copied, sold, resold, visited, or otherwise exploited for any commercial purpose without express written consent or license of AWS. You may not frame or utilize framing techniques to enclose any trademark, logo, or other proprietary information (including images, text, page layout, or form) of AWS without express written consent. You may not use any meta tags or any other “hidden text” utilizing AWS’s name or trademarks without the express written consent of AWS. Any unauthorized use terminates the permission or license granted by AWS. You are granted a limited, revocable, and nonexclusive right to create a hyperlink to the home page of the AWS Site, so long as the link does not portray AWS, or its products or services in a false, misleading, derogatory, or otherwise offensive matter. You may not use any AWS logo or other proprietary graphic or trademark as part of the link without express written permission.',
            'privacyTitle' => 'Privacy',
            'agreePrivacy' => 'I agree to the privacy policy.',
            'privacy' => 'To see prior version, click here.
                            This Privacy Notice describes how we collect and use your personal information in relation to AWS websites, applications, products, services, events, and experiences that reference this Privacy Notice (together, “AWS Offerings”).
                            This Privacy Notice does not apply to the “content” processed, stored, or hosted by our customers using AWS Offerings in connection with an AWS account. See the agreement governing your access to your AWS account and the AWS Data Privacy FAQ for more information about how we handle content and how our customers can control their content through AWS Offerings. This Privacy Notice also does not apply to any products, services, websites, or content that are offered by third parties or have their own privacy notice.
                            Personal Information We Collect
                            How We Use Personal Information
                            Cookies
                            How We Share Personal Information
                            Location of Personal Information
                            How We Secure Information
                            Access and Choice
                            Children’s Personal Information
                            Third Party Sites and Services
                            Retention of Personal Information
                            Contacts, Notices, and Revisions
                            EU-US and Swiss-US Data Privacy Framework
                            Additional Information for Certain Jurisdictions
                            Examples of Information Collected
                            Personal Information We Collect
                            We collect your personal information in the course of providing AWS Offerings to you.
                            Here are the types of information we gather:
                            Information You Give Us: We collect any information you provide in relation to AWS Offerings. Click here to see examples of information you give us.
                            Automatic Information: We automatically collect certain types of information when you interact with AWS Offerings. Click here to see examples of information we collect automatically.
                            Information from Other Sources: We might collect information about you from other sources, including service providers, partners, and publicly available sources. Click here to see examples of information we collect from other sources.',
            'signUp' => 'Sign Up',
            'agree' => 'Agree',
            'disagree' => 'Disagree',
            'agencyName' => 'Agency Name',
            'personalGroup' => 'Personal Group',
            'group' => 'Group',
        )
        ];

    // 로그인 폼
    public const SIGNIN_FORM = [
        'ko' => array(
            'title'                 => '관리자 로그인',
            'loginId'               => '사용자 ID',
            'password'              => '비밀번호',
            'signIn'                => '로그인',
            'inputConfirm'          => 'ID 혹은 PASSWORD를 입력해주세요.',
            'verification'          => '기관 관리자 회원은 관리자의 승인이 필요합니다.',
            'useFlag'               => '사용 정지된 회원 입니다.',
            'guest'                 => '게스트 회원은 로그인 하실 수 없습니다.',
            'wrongUser'             => '가입된 회원이 아니거나 패스워드가 틀립니다.',
        ),
        'en' => array(
            'title'                 => 'Admin Login',
            'loginId'               => 'Login Id',
            'password'              => 'Password',
            'signIn'                => 'Sign In',
            'inputConfirm'          => 'Please Check Id and Password.',
            'verification'          => 'Agency Member Needs Approval From Manager.',
            'useFlag'               => 'blocked Member.',
            'guest'                 => 'Guest Members Are Not Allowed To Sign In.',
            'wrongUser'             => 'Not a Registered Member Or You Have a Wrong Password.',
        )
    ];

    // 회원가입 메일 인증 폼
    public const MAIL_VERIFICATION_FORM = [
        'ko' => array(
            'title'                 => '이메일 인증',
            'hello'                 => '안녕하세요. 반갑습니다.',
            'thanks'                => 'XRSPORTS Platform에 회원가입을 위해서는 다음 코드를 입력해 주세요.',
            'code'                  => '이메일 인증 코드',
            'ignore'                => '인증을 시도하지 않았다면 이 메세지를 무시해도 됩니다.',
            'footer'                => '회원님의 요청에 따라 airpass@gmail.com 주소에서 발송된 메시지 입니다.'
        ),
        'en' => array(
            'title'                 =>'Email Verification',
            'hello'                 => 'Welcome to XRSPORTS Platform.',
            'thanks'                => 'Please use following code to verify your account.',
            'code'                  => 'Email Verification Code',
            'ignore'                => 'If you did Not request This Verification, You can safely ignore This Email',
            'footer'                => 'This email sent to airpass@gmail.com as requested by the user.'
        )
    ];

    // 라이센스 입력 폼
    public const LICENSE_FORM = [
        'ko' => array(
            'title'   => '라이센스 폼',
            'welcome' => '환영합니다<br> 라이센스를 입력해주세요',
            'submit'  => '확인',
            'confirm' => '라이센스 확인 되었습니다.',
            'already' => '이미 사용중인 라이센스입니다.',
            'pause'   => '사용 중지된 라이센스입니다.',
            'invalid' => '잘못된 라이센스입니다.'
        ),
        'en' => array(
            'title'   => 'License Code form',
            'welcome' => "Welcome <br> Please Enter the License Code ",
            'submit'  => 'Confirm',
            'confirm' => 'License Code Has Been Confirmed.',
            'already' => 'License Code Has Already Benn Used.',
            'pause'   => 'License Code Was Paused',
            'invalid' => 'Invalid License Code.'
        )
    ];

    // 그룹 폼
    public const GROUP_FORM = [
        'ko' => array(
            'checkIdMessage' => 'ID 중복 체크를 해주세요',
            'checkGroupName'      => '그룹 이름을 입력해주세요.',
            'checkLoginId'        => '사용자ID를 입력해주세요.',
            'checkPassword'       => '비밀번호를 입력해주세요.'
        ),
        'en' => array(
            'checkIdMessage' => 'Please Duplicate Check',
            'checkGroupName'      => 'Please enter a Group Name.',
            'checkLoginId'        => 'Please enter Login ID.',
            'checkPassword'       => 'Please enter Password.'
        )
    ];

    // 유저 폼
    public const USER_FORM = [
        'ko' => array(
            'checkIdMessage'      => 'ID 중복 체크를 해주세요',
            'checkName'           => '이름을 입력해주세요.',
            'checkLoginId'        => '사용자ID를 입력해주세요.',
            'checkPassword'       => '비밀번호를 입력해주세요.'
        ),
        'en' => array(
            'checkIdMessage'      => 'Please Duplicate Check',
            'checkName'           => 'Please enter a Name.',
            'checkLoginId'        => 'Please enter Login ID.',
            'checkPassword'       => 'Please enter Password.'
        )
    ];


    public const PERSONAL_LIST = [
        'ko' => array(
            'changeSort'      => '최대 혹은 최소 정렬번호 입니다.',
        ),
        'en' => array(
            'changeSort'          => 'This is the maximum or minimum sort number.',
        )
    ];


    // 어드민 레프트 메뉴
    public const ADMIN_LEFT_MENU = [
        'ko' => array(
            'board'                 => '게시판 관리',
            'notice'                => '공지사항',
            'operationsManagement'  => '운영가이드 관리',
            'contentsManagement'    => '콘텐츠 관리',
            'contentsInformation'   => '콘텐츠 정보',
            'agencyManagement'      => '기관 관리',
            'agencyInformation'     => '기관 정보',
            'resourceManagement'    => '리소스 관리'
        ),
        'en' => array(
            'board'                 => 'Board',
            'notice'                => 'Notice',
            'operationsManagement'  => 'Operations Management',
            'contentsManagement'    => 'Contents Management',
            'contentsInformation'   => 'Contents Information',
            'agencyManagement'      => 'Agency Management',
            'agencyInformation'     => 'Agency Information',
            'resourceManagement'    => 'Resource Management'
        )
    ];

    // 성공 메세지
    public const SUCCESS_MESSAGE = [
        'ko' => array(
            'success'                 => '변경 완료 하였습니다.',
        ),
        'en' => array(
            'success'                 => 'The change has been completed.',
        )
    ];

    // 에러 메세지
    public const ERROR_MESSAGE = [
        'ko' => array(
            'error'                 => '에러가 발생하였습니다. 잠시 후 다시 시도해주세요.',
        ),
        'en' => array(
            'error'                 => 'Error Has Occurred. Please Try Again In a Few Minutes',
        )
    ];
}
