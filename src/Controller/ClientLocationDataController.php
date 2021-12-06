<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpClient\HttpClient;
    use Symfony\Component\Cache\Adapter\FilesystemAdapter;
    use App\Controller\IpDataController;

    class ClientLocationDataController extends AbstractController
    {
        /**
         * @Route("/Test/getlocationData")
         */
        public function get_location_data()
        {
            $IpDataController = new IpDataController();
            $city = $IpDataController->get_client_ip_location();
            $cache = new FilesystemAdapter('CityCache', 86400, __DIR__ . '/cache'); # Makes subdirectory named CityCache for 24 hours
            $cacheKey = $city;
            $weather = $cache->get(
                $cacheKey,

                function () # Creates and returns weather data, if not saved in cache
                {
                    $IpDataController = new IpDataController();
                    $city = $IpDataController->get_client_ip_location();
                    $httpClient = HttpClient::create();
                    $response = $httpClient->request('GET', 'https://api.openweathermap.org/data/2.5/weather?q='. $city .'&appid=26e29aa16ee3a3a8af761f4dd0410824');
                    $weather = $response->toArray();
                    return $weather;
                }
            );
            return $weather;
            #$cache->clear(); # Uncomment for clearing cache
        }

        /**
         * @Route("/")
         */
        public function show() # Connects with show.html.twig
        {
            $weather = $this->get_location_data();
            return $this->render('IpData/show.html.twig', [
            'weather' => $weather, #Dump looks better, that's why its here
            ]);
        }
    }


