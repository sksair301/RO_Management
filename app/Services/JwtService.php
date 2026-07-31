<?php

namespace App\Services;

class JwtService{

    private $secret;

    public function __construct(){

        $this->secret = env('JWT_SECRET');
    }

    private function base64UrlEncode($data){

        return rtrim(strtr(base64_encode($data),'+/','-_'),'=');
    }

    private function base64UrlDecode($data){

        return base64_decode(strtr($data,'-_','+/'));
    }

    public function generateToken($user){

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'id' => $user->id,
            'roles_id' => $user->roles_id,
            'departments_id' => $user->departments_id,
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $header = $this->base64UrlEncode(json_encode($header));

        $payload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $header.".".$payload,
            $this->secret,
            true
        );

        $signature = $this->base64UrlEncode($signature);

        return $header.".".$payload.".".$signature;
    }

    public function verifyToken($token){

        $parts = explode('.',$token);

        if(count($parts) !=3){
            return response()->json([
                'success' => FALSE,
                'message' => 'Missing Token',
            ],401);
        }

        [$header, $payload, $signature] = $parts;

        $expected = $this->base64UrlEncode(
            hash_hmac(
                'sha256',
                $header.".".$payload,
                $this->secret,
                true
            )
        );

        if(!hash_equals($expected,$signature)){
            return response()->json([
                'success'=>FALSE,
                'message'=>'Invalid Token',
            ],401);
        }

        $payload = json_decode($this->base64UrlDecode($payload),true);

        if($payload['exp']<time()){
            return response()->json([
                'success' => FALSE,
                'message' => 'Inavlid Token'
            ],401);
        }

        return $payload;
    }
}
