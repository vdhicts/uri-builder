<?php

namespace Vdhicts\UriBuilder;

use Stringable;

class BasicAuthentication implements Stringable
{
    public function __construct(
        public string $username,
        public string $password,
    ) {
    }

    public function toString(): string
    {
        return sprintf('%s:%s', $this->username, $this->password);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
