<?php

namespace App\Constants;

class ContentsConstants
{
    // 콘텐츠 타입
    public const CONTENTS_TYPE = [
        'RK'    =>  '랭킹챌린지',
        'XR'    =>  'XR 콘텐츠',
        'AL'    =>  '콘텐츠 타입',
        'OB'    => '온라인배틀'
        ];

    // 콘텐츠 카테고리
    public const CONTENTS_CATEGORY = [
        '10'    =>  '융합교육',
        '20'    =>  '교과융합',
        '30'    =>  '스포츠',
        '40'    =>  '스포츠놀이',
    ];

    // 콘텐츠 센서
    public const CONTENTS_SENSOR = [
        '00'    =>  'Lidar',
        '10'    =>  '3xVision',
        '20'    =>  'Treadmill',
        '21'    =>  'Treadmill_BT',
        '22'    =>  'Treadmill_Wifi',
        '30'    =>  'ActionFloor',
        '31'    =>  'Concept2_SkiERG',
        '40'    =>  'bike',
        '50'    =>  'Laser',
        '60'    =>  'ARKinect',
        '90'    =>  'test',
        '91'    =>  'shoot',
    ];

    // 점수 단위
    public const CONTENTS_CRT_CODE = [
      '10' => '점수', '20' => '시간'
    ];

}
