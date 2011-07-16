<?php

interface Search_HTTP_CookieJar_Interface{
    public function addOrUpdateCookies(array $cookies, $domain);
    public function getCookies($domain);
}