<?php

    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpClient\HttpClient;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Cache\Adapter\FilesystemAdapter;

    class IpDataController
    {
        /**
         * @Route("/Test/getClientIp")
         */
        public function get_client_ip()
        {
            $request = Request::createFromGlobals();
            $request->getPathInfo();
            $ip = $request->server->get('HTTP_CLIENT_IP')?: $request->server->get('HTTP_CLIENT_IP')?: $request->server->get('REMOTE_ADDR'); # For testing purposes $ip = '54.93.127.49';
            return $ip;

        }

        /**
         * @Route("/Test/getclientIpLocation")
         */
        public function get_client_ip_location()
        {
            $ip = $this->get_client_ip();
            $cache = new FilesystemAdapter('IpCache', 86400, __DIR__ . '/cache'); # Makes subdirectory named IpCache for 24 hours
            $cacheKey = $ip;
            $city = $cache->get(
                $cacheKey,

                function () # Creates and returns city data, if not saved in cache
                {
                    $ip = $this->get_client_ip();
                    $httpClient = HttpClient::create();
                    $response = $httpClient->request('GET', 'http://api.ipstack.com/' . $ip . '?access_key=d3a06fcdb9b3b6e31a164160d2a3047a');
                    $data = $response->toArray();
                    $city = $data['city'];
                    return $city;
                }
            );
            return $city;
            #$cache->clear(); # Uncomment for clearing cache
        }
    }