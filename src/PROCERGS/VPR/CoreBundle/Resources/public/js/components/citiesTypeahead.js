    $(document).ready(function() {
        var cities = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: $.map(citiesList, function(city) { return { value: city.name }; })

        });

        cities.initialize();

        $('input.city').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'cities',
            displayKey: 'value',
            source: cities.ttAdapter()
        });
    });