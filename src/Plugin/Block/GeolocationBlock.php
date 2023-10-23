<?php

namespace Drupal\miafemtech_geolocation\Plugin\Block;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\miafemtech_geolocation\Geolocation;

/**
 * Provides a 'Geolocation' Block.
 *
 * @Block(
 *   id = "geolocation_block",
 *   admin_label = @Translation("Geolocation block"),
 *   category = @Translation("Geolocation Block"),
 * )
 */
class GeolocationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The ConfigFactory manager.
   *
   * @var Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The custom service.
   *
   * @var \Drupal\miafemtech_geolocation\Geolocation
   */
  protected $geolocation;

  /**
   * Constructs a contruct the object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param Drupal\Core\Config\ConfigFactory $config_factory
   *   The ConfigFactory manager.
   * @param Drupal\miafemtech_geolocation\Geolocation $geolocation
   *   The Geolocation service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactory $config_factory,
    Geolocation $geolocation
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->configFactory = $config_factory;
    $this->geolocation = $geolocation;
  }

  /**
   * Constructs a ContainerFactoryPluginInterface container.
   *
   * @param Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container interface.
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin_id.
   * @param mixed $plugin_definition
   *   The plugin_definition.
   *
   * @return static
   */
  public static function create(
    ContainerInterface $container,
    $configuration,
    $plugin_id,
    $plugin_definition,
  ) {

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('geolocation'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $cookie = $_COOKIE['country'] ?? NULL;
    $country = $cookie ? $cookie : $this->geolocation->getCountry();
    $config = $this->configFactory->get('miafemtech_geolocation.geolocation_form');
    $geolocation_text = $config->get('geolocation_block_text');
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    return [
      '#theme' => 'geolocation',
      '#geolocation_text' => $geolocation_text,
      '#country' => $country,
      '#attached' => [
        'drupalSettings' => [
          'geolocation' => [
            'language' => $language,
            'country' => $country,
          ],
        ],
      ],
    ];
  }

}
