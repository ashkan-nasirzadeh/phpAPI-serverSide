<?php
namespace PhpAPI;
require_once __DIR__.'/vendor/autoload.php';
use \Firebase\JWT\JWT;

class JwtHandler
{
    protected $issuedAt;
    protected $expire;
    protected $jwt_secrect;
    protected $token;
    protected $jwt;

    public function __construct()
    {
        // set your default time-zone
        date_default_timezone_set('Asia/Tehran');
        $this->issuedAt = time();

        // Token Validity (3600 second = 1hr)
        $this->expire = $this->issuedAt + 3600;

        // Set your secret or signature
        $this->jwt_secrect = "phpAPISECRETkey";
    }
    // ENCODING THE TOKEN
    public function _jwt_encode_data($data)
    {
        $this->token = array(
            "iat" => $this->issuedAt,
            "exp" => $this->expire,
            // Payload
            "data" => $data
        );

        $this->jwt = JWT::encode($this->token, $this->jwt_secrect);
        return $this->jwt;
    }
    //DECODING THE TOKEN
    public function _jwt_decode_data($jwt_token)
    {
        try {
            $decode = JWT::decode($jwt_token, $this->jwt_secrect, array('HS256'));
            return $decode->data;
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new \Exception($e->getMessage());
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            throw new \Exception($e->getMessage());
        } catch (\Firebase\JWT\BeforeValidException $e) {
            throw new \Exception($e->getMessage());
        } catch (\DomainException $e) {
            throw new \Exception($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \Exception($e->getMessage());
        } catch (\UnexpectedValueException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
