$(document).ready(function() {
    $('#incidents-report-modal').on('show.bs.modal', function(ev) {
        var id = $(ev.relatedTarget).data('id');

        document.getElementById('incidents-report-form').reset();

        $('#incidents-report-form-resolve').toggleClass('hidden', !id);

        if (id) {
            $.ajax('/admin/safety/ajax_get_incident/'+id).done(function(data) {
                if (data.success) {
                    var incident = data.data;
                    $('#incidents-report-form-id').val(incident.id);
                    $('#incidents-report-form-title').val(incident.title);
                    $('#incidents-report-form-status').val(incident.status || 'Pending');
                    $('#incidents-report-form-location_id').val(incident.location_id).trigger('change');
                    $('#incidents-report-form-date').val(incident.datetime.split(' ')[0]).trigger('change');
                    $('#incidents-report-form-time').val(incident.datetime.split(' ')[1].substr(0, 5));
                    $('#incidents-report-form-severity').val(incident.severity);
                    $('#incidents-report-form-description').val(incident.description);
                    $('#incidents-report-form-action_taken').val(incident.action_taken);
                    $('#incidents-report-form-notes').val(incident.notes);
                    $('#incidents-report-form-resolve').toggleClass('hidden', incident.status == 'Resolved');

                    $('#incidents-report-form-injured_people-wrapper').fill_people_section('injured_people', incident.injured_people);
                    $('#incidents-report-form-witnesses-wrapper').fill_people_section('witnesses', incident.witnesses);

                    $('#incidents-report-form-reporter-id').val(incident.reporter_id);
                    $('#incidents-report-form-reporter-first_name').val(incident.reporter.first_name);
                    $('#incidents-report-form-reporter-last_name').val(incident.reporter.last_name);
                    $('#incidents-report-form-reporter-email').val(incident.reporter.email);
                    $('#incidents-report-form-reporter-mobile').val(incident.reporter.mobile);

                    // If the type select is used
                    $('#incidents-report-form-reporter_id').val(incident.reporter_id);
                    $('#incidents-report-form-reporter_id-display').val(
                        incident.reporter_id ? [incident.reporter.first_name, incident.reporter.last_name, '-', incident.reporter.email].join(' ') : '')
                }
            });
        }
    });

    $.fn.fill_people_section = function(type, people) {
        // Clear existing people rows and add a row for each of the loaded ones.
        var $wrapper = $(this);
        var $template = $wrapper.find('.incident-person').first().clone();
        var $clone;
        $wrapper.html('');

        for (var i = 0; i < people.length; i++) {
            $clone = $template.clone();
            $clone.find('[name*="first_name"]').attr('name', type+'['+i+'][first_name]').val(people[i].first_name);
            $clone.find('[name*="last_name"]' ).attr('name', type+'['+i+'][last_name]' ).val(people[i].last_name);
            $wrapper.append($clone);
        }

        // Ensure there is at least one
        if (people.length == 0) {
            $wrapper.append($template);
        }
    };

    var $incident_form = $('#incidents-report-form');

    $incident_form.on('click', '.incident-person-add', function() {
        var $row   = $(this).parents('.incident-person');
        var type   = $(this).data('type');
        var $clone = $row.clone();

        $clone.find(':input').val('');

        $clone.find('[name*="'+type+'"]').each(function(i, element) {
            var number = element.name.match(/\[([0-9]*)\]/)[1];
            element.name = element.name.replace('['+number+']', '['+(parseInt(number) + 1)+']');
        });

        $row.after($clone);
    });

    $incident_form.on('click', '.incident-person-remove', function() {
        var $row = $(this).parents('.incident-person');

        if (!$row.is(':only-child')) {
            $row.remove();
        }
    });

    $incident_form.on(':ib-typeselect-select', '#incidents-report-form-reporter_id', function(ev, data) {

    });
});