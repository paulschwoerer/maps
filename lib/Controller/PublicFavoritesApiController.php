<?php

namespace OCA\Maps\Controller;

use OCP\IConfig;
use OCP\IRequest;
use OCP\ISession;
use OCA\Maps\Service\FavoritesService;
use OCP\AppFramework\PublicShareController;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\Template\PublicTemplateResponse;
use Punic\Data;

class PublicFavoritesApiController extends PublicShareController {
    private $favoritesService;
    private $config;
    private $qb;

    public function __construct($appName, IRequest $request, ISession $session,  IConfig $config, FavoritesService $favoritesService) {
        parent::__construct($appName, $request, $session);

        $this->config = $config;
        $this->favoritesService = $favoritesService;

        $this->qb = \OC::$server->getDatabaseConnection()->getQueryBuilder();
    }

    public function getPasswordHash(): string {
        return '';
    }

    protected function isPasswordProtected(): bool {
        return false;
    }

    public function isValidToken(): bool {
        return $this->favoritesService->isValidToken($this->getToken());
    }

    /**
     * @param $token
     * @return DataResponse
     *
     * @PublicPage
     * @Cors
     */
    public function getFavorites($token) {
        if ($token === '') {
            return new DataResponse('Invalid token', Http::STATUS_BAD_REQUEST);
        }

        $favorites = $this->favoritesService->getFavoritesByShareToken($token);

        if ($favorites === false) {
            return new DataResponse('Not found', Http::STATUS_NOT_FOUND);
        }

        return new DataResponse($favorites);
    }

    public function addFavorite($lat, $lng, $name) {
        return new DataResponse($lat . ' ' . $lng . ' ' . $name);
    }

    public function editFavorite($id) {

    }

    public function deleteFavorite($id) {

    }

    private function addCsp($response) {
        if (class_exists('OCP\AppFramework\Http\ContentSecurityPolicy')) {
            $csp = new \OCP\AppFramework\Http\ContentSecurityPolicy();
            // map tiles
            $csp->addAllowedImageDomain('https://*.tile.openstreetmap.org');
            $csp->addAllowedImageDomain('https://server.arcgisonline.com');
            $csp->addAllowedImageDomain('https://*.cartocdn.com');
            $csp->addAllowedImageDomain('https://*.opentopomap.org');
            $csp->addAllowedImageDomain('https://*.cartocdn.com');
            $csp->addAllowedImageDomain('https://*.ssl.fastly.net');
            $csp->addAllowedImageDomain('https://*.openstreetmap.se');

            // default routing engine
            $csp->addAllowedConnectDomain('https://*.project-osrm.org');
            $csp->addAllowedConnectDomain('https://api.mapbox.com');
            $csp->addAllowedConnectDomain('https://graphhopper.com');
            // allow connections to custom routing engines
            $urlKeys = [
                'osrmBikeURL',
                'osrmCarURL',
                'osrmFootURL',
                'graphhopperURL'
            ];
            foreach ($urlKeys as $key) {
                $url = $this->config->getAppValue('maps', $key);
                if ($url !== '') {
                    $scheme = parse_url($url, PHP_URL_SCHEME);
                    $host = parse_url($url, PHP_URL_HOST);
                    $port = parse_url($url, PHP_URL_PORT);
                    $cleanUrl = $scheme . '://' . $host;
                    if ($port && $port !== '') {
                        $cleanUrl .= ':' . $port;
                    }
                    $csp->addAllowedConnectDomain($cleanUrl);
                }
            }
            //$csp->addAllowedConnectDomain('http://192.168.0.66:5000');

            // poi images
            $csp->addAllowedImageDomain('https://nominatim.openstreetmap.org');
            // search and geocoder
            $csp->addAllowedConnectDomain('https://nominatim.openstreetmap.org');
            $response->setContentSecurityPolicy($csp);
        }
    }
}