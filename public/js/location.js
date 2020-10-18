function LocationSelect(
    countryId,
    regionId,
    cityId,
    countrySelect,
    regionSelect,
    citySelect,
    regionLabel,
    cityLabel,
    regionErrorMessage,
    cityErrorMessage
) {
    this.countryId = countryId;
    this.regionId = regionId;
    this.cityId = cityId;
    this.countrySelect = countrySelect;
    this.regionSelect = regionSelect;
    this.citySelect = citySelect;
    this.regionLabel = regionLabel;
    this.cityLabel = cityLabel;
    this.regionErrorMessage = regionErrorMessage;
    this.cityErrorMessage = cityErrorMessage;

    this.regionSelect.selectpicker({
        dropupAuto: false
    });

    this.countrySelect.selectpicker({
        dropupAuto: false
    });

    this.citySelect.selectpicker({
        dropupAuto: false
    });

    this.countrySelect.on('hidden.bs.select', function (e) {
        console.log('populating regions');
        this.populateRegionsSelect();
    }.bind(this));

    this.regionSelect.on('hidden.bs.select', function (e) {
        console.log('populating cities');
        this.populateCitiesSelect();
    }.bind(this));

    if (this.countryId) {
        this.selectCountry(this.countryId);
    }
}


LocationSelect.prototype.selectCountry = function(countryId) {
    this.countrySelect.selectpicker('val', countryId);
    this.populateRegionsSelect();
};

LocationSelect.prototype.getCountryId = function() {
    return this.countrySelect.val();
};

LocationSelect.prototype.getRegionId = function() {
    return this.regionSelect.val();
}


LocationSelect.prototype.populateRegionsSelect = function() {
    if (this.getCountryId()) {
        $.getJSON("/location/country/" + this.getCountryId() + "/regions", null, function(data) {})
            .done(function (data) {
                this.addRegionOptionsToDropDown(data);
            }.bind(this))
            .fail(function (error) {
                console.log(error);
                var regionText = this.regionLabel.clone().children().remove().end().text();
                this.regionLabel.empty();
                this.regionLabel.append(regionText);
                this.regionLabel.append($('<span class="invalid-feedback d-block"><span class="d-block"><span class="form-error-icon badge badge-danger text-uppercase">Error</span>&nbsp;<span class="form-error-message">' + this.regionErrorMessage + '</span></span></span>'));
            }.bind(this));
    }
};

LocationSelect.prototype.addRegionOptionsToDropDown = function (regions) {
    console.log('adding regions');
    var regionOptions = '';

    $.each(regions, function (index, region) {
        regionOptions += '<option value="' + region.id + '">' + region.name + '</option>';
    });

    this.regionSelect.empty();
    this.regionSelect.append(regionOptions);
    this.regionSelect.selectpicker('refresh');
    this.regionSelect.selectpicker('render');
    this.regionSelect.selectpicker('val', null);

    if (this.regionId) {
        this.selectRegion(this.regionId);
    }
};

LocationSelect.prototype.selectRegion = function(regionId) {
    this.regionSelect.selectpicker('val', regionId);
    this.populateCitiesSelect();
};

LocationSelect.prototype.populateCitiesSelect = function() {
    if (this.getRegionId()) {
        $.getJSON("/location/region/" + this.getRegionId() + "/cities", null, function(data) {})
            .done(function (data) {
                this.addCityOptionsToDropDown(data);
            }.bind(this))
            .fail(function (error) {
                console.log(error);
                var cityText = this.cityLabel.clone().children().remove().end().text();
                this.cityLabel.empty();
                this.cityLabel.append(cityText);
                this.cityLabel.append($('<span class="invalid-feedback d-block"><span class="d-block"><span class="form-error-icon badge badge-danger text-uppercase">Error</span>&nbsp;<span class="form-error-message">' + this.cityErrorMessage + '</span></span></span>'));
            }.bind(this));
    }
};

LocationSelect.prototype.addCityOptionsToDropDown = function(cities) {
    var cityOptions = '';
    $.each(cities, function (index, city) {
        cityOptions += '<option value="' + city.id + '">' + city.name + '</option>';
    });

    this.citySelect.empty();
    this.citySelect.append(cityOptions);
    this.citySelect.selectpicker('refresh');
    this.citySelect.selectpicker('render');

    if (this.cityId) {
        this.selectCity(this.cityId);
    }
};

LocationSelect.prototype.selectCity = function(cityId) {
    this.citySelect.selectpicker('val', cityId);
};