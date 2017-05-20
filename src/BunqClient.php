<?php

namespace Bunq;

interface BunqClient
{
    /**
     * @param string $url
     * @param array  $options
     *
     * @return array
     */
    public function get($url, array $options = []);

    /**
     * @param string $url
     * @param array  $options
     *
     * @return array
     */
    public function post($url, array $options = []);

    /**
     * @param string $url
     * @param array  $options
     *
     * @return array
     */
    public function put($url, array $options = []);
}
