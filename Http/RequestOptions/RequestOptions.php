<?php

namespace Modules\SouthAPI\Http\RequestOptions;

abstract class RequestOptions
{
    protected array $options;

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setBody(array|object $data): static
    {
        $this->options[\GuzzleHttp\RequestOptions::BODY] = json_encode($data);

        return $this;
    }

    public function setHeader(string $key, string $value): static
    {
        $this->options[\GuzzleHttp\RequestOptions::HEADERS][$key] = $value;

        return $this;
    }

    public function setBearerToken(string $value): static
    {
        $this->setHeader('Authorization', 'Bearer '.$value);

        return $this;
    }
}
