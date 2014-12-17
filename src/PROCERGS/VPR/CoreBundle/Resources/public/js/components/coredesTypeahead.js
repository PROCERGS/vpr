    $(document).ready(function() {
        var coredes = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: $.map(coredesList, function(corede) { return { value: corede.name }; })

        });

        coredes.initialize();

        $('input.corede').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'coredes',
            displayKey: 'value',
            source: coredes.ttAdapter()
        });
    });