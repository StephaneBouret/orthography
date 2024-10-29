"use strict";
  
// This loads helper components from the Extended Component Library,
// https://github.com/googlemaps/extended-component-library.
// Please note unpkg.com is unaffiliated with Google Maps Platform.
import {APILoader} from 'https://unpkg.com/@googlemaps/extended-component-library@0.6';

const CONFIGURATION = {
  "ctaTitle": "Checkout",
  "mapOptions": {"center":{"lat":37.4221,"lng":-122.0841},"fullscreenControl":true,"mapTypeControl":false,"streetViewControl":true,"zoom":11,"zoomControl":true,"maxZoom":22,"mapId":""},
  "mapsApiKey": "AIzaSyCsWtuAIV6GBnjT7M_199g-xY7v0wUzV-I",
  "capabilities": {"addressAutocompleteControl":true,"mapDisplayControl":false,"ctaControl":true}
};

const SHORT_NAME_ADDRESS_COMPONENT_TYPES =
    new Set(['street_number', 'postal_code']);

const ADDRESS_COMPONENT_TYPES_IN_FORM = [
  'adress',
  'city',
  'postalCode',
];

function getFormInputElement(componentType) {
  return document.getElementById(`registration_form_${componentType}`);
}

function fillInAddress(place) {
    // console.log(place.address_components);  // Debug: Affiche les composants d'adresse
    
    function getComponentName(componentType) {
      for (const component of place.address_components || []) {
        if (component.types.includes(componentType)) {
          return SHORT_NAME_ADDRESS_COMPONENT_TYPES.has(componentType) ?
            component.short_name :
            component.long_name;
        }
      }
      return '';
    }
  
    function getComponentText(componentType) {
      if (componentType === 'adress') {
        return `${getComponentName('street_number')} ${getComponentName('route')}`;
      } else if (componentType === 'city') {
        return getComponentName('locality') || getComponentName('administrative_area_level_1');
      } else if (componentType === 'postalCode') {
        return getComponentName('postal_code');  // Capture directe du code postal
      }
      return '';
    }
  
    for (const componentType of ADDRESS_COMPONENT_TYPES_IN_FORM) {
      const inputElement = getFormInputElement(componentType);
      if (inputElement) {
        inputElement.value = getComponentText(componentType);
      }
    }
  }
  

async function initMap() {
  const {Autocomplete} = await APILoader.importLibrary('places');

  const autocomplete = new Autocomplete(getFormInputElement('adress'), {
    fields: ['address_components', 'geometry', 'name'],
    types: ['address'],
  });

  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    if (!place.geometry) {
      // User entered the name of a Place that was not suggested and
      // pressed the Enter key, or the Place Details request failed.
      window.alert(`No details available for input: '${place.name}'`);
      return;
    }
    fillInAddress(place);

    document.getElementById('additional-fields').style.display = 'block';
  });
}

initMap();