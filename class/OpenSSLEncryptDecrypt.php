<?php
/**
 * use openssl to encrypt or decrypt data.
 *
 * @property $method
 * @property $key
 * @property $cipherText
 * @property $originalPlaintext
 *
 * Class OpenSSLEncryptDecrypt
 * @package common\components
 */
class OpenSSLEncryptDecrypt
{
    private $method = 'AES-128-CBC';
    private $key = null;
    private $cipherText = null;
    private $originalPlaintext = null;

    private $iv;
    private $ivLength;
    private static $tagLength = 24;

    /**
     * OpenSSLEncryptDecrypt constructor.
     * @param string $key
     * @throws \Exception
     */
    public function __construct($key = '')
    {
        $this->setKey($key);
    }

    /**
     * encode
     * @return string
     * @throws \Exception
     */
    public function encrypt()
    {
        $this->checkOriginalPlaintext();
        $this->ivLength = openssl_cipher_iv_length($this->method);
        $this->iv = openssl_random_pseudo_bytes($this->ivLength);
        if ($this->checkMethodAEAD()) {
            $pass = openssl_encrypt($this->originalPlaintext, $this->method, $this->key, 1, $this->iv, $tag);
        } else {
            $pass = openssl_encrypt($this->originalPlaintext, $this->method, $this->key, 1, $this->iv);
        }

        $hash = hash_hmac('sha256', $pass, $this->key, true);
        if ($this->checkMethodAEAD()) {
            $this->cipherText = base64_encode($this->iv . $hash . base64_encode($tag) . $pass);
        } else {
            $this->cipherText = base64_encode($this->iv . $hash . $pass);
        }
        return $this->cipherText;
    }

    /**
     * decode
     * @return bool|string
     * @throws \Exception
     */
    public function decrypt()
    {
        $this->checkCipherText();
        $this->cipherText = base64_decode($this->cipherText);
        $this->ivLength = openssl_cipher_iv_length($this->method);
        $this->iv = substr($this->cipherText, 0, $this->ivLength);
        $hash = substr($this->cipherText, $this->ivLength, 32);
        if ($this->checkMethodAEAD()) {
            $tag = base64_decode(substr($this->cipherText, $this->ivLength + 32, self::$tagLength));
            $tagLength = self::$tagLength;
        } else {
            $tagLength = 0;
        }
        $cipherText = substr($this->cipherText, $this->ivLength + 32 + $tagLength);
        $res = hash_hmac('sha256', $cipherText, $this->key, true) == $hash ? true : false;
        if ($res === false)
            return false;
        if ($this->checkMethodAEAD()) {
            $this->originalPlaintext = openssl_decrypt($cipherText, $this->method, $this->key, 1, $this->iv, $tag);
        } else {
            $this->originalPlaintext = openssl_decrypt($cipherText, $this->method, $this->key, 1, $this->iv);
        }
        return $this->originalPlaintext;
    }

    /**
     * Set cipherText.
     * @param string $value
     * @throws \Exception
     */
    public function setCipherText($value)
    {
        if (!is_string($value))
            throw new \Exception('The cipher text must be a string, ' . gettype($value) . ' given.');
        $this->cipherText = $value;
    }

    /**
     * Get the setting cipherText.
     * @return mixed
     */
    public function getCipherText()
    {
        return $this->cipherText;
    }

    /**
     * Set original data.
     * @param string $value
     * @throws \Exception
     */
    public function setOriginalPlaintext($value)
    {
        if (!is_string($value))
            throw new \Exception('The original plaintext must be a string, ' . gettype($value) . ' given.');
        $this->originalPlaintext = $value;
    }

    /**
     * Get the setting data.
     * @return mixed
     */
    public function getOriginalPlaintext()
    {
        return $this->originalPlaintext;
    }

    /**
     * Set key.
     * @param string $value
     * @throws \Exception
     */
    public function setKey($value)
    {
        if (!is_string($value))
            throw new \Exception('The key must be a string, ' . gettype($value) . ' given.');
        $this->key = $value;
    }

    /**
     * Get the setting key.
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set an available cipher method.
     * @param string $value
     * @throws \Exception
     */
    public function setMethod($value)
    {
        if (!in_array($value, openssl_get_cipher_methods()))
            throw new \Exception('The method is not an available cipher methods. See more openssl_get_cipher_methods()');
        $this->method = $value;
    }

    /**
     * Get the setting cipher method.
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * check is set a key.
     * @throws \Exception
     */
    private function checkKey()
    {
        if ($this->key === null)
            throw new \Exception('The key can not be null.');
    }

    /**
     * check is set original plaintext.
     * @throws \Exception
     */
    private function checkOriginalPlaintext()
    {
        if ($this->originalPlaintext === null)
            throw new \Exception('The original plaintext can not be null.');
    }

    /**
     * check is set cipher text.
     * @throws \Exception
     */
    private function checkCipherText()
    {
        if ($this->cipherText === null)
            throw new \Exception('The cipher text can not be null.');
    }

    /**
     * check the method.
     * @throws \Exception
     */
    private function checkMethodAEAD()
    {
        if ($this->method === null)
            throw new \Exception('The method can not be null.');
        if (stripos($this->method, 'ccm') || stripos($this->method, 'gcm'))
            return true;
        return false;
    }

}