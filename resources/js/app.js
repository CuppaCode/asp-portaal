import './bootstrap';
import './mychart.js';
import { TempusDominus } from '@eonasdan/tempus-dominus';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

$(document).ready(function () {

    //Setup headers for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var array = document.querySelectorAll('.custom_datepicker');
    
    array.forEach(function (datepicker) {
        new TempusDominus(datepicker, {
            display: {
                icons: {
                  time: 'fa fa-clock',
                  date: 'fa fa-calendar',
                  up: 'fa fa-arrow-up',
                  down: 'fa fa-arrow-down',
                  previous: 'fa fa-chevron-left',
                  next: 'fa fa-chevron-right',
                  today: 'fa fa-calendar-check',
                  clear: 'fa fa-trash',
                  close: 'fa fa-x',
                },
                buttons: {
                  today: true,
                  clear: false,
                  close: true,
                },
                components: {
                    clock: false,
                },
              },
              useCurrent: true,
              localization: {
                locale: 'nl',
                dateFormats: {
                    LTS: 'h:mm:ss T',
                    LT: 'h:mm T',
                    L: 'dd-MM-yyyy',
                    LL: 'MMMM d, yyyy',
                    LLL: 'MMMM d, yyyy h:mm T',
                    LLLL: 'dddd, MMMM d, yyyy h:mm T'
                  },
                  ordinal: (n) => n,
                  format: 'L'
              },
        });
    });


    

    var injury_office = $('.injury-office-show');
    var injury_other = $('.injury-other-show');

    $('#injury').change(function(){ // [create.claims] Showing the injury fields based on the value of the question.
        if($(this).val() == 'yes') {
            injury_office.removeClass('d-none');
            if (!injury_other.hasClass('d-none')) {
             injury_other.addClass('d-none');   
            }
        } else if($(this).val() == 'other') {
            injury_other.toggleClass('d-none');
            if (!injury_office.hasClass('d-none')) {
                injury_office.addClass('d-none');   
            }
        } else {
            if (!injury_office.hasClass('d-none')) {
                injury_office.addClass('d-none');   
            } else if (!injury_other.hasClass('d-none')) {
                injury_other.addClass('d-none');   
            }
        }
    });

    if($('#injury').val() == 'yes') {
        injury_office.removeClass('d-none');
        if (!injury_other.hasClass('d-none')) {
         injury_other.addClass('d-none');   
        }
    } else if($('#injury').val() == 'other') {
        injury_other.toggleClass('d-none');
        if (!injury_office.hasClass('d-none')) {
            injury_office.addClass('d-none');   
        }
    } else {
        if (!injury_office.hasClass('d-none')) {
            injury_office.addClass('d-none');   
        } else if (!injury_other.hasClass('d-none')) {
            injury_other.addClass('d-none');   
        }
    }

    var obstacle = $('.obstacle-show');
    var vehicle = $('.opposite-vehicle-show');
    var other = $('.other-show');

    $('#opposite_type').change(function(){ // [create.claims] Showing the opposite vehcile fields based on the value of the question.

        if($(this).val() == 'obstacle') {
            obstacle.toggleClass('d-none');
            if (!vehicle.hasClass('d-none')) {
                vehicle.addClass('d-none');   
            }
        } else {
            vehicle.removeClass('d-none');
            if (!obstacle.hasClass('d-none')) {
                obstacle.addClass('d-none');   
            }
        }
    });

    if($('#opposite_type').val() == 'obstacle') {
        obstacle.toggleClass('d-none');
        if (!vehicle.hasClass('d-none')) {
            vehicle.addClass('d-none');   
        }
    } else if ($('#opposite_type').val() == 'private') {
        vehicle.toggleClass('d-none');
        if (!obstacle.hasClass('d-none')) {
            obstacle.addClass('d-none');   
        }
    } else if ($('#opposite_type').val() == 'business') {
        vehicle.toggleClass('d-none');
        if (!obstacle.hasClass('d-none')) {
            obstacle.addClass('d-none');   
        }
    } else if ($('#opposite_type').val() == 'unknown') {
        vehicle.toggleClass('d-none');
        if (!obstacle.hasClass('d-none')) {
            obstacle.addClass('d-none');   
        }
    }

    $('#analytics_options').on('change', function(){
        
        if($.inArray('other', $(this).val()) !== -1) {
            other.removeClass('d-none');
        } else if ($.inArray('other', $(this).val()) == -1){
            if(!other.hasClass('d-none')) {
                other.addClass('d-none');
            }
        }
    });


    var expertise_receive_date = $('.expertise-report-show');

    $('#expert_report_is_in').change(function()  {
        if ($(this).is(':checked')) {
            expertise_receive_date.removeClass('d-none');
        } else {
            expertise_receive_date.addClass('d-none');
        }
    });

    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
 

    $('.datatable tbody').on('click', 'tr', function (evt) { // clickable row
    
        if(!$(".btn, .select-checkbox").is(evt.target)){
            var url = $(this).attr('data-entry-url');
            window.location = url;  
        } 
    });

    $('.reset-multiSelect').on('click', function (e) {
        var data = $(this).data('for');

        $('#'+data).val('');
    });

    $('#sla-toggle').on('click', function (e) {
        $('.sla-show').slideToggle();
    });


    // Claims AJAX requests
    var bootstrapButton = $.fn.button.noConflict() // return $.fn.button to previously assigned value
    $.fn.bootstrapBtn = bootstrapButton // give $().bootstrapBtn the Bootstrap functionality
    //launchModal();

    // Claim status change
    $('#current-status').on('change', function (e) {

        var claimID = $(this).data('claim-id');
        var newStatus = $(this).val();

        $.post('/admin/claims/update-status', { claim_id: claimID, new_status: newStatus } , function(res) {

            sendFlashMessage(res.message, res.type);

            if(newStatus == 'claim_denied') {

                launchModal();
            }
            
            if(newStatus != res.status && newStatus != 'claim_denied') {
                $('#current-status').val(res.status);
            }

        });

    });

    // Task status change
    $('.js-task-status').on('change', function (e) {

        var taskID = $(this).data('task-id');
        var newStatus = $(this).val();

        $.post('/admin/tasks/update-status', { task_id: taskID, new_status: newStatus } , function(res) {

            sendFlashMessage(res.message, res.type);
            
            if(newStatus != res.status) {
                $('.js-task-status').val(res.status);
            }

        });

    });

    // Company creation
    var companyID = $('#company_id');
    ajaxCreateCompany(companyID);


    // Injury office creation
    var injuryOfficeID = $('#injury_office_id');
    ajaxCreateCompany(injuryOfficeID, 'injury');

    // Recovery office creation
    var recoveryOfficeID = $('#recovery_office_id');
    ajaxCreateCompany(recoveryOfficeID, 'recovery');

    // Expertise office creation
    var expertiseOfficeID = $('#expertise_office_id');
    ajaxCreateCompany(expertiseOfficeID, 'expertise');

    // Vehicle creation
    var vehicleID = $('#vehicle_plates');
    bindTags( vehicleID );

    // Submitted check
    $('button[type="submit"]').on('click', function() {
        
        window.onbeforeunload = null;

    });

    // On editpage leave
    if(window.location.href.indexOf('edit') !== -1 || window.location.href.indexOf('create') !== -1  && window.location.href.indexOf('claims/create') == -1) {

        window.onbeforeunload = function() {
            return 'Weet je zeker dat je weg wilt gaan?';
        };

    }

    // Select all on SA tools
    const allMigrateSelectsSA = [
        $('#migrateStatusClaimsSA'),
        $('#migrateOppositeTypeClaimsSA'),
        $('#migrateDamagedPartClaimsSA'),
        $('#migrateDamagedPartOppositeClaimsSA'),
        $('#migrateDamageOriginClaimsSA'),
        $('#migrateDamageOriginOppositeClaimsSA'),
        $('#migrateDamagedAreaClaimsSA'),
        $('#migrateDamagedAreaOppositeClaimsSA')
    ];

    allMigrateSelectsSA.forEach( item => {

        addSelectAllToDropdown(item);

    })
    


    // Comments form show
    $('.item .add-comment').on('click', function(e) {

        var form = $(this).closest('.item').find('.item.form');
        var commentID = $(this).data('commentable-id');

        form.slideDown();

        form.find('textarea').focus();

        $('html, body').animate({
            scrollTop: form.find('textarea').offset().top
        }, 1000);

        $('.hide-comment[data-commentable-id=' + commentID + ']').show().css('display', 'flex');
        $(this).hide();
    });

    // Comments form hide
    $('.item .hide-comment').on('click', function(e) {

        var commentID = $(this).data('commentable-id');
        var comment = $('.item[data-commentable-id=' + commentID + ']');
        var form = comment.find('.item.form');

        form.slideUp();

        $('.add-comment[data-commentable-id=' + commentID + ']').show();
        $(this).hide();
        $('.hide-comment[data-commentable-id=' + commentID + ']').hide();

        if(!comment.hasClass('collapsed')){

            comment.addClass('collapsed');
            $(this).find('.js-read-more-text').text('Lees meer...');
            
        }

    });

    // Comments bind event
    $('[data-submit-comment]').on('click', function(e) {
        e.preventDefault();

        var commentableID = $(this).parent().parent().find('input[name="commentable"]').val();
        var commentableType = $(this).parent().parent().find('input[name="commentable_type"]').val();
        var commentableDOM = $(this).closest('.item:not(.form)');

        var body = $(this).parent().parent().find('textarea[name="body"]').val();
        var userID = $(this).parent().parent().find('input[name="user_id"]').val();
        var teamID = $(this).parent().parent().find('input[name="team_id"]').val();

        ajaxCreateComment( commentableID, commentableType, commentableDOM, body, userID, teamID );

    });
    
    $('.recent-activities > .item').each(function(index, item) {

        if ($(this).outerHeight() > 200 && !$(this).hasClass('last-form')) {

            $(this).addClass('collapsed');

        } else {

            $(this).find('.js-read-more').remove();

        }

    });

    $('.recent-activities > .item').each(function () {
        const commentableDOM = $(this);
        updateCommentCount(commentableDOM); // Update the comment count on DOM load
    });

    $('.js-read-more').on('click', function(e) {
        e.preventDefault();

        var item = $(this).parent();

        if (item.hasClass('collapsed')) {

            item.removeClass('collapsed');
            $(this).find('.js-read-more-text').text('Lees minder...');

        } else if (!item.hasClass('collapsed')) {

            item.addClass('collapsed');
            $(this).find('.js-read-more-text').text('Lees meer...');

        }


    });

    // Bind mailreceiver and cc
    const mailBinders = $('#mailReceiver, #mailCc, #mailBcc');

    bindTags(mailBinders);
    

    // Handle template change
    setupMailBody();

    createWysiwyg(document.querySelectorAll('.ckeditor'));

});

