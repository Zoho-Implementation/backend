<?php

namespace App\Services;

use App\Exceptions\HttpQueryBuilderException;
use App\Helpers\Constants;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;

final class HttpQueryBuilder
{
    private array $headers = [];
    private array $body = [];
    private array $urlQueryParams = [];
    private string $methodName = '';
    private string $url = '';

    public function __construct() {}

    public function setHeaders(array $headers) : void
    {
        $this->headers = $headers;
    }

    public function setBody(array $body) : void
    {
        $this->body = $body;
    }

    public function setUrlQueryParam(array $urlQueryParam) : void
    {
        $this->urlQueryParams = $urlQueryParam;
    }

    public function setMethodName(string $methodName) : void
    {
        $this->methodName = $methodName;
    }

    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }

    /**
     * @throws GuzzleException
     * @throws HttpQueryBuilderException
     */
    public function send(): array
    {
        $client = new Http();
        if (!empty($this->getPreparedHeaders())) {
            $response = $client::withHeaders($this->getPreparedHeaders())->{$this->getPreparedMethodName()}(
                $this->getPreparedUrl(),
                $this->getPreparedBody()
            );

            return [
                'code' => $response->getStatusCode(),
                'body' => json_decode($response->body()),
            ];
        }

        $response = $client::{$this->getPreparedMethodName()}(
            $this->getPreparedUrl(),
            $this->getPreparedBody()
        );
        return [
            'body' => json_decode($response->body()),
            'headers' => $response->headers()
        ];
    }

    private function getPreparedMethodName() : string
    {
        if (!empty($this->methodName)) {
            return $this->methodName;
        }
        return Constants::METHOD_GET;
    }

    private function getPreparedHeaders() : array
    {
        return $this->prepareArray($this->headers);
    }

    private function getPreparedBody(): array
    {
        return $this->prepareArray($this->body);
    }

    private function getPreparedUrlQueryParams() : string
    {
        if (count($this->urlQueryParams) !== 0) {
            $query = '?';
            foreach($this->urlQueryParams as $key => $value) {
                $query .= $key . '=' . $value . '&';
            }
            return $query;
        }
        return '';

    }

    /**
     * @throws HttpQueryBuilderException
     */
    private function getPreparedUrl() : string
    {
        if (!filter_var($this->url, FILTER_VALIDATE_URL) === false) {
            return trim($this->url . $this->getPreparedUrlQueryParams());
        } else {
            throw new HttpQueryBuilderException(
                Constants::SOMETHING_WENT_WRONG_MESSAGE,
                Constants::INTERNAL_SERVER_ERROR
            );
        }
    }

    private function prepareArray(array $array) : array
    {
        if (count($array) !== 0) {
            return $array;
        }
        return [];
    }

}
