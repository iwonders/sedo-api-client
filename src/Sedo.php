<?php

namespace SedoClient;

use DateTime;
use SedoClient\Exceptions\MaxElementsExceeded;
use SedoClient\Exceptions\UnableToOpenFileException;
use SoapClient;

class Sedo
{
    protected $client;

    protected $credentialParams;

    protected $params;

    protected $signKey;

    protected $partnerId;

    protected $username;

    protected $password;

    protected $response;

    protected $method;

    protected $timeout;

    protected $wsdl;

    protected $isLog = false;

    protected $logPath = '';

    /**
     * Sedo constructor.
     * @param $username
     * @param $password
     * @param $signKey
     * @param $partnerId
     * @param int $timeout
     * @param string $wsdl
     * @throws \SoapFault
     */
    public function __construct($username, $password, $signKey, $partnerId, $timeout = 30, $wsdl = 'https://api.sedo.com/api/sedointerface.php?wsdl') {
        $this->username = $username;
        $this->password = $password;
        $this->signKey = $signKey;
        $this->partnerId = $partnerId;
        $this->timeout = $timeout;
        $this->wsdl = $wsdl;

        $this->client = new SoapClient(
            $this->wsdl,
            [
                'exceptions' => false,
                'connection_timeout' => $timeout,
            ]
        );

        $this->credentialParams = [
            'username' => $this->username,
            'password' => $this->password,
            'partnerid' => $this->partnerId,
            'signkey' => $this->signKey
        ];

        $this->params = [];
    }

    /**
     * Call the SOAP request
     * @return $this
     * @throws \SoapFault
     */
    public function call()
    {
        $this->response = $this->client->__soapCall($this->method, ['name' => $this->getRequest()]);

        $this->log();

        if($this->response instanceof \SoapFault) {
            throw $this->response;
        }

        return $this;
    }

    /**
     * @return SoapClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param SoapClient $client
     * @return Sedo
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return Sedo
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignKey()
    {
        return $this->signKey;
    }

    /**
     * @param mixed $signKey
     * @return Sedo
     */
    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPartnerId()
    {
        return $this->partnerId;
    }

    /**
     * @param mixed $partnerId
     * @return Sedo
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return Sedo
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return Sedo
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return array_merge($this->params, $this->credentialParams);
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     * @return Sedo
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getCredentialParams()
    {
        return $this->credentialParams;
    }

    /**
     * @param array $credentialParams
     * @return Sedo
     */
    public function setCredentialParams($credentialParams)
    {
        $this->credentialParams = $credentialParams;
        return $this;
    }

    /**
     * Verify the max element allowed for the data
     * @param $maxElements
     * @param $data
     * @param $key
     * @throws MaxElementsExceeded
     */
    protected function verifyMaxElements($maxElements, $data, $key)
    {
        if (count($data) > $maxElements) {
            throw new MaxElementsExceeded("Max element exceeded, amount of data in $key should not be more than $maxElements");
        }
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return Sedo
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWsdl()
    {
        return $this->wsdl;
    }

    /**
     * @param mixed $wsdl
     * @return Sedo
     */
    public function setWsdl($wsdl)
    {
        $this->wsdl = $wsdl;
        return $this;
    }

    /**
     * Get response as array
     * @return mixed
     */
    public function toArray()
    {
        return json_decode($this->toJson(), true);
    }

    /**
     * Get response as json
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->response);
    }

    /**
     * @return bool
     */
    public function isLog(): bool
    {
        return $this->isLog;
    }

    /**
     * @param bool $isLog
     * @return Sedo
     */
    public function setIsLog(bool $isLog): Sedo
    {
        $this->isLog = $isLog;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogPath(): string
    {
        return $this->logPath;
    }

    /**
     * @param string $logPath
     * @return Sedo
     */
    public function setLogPath(string $logPath): Sedo
    {
        $this->logPath = $logPath;
        return $this;
    }

    /**
     * Log the request and response to log path
     */
    private function log()
    {
        if(!$this->isLog()) return;

        // echo "logPath={$this->logPath} , ";
        // echo "logFileName={$this->getLogFileName()}";

        // check and create dir
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true); // set permission 0777
        }

        $logFilePath = "{$this->logPath}/{$this->getLogFileName()}";

        // check and create the file
        if (!file_exists($logFilePath)) {
            file_put_contents($logFilePath, ''); 
            chmod($logFilePath, 0777); // set permission 0777
        }

        $logFile = fopen($logFilePath, "a");

        if(! $logFile) {
            throw new UnableToOpenFileException("Unable to open log file.");
        }

        fwrite($logFile, $this->getLogContent());
        fclose($logFile); // remember close the log file
    }

    /**
     * Get log content
     * @return string
     */
    private function getLogContent()
    {
        $dt = new DateTime();

        $response = json_decode(json_encode($this->getResponse()), true);

        $data = [
            'wsdl' => $this->wsdl,
            'time' => $dt->format('Y-m-d H:i:s'),
            'method' => $this->method,
            'request' => $this->getRequest(),
            'count' => count($response),
            'response' => $this->getResponse(),
        ];

        if(count($response) > 10){
            // to mush data will make the log file too big, so only show first 10
            $data['response'] = array_slice($response, 0, 10);
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $data['ip'] = trim($ips[0]);
        }elseif(isset($_SERVER['REMOTE_ADDR'])) {
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
        }

        return date('Y-m-d H:i:s') .'sedo log:' . json_encode($data)."\n";
    }

    /**
     * Get log file name
     * @return string
     */
    private function getLogFileName()
    {
        $dt = new DateTime();

        return "{$dt->format('Y-m-d')}.{$this->getLogFileExtension()}";
    }

    /**
     * Get log file extension
     * @return string
     */
    private function getLogFileExtension()
    {
        return 'log';
    }

}
