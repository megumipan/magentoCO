var PostToAddr = function(button, buttonLabel, street, region, city, country, zip, zipSplitted) {
  this.zipidArray = new Array();
  this.posttoaddrId = button;
  this.buttonLabel = buttonLabel;
  this.streetId = street;
  this.regionId = region;
  this.cityId = city;
  this.countryId = country;
  this.zipId = zip;
  this.zipSplitted = zipSplitted;
  this.setZipidArray(zip, zipSplitted);
  this.messages = {};
  var country = document.getElementById(this.countryId);
  if (isSet(country)) {
    if (country.tagName.toLowerCase() == 'input' && country.value.toUpperCase() == 'JP') {
      this.showButton();
    } else if (country.tagName.toLowerCase() == 'select') {
      var __this__ = this;
      Event.observe(country, 'change', function(){if (country.options[country.selectedIndex].value == 'JP'){__this__.showButton();}else{__this__.hideButton();}});
      if (country.options[country.selectedIndex].value == 'JP'){
        this.showButton();
      }
    }
  }
}

PostToAddr.prototype = {
  
  setZipidArray: function(zip, splitted) {
    if (!splitted) {
      this.zipidArray[0] = zip;
    } else {
      this.zipidArray[0] = zip + '1';
      this.zipidArray[1] = zip + '2';
    }
  },

  getAddress: function(option) {
    var url = "/magento/postcodetoaddress/postcodetoaddress/get";
    var postcode = '';
    var __this__ = this;
    for(var i=0;i<this.zipidArray.length;++i) {
      postcode += document.getElementById(this.zipidArray[i]).value;
    }
    if (postcode.length < option['size']) {
      if (option['showError']) {
        this.showError(option['sizeErrorMessage']);
      }
      return false;
    }
    var req = new Ajax.Request(
      url, 
      {
        'method': 'post',
        'parameters': 'zip=' + postcode + '&country=' + this.getCountry(),
        onSuccess: function(request) {
          var addresses;
          eval("addresses=" + request.responseText);
          if (!__this__.isError(addresses)) {
            __this__.setAddress(addresses);
            var street = document.getElementById(__this__.streetId);
            street.focus();
            street.setSelectionRange(street.value.length, street.value.length);
          } else {
            __this__.showError(addresses['error']);
          }
        },
        onFailure: function(request) {
          __this__.showError(__this__.messages['system_error']);
        }/*,
        onException: function(request) {
          showError('exception occur');
        }*/
      }
    );
  },
  
  setAddress: function(addresses) {
    var prefs = document.getElementById(this.regionId).options;
    for (var i=0; i<prefs.length; ++i) {
      if (prefs[i].innerHTML == addresses['pref']) {
        document.getElementById(this.regionId).selectedIndex = i;
        break;
      }
    }
  //  document.getElementById(region).value = addresses['pref'];
    document.getElementById(this.cityId).value = addresses['city'];
    document.getElementById(this.streetId).value = addresses['street'];
  },

  getCountry: function() {
    var country = document.getElementById(this.countryId);
    var country_tagName = country.tagName.toLowerCase();
    if (country_tagName == 'input') {
      return country.value;
    } else if (country_tagName == 'select') {
      return country.options[country.selectedIndex].value;
    } else {
      return '';
    }
  },

  isError: function(address) {
    return isSet(address['error']);
  },

  showButton: function() {
    if (isSet(document.getElementById(this.posttoaddrId))) {
      document.getElementById(this.posttoaddrId).show();
    } else {
      var button = document.createElement('button');
      var insertZipid = this.zipSplitted ? this.zipidArray[1] : this.zipidArray[0];
      var zipcode = Array();
      var __this__ = this;
      button.id = this.posttoaddrId;
      button.innerHTML = this.buttonLabel;
      button.type = "button";
      button.addEventListener('click', function() {execMethod(__this__);return false;}, false);
      var zip = document.getElementById(insertZipid);
      zip.parentNode.insertBefore(button, zip.nextSibling);
    }
  },

  hideButton: function() {
    var button = document.getElementById(this.posttoaddrId);
    if (isSet(button)) {
      button.hide();
    }
  },

  showError: function(message) {
    alert(message);
  },

  setMessages: function(messages) {
    this.messages = messages;
  }
}

function isSet(data) {
  return (typeof (data) !== 'undefined') && data !== null;
}

function execMethod(obj) {
  obj.getAddress({size:errorSize, showError:true, sizeErrorMessage:sizeErrorMessage});
}
