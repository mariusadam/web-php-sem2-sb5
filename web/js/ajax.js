function populateForm(formId, entity) {
    for (var property in entity) {
        if (!entity.hasOwnProperty(property)) {
            continue;
        }
        // console.log(property);
        $('#' + formId + '_' + property).val(entity[property]);
    }
}
    function populateTeamsTable(tableId, entities) {
    var $tbody = $('#' + tableId).find('tbody').empty();

    function getRowId(entityId) {
        return 'team-list-row-' + entityId;
    }

    function getButtonId(entityId) {
        return 'team-list-delete-button-' + entityId;
    }

    for (var i = 0; i < entities.length; i++) {
        var entity = entities[i];
        var buttonId = getButtonId(entity.id);
        var rowId = getRowId(entity.id);
        var $tr = $('<tr>').attr('id', rowId);

        $tr.append($('<td>').append(entity.id));
        $tr.append($('<td>').append(entity.name));
        $tr.append($('<td>').append(entity.playedGames));
        $tr.append($('<td>').append(entity.wonGames));
        $tr.append($('<td>').append(entity.lostGames));
        $tr.append($('<td>').append(entity.scoredGoals));
        $tr.append($('<td>').append(entity.score));

        var $deleteBtn = $('<button id="' + buttonId + '" type="button" class="btn btn-danger delete-team">Delete &times;</button>');
        $tr.append($('<td>').append($deleteBtn));

        $tbody.append($tr);

        $deleteBtn.click(function () {
            var teamId = this.id.split('-').last();

            var rowId = getRowId(teamId);

            $('#team_to_delete_it')
                .empty()
                .append(teamId);

            $('#football_team_delete_modal').modal();

            deleteTeamAjax(function (type, msg, deletedId) {
                writeMessage('football_team_list_messages', type, msg);
                $('#' + rowId + ' td').fadeOut(1000, function () {
                    $(this).remove();
                });
            }, teamId);
        });
    }
}

function writeMessage(targetId, type, msg) {
    var $alertDiv = $('<div>')
        .addClass('alert')
        .addClass('alert-' + type)
        .addClass('alert-dismissable')
        .addClass('fade')
        .addClass('in')
        .append(
            $('<a>')
                .attr('href', '#')
                .attr('data-dismiss', 'alert')
                .attr('aria-label', 'close')
                .addClass('close')
                .append('&times;')
        )
        .append(msg);

    $('#' + targetId).append($alertDiv);

    $alertDiv.fadeTo(2000, 500).slideUp(500, function () {
        $alertDiv.slideUp(500);
    });
}

function refreshFadeEfect() {
    $('.alert-dismissable').fadeTo(2000, 500).slideUp(1000, function () {
        $(this).alert('close');
    });
}

if (!Array.prototype.last) {
    Array.prototype.last = function () {
        return this[this.length - 1];
    };
}

function resetForm(formName) {
    $(':input', 'form[name="' + formName + '"]')
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .removeAttr('checked')
        .removeAttr('selected');
}

$(document).ready(function () {
    refreshFadeEfect();
});