// // Use event delegation to handle clicks on dynamically added delete buttons
// $(document).on('click', '.delete-comment-btn', function (event) {
//     event.preventDefault();

//     const button = $(this); // Get the button element
//     const commentID = button.data('comment-id');
//     const commentDOM = button.closest('.item.comment');

//     // Show confirmation prompt
//     if (confirm('Weet je zeker dat je deze opmerking wilt verwijderen?')) {
//         ajaxDeleteComment(commentID, commentDOM);
//     }
// });

function ajaxCreateCompany( inputID, typeID = null ) {

    if(isAdminOrAgent < 1 || !isAdminOrAgent || isAdminOrAgent != 1){

        return;
        
    }

    inputID.select2({
        tags: true
    });

    inputID.on('select2:select', function (e) {

        var selected = e.params.data;

        if( !selected.element ) {

            $.post('/admin/companies/quick-store', { name: selected.text , company_type: typeID  } , function(res) {

                var newOption = inputID.find('option[value="'+ selected.id +'"]');
                var inputName = inputID.attr('name');

                sendFlashMessage(res.message, res.type);
                newOption.attr('value', res.company_id);
                inputID.attr('disabled', 'disabled');

                var newInputID = $(`<input type="hidden" name="${inputName}" value="${inputID.val()}">`);

                inputID.removeAttr('name');
                inputID.after(newInputID); 
    
            });

        }

    });

    return;

}

