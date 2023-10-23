Drupal.behaviors.geolocation = {
  attach() {
    function hiddenGeolocationBlock(country) {
    let next = true;
    Object.keys(countries).forEach(key => {
      if (next == true) {
        if (currentLanguage == key) {
          geolocationBlock.classList.add('hidden');
        }
        else if (countries[key].includes(country)) {
          geolocationBlock.classList.remove('hidden');
          next = false;
        }
      }
    });
    }

    const cookie = document.cookie.replace(/(?:(?:^|.*;\s*)country\s*\=\s*([^;]*).*$)|^.*$/, "$1");
    const countryShortName = drupalSettings.geolocation.country['short_name'];
    const currentLanguage = drupalSettings.geolocation.language;
    const baseUrl = `https://${window.location.host}`;
    const geolocationBlock = document.querySelector('#block-geolocationblock');
    const geolocationLink = geolocationBlock.querySelector('.link');
    const country = cookie ? cookie : countryShortName;
    const countries = {
      'en': [
        'CR',
        'USA'
      ],
      'ja': [
        'JP',
      ],
    };

    hiddenGeolocationBlock(country);

    geolocationLink.addEventListener("click", function () {
      document.cookie = `country=${countryShortName}`;
    });

    switch (country) {
      case 'jp':
        geolocationLink.href = `${baseUrl}/ja`;
        break;

      default:
        geolocationLink.href = baseUrl;
        break;
    }
  },
};
