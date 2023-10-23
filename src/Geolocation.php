<?php

namespace Drupal\miafemtech_geolocation;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client;

class Geolocation {

  /**
   * The ConfigFactory manager.
   *
   * @var Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs a contruct the object.
   *
   * @param Drupal\Core\Config\ConfigFactory $config_factory
   *   The ConfigFactory manager.
   */
  public function __construct(ConfigFactory $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getCoordinates() {
    $client = new Client();
    $config = $this->configFactory->get('miafemtech_geolocation.geolocation_form');
    $api_key = $config->get('google_cloud_api_key');
    $url = "https://www.googleapis.com/geolocation/v1/geolocate?key={$api_key}";

    $postData = [
      'considerIp' => 'true',
    ];

    $response = $client->post($url, [
      'json' => $postData,
    ]);

    $data = json_decode($response->getBody(), true);
    if ($data['location']['lat'] && $data['location']['lng']) {
      return $data;
    }
  }

  /**
   * {@inheritdoc}
   */
  function getCountry() {
    $config = $this->configFactory->get('miafemtech_geolocation.geolocation_form');
    $coordinates = $this->getCoordinates();
    if ($coordinates) {
      $lat = $coordinates['location']['lat'];
      $lng = $coordinates['location']['lng'];

      $api_key = $config->get('google_cloud_api_key');
      $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$api_key}";

      $client = new Client();
      $response = $client->get($url);

      $data = json_decode($response->getBody(), true);

      if (isset($data['results']) && !empty($data['results'])) {
        foreach ($data['results'] as $result) {
          foreach ($result['address_components'] as $component) {
            if (in_array('country', $component['types'])) {
              $geolocation = [
                'long_name' => $component['long_name'],
                'short_name' => $component['short_name'],
              ];
              return $geolocation;
            }
          }
        }
      }

      return 'Desconocido';
    }
  }
}
