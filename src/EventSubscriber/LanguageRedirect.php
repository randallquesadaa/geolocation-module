<?php

namespace Drupal\geolocation_module\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\geolocation_module\Geolocation;

/**
* Event subscriber para redireccionar antes de cargar la página.
*/
class LanguageRedirect implements EventSubscriberInterface {

  /**
   * The geolocation service.
   *
   * @var \Drupal\geolocation_module\Geolocation
   */
  protected $geolocation;

  /**
   * Constructs a contruct the object.
   *
   * @param Drupal\geolocation_module\Geolocation $geolocation
   *   The Geolocation service.
   */
  public function __construct(Geolocation $geolocation) {
    $this->geolocation = $geolocation;
  }
  /**
  * {@inheritdoc}
  */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['redirect'];
    return $events;
  }

  /**
  * Redirect to your language
  */
  public function redirect(RequestEvent $event) {
      if (\Drupal::service('path.matcher')->isFrontPage()) {
        $cookie = $_COOKIE['country'] ?? NULL;
        $your_country = $cookie ? $cookie : $this->geolocation->getCountry()['short_name'];
        $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $url_object = Url::fromUri('base:/', array('absolute' => TRUE));
        $url_absoluta = $url_object->toString();
        $current_url = $event->getRequest()->getRequestUri();

        $countries = [
          'en' => [
            'cr',
            'usa',
          ],
          'ja' => [
            'jp',
          ],
        ];

        foreach ($countries as $key => $country_list) {
          if ($current_language != $key && in_array(strtolower($your_country), $country_list)) {
            if ($key == 'en') {
              $key = '/';
            }
            if ($url_absoluta . $current_url != $url_absoluta . $key) {
              $response = new RedirectResponse($url_absoluta . $key);
              $response->send();
              break;
            }
          }
        }
      }
  }
}