function ajaxCreateComment(commentableID, commentableType, commentableDOM, body, userID, teamID) {
    if (!body || body === '' || body === undefined) {
        sendFlashMessage('Vul eerst een opmerking in voordat je deze verzend.', 'alert-warning');
        return;
    }

    const data = {
        commentableID: commentableID,
        commentableType: commentableType,
        body: body,
        userID: parseInt(userID),
        teamID: parseInt(teamID),
    };

    $.post('/admin/comments/quick-store', data)
        .done((res) => {
            commentableDOM.find('.item.form textarea[name="body"]').val('');

            commentableDOM.find('.item.comment').each((index, comment) => {
                comment.remove();
            });

            if (res.allComments) {
                const commentDOM = res.allComments.map((comment) => {
                    return `
                        <div class="item comment">
                            <div class="row">
                                <div class="col-2 p-0"></div>
                                <div class="col-2 date-holder text-right">
                                    <div class="icon"><i class="fa fa-commenting-o"></i></div>
                                    <div class="date">
                                        <span id="js-username-${comment.id}"></span>
                                        <br>
                                        <span class="text-info">${comment.created_at}</span>
                                    </div>
                                </div>
                                <div class="col-7">
                                    ${comment.body}
                                </div>
                                <div class="col-1 text-right">
                                    <button class="btn btn-danger btn-sm delete-comment-btn" data-comment-id="${comment.id}">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                commentableDOM.find('.row:first').after(commentDOM);

                res.allComments.forEach((comment) => {
                    const userDOM = $('#js-username-' + comment.id);

                    $.post('/admin/users/get-user-name', { userID: comment.user_id }).done(function (res) {
                        userDOM.text(res.name);
                    });
                });

                // Update the comment count
                updateCommentCount(commentableDOM);
            }

            sendFlashMessage(res.message, res.type);
        });
}

document.addEventListener('click', function (event) {
    // Check if the clicked element is the button or the child icon
    if (event.target.matches('.delete-comment-btn') || event.target.closest('.delete-comment-btn')) {
        event.preventDefault();

        const button = event.target.closest('.delete-comment-btn'); // Get the button element
        const commentID = button.dataset.commentId;
        const commentDOM = $(button).closest('.item.comment');

        if (confirm('Weet je zeker dat je deze opmerking wilt verwijderen?')) {
            ajaxDeleteComment(commentID, commentDOM);
        }
    }
});

function ajaxDeleteComment(commentID, commentDOM) {
    if (!commentID || commentID === undefined) {
        sendFlashMessage('Er is een fout opgetreden. Probeer het opnieuw.', 'alert-warning');
        return;
    }

    $.ajax({
        url: `/admin/comments/${commentID}`,
        type: 'DELETE',
        success: function (res) {
            // Remove the comment from the DOM
            const commentableDOM = commentDOM.closest('.item:not(.form)');
            commentDOM.remove();

            // Update the comment count
            updateCommentCount(commentableDOM);

            // Show success message
            sendFlashMessage(res.message, res.type);
        },
        error: function (err) {
            // Handle errors
            if (err.responseJSON && err.responseJSON.message) {
                sendFlashMessage(err.responseJSON.message, 'alert-danger');
            } else {
                sendFlashMessage('Er is een fout opgetreden bij het verwijderen van de opmerking.', 'alert-danger');
            }
        },
    });
}

function bindTags( inputID ) {

    inputID.select2({
        tags: true
    });

    return;

}

function sendFlashMessage( message, type ) {

    var flashMessage = $('#js-message');

    flashMessage.removeClass( function (index, className) {

        return (className.match(/(^|\s)alert-\S+/g) || []).join(' ');

    });

    if(!type) {

        flashMessage.addClass('alert-success');

    } else {

        flashMessage.addClass(type);

    }

    flashMessage.text(message)
    flashMessage.fadeToggle();

    setTimeout(() => {

        flashMessage.fadeToggle();
        flashMessage.empty();

    }, 10000);

    return;

}

function translationParseHelper(key, value) {
    return " " + value;
}

async function setupMailBody() {

    const nativeMailBody = document.querySelector('#mailBody');

    const mailSubject = $('#mailSubject');

    const mailTemplate = $('#mailTemplate');

    const claimText = $('#claimJson');

    // Check if claimJSON is available
    if(claimText.length < 1) {
        return;
    }

    const claimJson = JSON.parse(claimText.text());

    const statusSelectJson = JSON.parse($('#statusSelectJson').text());
    const damagedPartTranslationsJson = JSON.parse($('#damagePartSelectJson').text());
    const damageAreaSelectJson = JSON.parse($('#damageAreaSelectJson').text());
    const damageOriginJson = JSON.parse($('#damageOriginJson').text());
    const damageKindJson = JSON.parse($('#damageKindJson').text());
    const recoverableClaimJson = JSON.parse($('#recoverableClaimJson').text());

    const allMailTranslations = Object.assign({}, statusSelectJson, damagedPartTranslationsJson, damageAreaSelectJson, damageOriginJson, damageKindJson, recoverableClaimJson);

    var find = [
        '[bedrijf]', 
        '[telnr]', 
        '[onderwerp]', 
        '[dossiernr]', 
        '[status]', 
        '[datumschade]', 
        '[kenteken]', 
        '[schade_aard]', 
        '[schade_plaats]', 
        '[schade_oorzaak]', 
        '[schade_bedrag]', 
        '[kenteken_wederpartij]', 
        '[verhaalbaar]', 
        '[schade_soort]'
    ];
    var replace = [
        claimJson.company ? claimJson.company.name : 'N/A', 
        claimJson.company ? '<a href="tel:'+ claimJson.company.phone +'" target="_blank">' + claimJson.company.phone + '</a>' : 'N/A', 
        claimJson.subject, 
        claimJson.claim_number, 
        claimJson.status, 
        claimJson.date_accident, 
        claimJson.vehicle ? claimJson.vehicle.plates : 'N/A',
        JSON.parse(claimJson.damaged_part, translationParseHelper), 
        JSON.parse(claimJson.damaged_area, translationParseHelper), 
        JSON.parse(claimJson.damage_origin, translationParseHelper), 
        claimJson.damage_costs, 
        claimJson.vehicle_opposite ? claimJson.vehicle_opposite.plates : 'N/A', 
        claimJson.recoverable_claim, 
        claimJson.damage_kind
    ];

    const contactText = $('#contactJson');

    if(contactText.length > 0) {
        
        const contactJson = JSON.parse(contactText.text());

        var find = find.concat([
            '[contact_naam]', 
            '[contact_email]'
        ]);

        var replace = replace.concat([
            contactJson.first_name + ' ' + contactJson.last_name, 
            '<a href="mailto:'+ contactJson.email +'" target="_blank">' + contactJson.email + "</a>"
        ]);

    }

    const recoveryText = $('#recoveryJson');

    if(recoveryText.length > 0) {
        
        const recoveryJson = JSON.parse(recoveryText.text());

        var find = find.concat([
            '[herstel_adres]', 
            '[herstel_postcode]', 
            '[herstel_telnr]'
        ]);
        var replace = replace.concat([
            recoveryJson.street + ' ' + recoveryJson.city, 
            recoveryJson.zipcode, 
            '<a href="tel:' + recoveryJson.phone + '" target="_blank">' + recoveryJson.phone + '</a>'
        ]);

        const recoveryContactText = $('#recoveryContactJson');

        if (recoveryContactText.length > 0) {

            const recoveryContactJson = JSON.parse(recoveryContactText.text());
            
            var find = find.concat([
                '[herstel_contact_naam]', 
                '[herstel_email]'
            ]);

            var replace = replace.concat([
                recoveryContactJson[0].first_name + ' ' + recoveryContactJson[0].last_name, 
                '<a href="mailto:' + recoveryContactJson[0].email + '" target="_blank">' + recoveryContactJson[0].email + '</a>'
            ]);
        }

    }

    const driverText = $('#driverJson');

    if(driverText.length > 0) {

        const driverJson = JSON.parse(driverText.text()); 

        var find = find.concat([
            '[chauffeur_naam]', 
            '[chauffeur_email]'
        ]);

        var replace = replace.concat([
            driverJson.first_name + ' ' + driverJson.last_name, 
            driverJson.email
        ]);

    }

    const oppositeText = $('#oppositeJson');

    if(oppositeText.length > 0) {

        const oppositeJson = JSON.parse(oppositeText.text());

        var find = find.concat([
            '[wederpartij_naam]', 
            '[wederpartij_adres]', 
            '[wederpartij_postcode_stad]', 
            '[wederpartij_telnr]', 
            '[wederpartij_email]',
            '[wederpartij_schade_aard]', 
            '[wederpartij_schade_plaats]', 
            '[wederpartij_schade_oorzaak]',
        ]);

        var replace = replace.concat([
            oppositeJson.name, 
            oppositeJson.street, 
            oppositeJson.zipcode + ' ' + oppositeJson.city, 
            oppositeJson.phone, 
            oppositeJson.email,
            oppositeJson.damaged_part ? JSON.parse(oppositeJson.damaged_part, translationParseHelper) : 'N/A', 
            oppositeJson.damaged_area ? JSON.parse(oppositeJson.damaged_area, translationParseHelper) : 'N/A', 
            oppositeJson.damage_origin ? JSON.parse(oppositeJson.damage_origin, translationParseHelper) : 'N/A' 
        ]);

    }

    var finalBody = mailTemplate.val() ?? '';
    var finalSubject = $('option:selected', mailTemplate).data('subject') ?? '';

    if (finalBody != '') {
        $.each(find, (index, item) => {
            finalBody = finalBody.replaceAll(item, replace[index]);
        });
        Object.keys(allMailTranslations)
            .filter(key => key.startsWith('[') && key.endsWith(']'))
            .forEach(key => {
                finalBody = finalBody.replaceAll(key, allMailTranslations[key]);
            });
    }

    if (finalSubject != '') {
        $.each(find, (index, item) => {
            finalSubject = finalSubject.replaceAll(item, replace[index]);
        });
        Object.keys(allMailTranslations)
            .filter(key => key.startsWith('[') && key.endsWith(']'))
            .forEach(key => {
                finalSubject = finalSubject.replaceAll(key, allMailTranslations[key]);
            });
    }

    const ckeditor = await ClassicEditor.create(nativeMailBody);
    ckeditor.setData(finalBody);
    mailSubject.val(finalSubject);

    mailTemplate.on('change', function (e) {
        var finalSubject = $('option:selected', this).data('subject') ?? '';
        var finalBody = $(this).val() ?? '';
        $.each(find, (index, item) => {
            finalBody = finalBody.replaceAll(item, replace[index]);
            finalSubject = finalSubject.replaceAll(item, replace[index]);
        });
        Object.keys(allMailTranslations)
            .filter(key => key.startsWith('[') && key.endsWith(']'))
            .forEach(key => {
                finalBody = finalBody.replaceAll(key, allMailTranslations[key]);
                finalSubject = finalSubject.replaceAll(key, allMailTranslations[key]);
            });
        ckeditor.setData(finalBody);
        mailSubject.val(finalSubject);
    });

   
    
}

async function createWysiwyg( textareaCollection ){

    if(textareaCollection.length < 1){
        return;
    }

    textareaCollection.forEach(async (item) => {

        const wysiwyg = ClassicEditor.create(item);

        // we could manipulate wysiwyg from here using methods within the wysiwyg const

    });

}

function launchModal( 
    modalTitle = 'Claim afwijzing',
    modalBody = 'Om een claim af te wijzen dient u een reden te geven, selecteer de juiste.'
){

    const rawModal = document.getElementById('exampleModal');
    const myModal = new coreui.Modal(rawModal, {
        keyboard: false
    });

    const DOMModalTitle = $(rawModal).find('[data-modal-title]');
    const DOMModalBody = $(rawModal).find('[data-modal-body]');

    const claimID = $(rawModal).find('input[name="claim_id"]').val();

    DOMModalTitle.text(modalTitle);
    DOMModalBody.html(modalBody);

    myModal.show();

    $('[data-modal-save]').on('click', function (e) {

        const declineReason = $(rawModal).find('#decline_reason').val();

        $.post('/admin/claims/decline-claim', { declineReason: declineReason, claimID: claimID })
        .done(function(res){

            sendFlashMessage(res.message, res.type);

            myModal.hide();
            
        });

    });
}

function addSelectAllToDropdown( inputID )
{
    const addSelectAll = matches => {
        if (matches.length > 0) {
          // Insert a special "Select all matches" item at the start of the 
          // list of matched items.
          return [
            {id: 'selectAll', text: 'Select All', matchIds: matches.map(match => match.id)},
            ...matches
          ];
        }
      };
    
    const handleSelection = event => {
        if (event.params.data.id === 'selectAll') {
            inputID.val(event.params.data.matchIds);
            inputID.trigger('change');
        };
    };
    
    inputID.select2({
        multiple: true,
        sorter: addSelectAll
    });

    inputID.on('select2:select', handleSelection);

}

function updateCommentCount(commentableDOM) {
    const commentCount = commentableDOM.find('.item.comment').length;
    const commentTotalElement = commentableDOM.find('.comment-total');
    const readMoreElement = commentableDOM.find('.js-read-more');
    const readMoreTextElement = readMoreElement.find('.js-read-more-text');

    if (commentTotalElement.length > 0) {
        if (commentCount > 0) {
            commentTotalElement.text(`(${commentCount})`).show();
            readMoreElement.show();
        } else {
            commentTotalElement.text('').hide();
            readMoreElement.show(); // Still show Lees meer, just without (0)
        }
    }

    // Adjust the read more/less label
    if (readMoreTextElement.length > 0) {
        if (commentableDOM.hasClass('collapsed')) {
            readMoreTextElement.text('Lees meer...');
        } else {
            readMoreTextElement.text('Lees minder...');
        }
    }
}

