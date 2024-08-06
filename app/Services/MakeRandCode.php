<?php

namespace App\Services;

class MakeRandCode
{
    /**
     * 하이폰 들어간 랜덤코드 생성
     * @param int $total // 총 갯수
     * @param int $digit // 자릿수
     * @return string
     */
    public function getHyphenRandCode(int $total, int $digit ){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $total; $i++) {
            for ($j = 0; $j < $digit; $j++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
            if ($i === ($total-1)) {
                break;
            }
            $code .= '-';
        }
        return $code;
    }

    /**
     * 일반 랜덤코드 생성
     * @param int $total
     * @return string
     */
    public function getRandCode(int $total){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $total; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }

    /**
     * 일반 랜덤코드 생성(소문자추가)
     * @param int $total
     * @return string
     */
    public function getRandCodeWithLoweCase(int $total){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $code = '';
        for ($i = 0; $i < $total; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }


}
