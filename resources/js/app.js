import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

$(document).ready(function () {

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
    


    var expertise_receive_date = $('.expertise-report-show');

    $('#expert_report_is_in').change(function()  {
        expertise_receive_date.toggleClass('d-none');
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


    // Claims AJAX requests

    // Claim status change
    $('#current-status').on('change', function (e) {

        var claimID = $(this).data('claim-id');
        var newStatus = $(this).val();

        $.post('/api/claims/update-status', { claim_id: claimID, new_status: newStatus } , function(res) {

            sendFlashMessage(res.message, res.type);
            
            if(newStatus != res.status) {
                $('#current-status').val(res.status);
            }

        });

    });

    // Task status change
    $('.js-task-status').on('change', function (e) {

        var taskID = $(this).data('task-id');
        var newStatus = $(this).val();

        $.post('/api/tasks/update-status', { task_id: taskID, new_status: newStatus } , function(res) {

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
    var vehicleID = $('#vehicle_pFlates');
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

    // Bind mailreceiver
    const mailReceiver = $('#mailReceiver');
    bindTags(mailReceiver);

    // Handle template change
    const mailTemplate = $('#mailTemplate');
    const mailBody = $('#mailBody');
    const claimJson = JSON.parse($('#claimJson').text());

    //console.log(claimJson);

    var find = ['[bedrijf]', '[onderwerp]', '[dossiernr]', '[status]', '[datumschade]'];
    var replace = [claimJson.company.name, claimJson.subject, claimJson.claim_number, claimJson.status, claimJson.date_accident];

    var finalBody = mailTemplate.val() ?? '';

    if (finalBody != '') {

        $.each(find, (index, item) => {
            
            finalBody = finalBody.replace(item, replace[index]);

        });

    }

    mailBody.html(finalBody);

    mailTemplate.on('change', function (e) {


        var finalBody = $(this).val();

        $.each(find, (index, item) => {
            
            finalBody = finalBody.replace(item, replace[index]);

        });

        mailBody.html(finalBody);

    });

});

function ajaxCreateCompany( inputID, typeID = null ) {

    if(isAdmin < 1 || !isAdmin || isAdmin != 1){

        return;
        
    }

    inputID.select2({
        tags: true
    });

    inputID.on('select2:select', function (e) {

        var selected = e.params.data;

        if( !selected.element ) {

            $.post('/api/companies/quick-store', { name: selected.text , company_type: typeID  } , function(res) {

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

function ajaxCreateComment( commentableID, commentableType, commentableDOM, body, userID, teamID ) {

    if(!body || body == '' || body == undefined) {

        sendFlashMessage('Vul eerst een opmerking in voordat je deze verzend.', 'alert-warning');

        return;
    }

    const data = { 
        commentableID: commentableID,
        commentableType: commentableType,
        body: body,
        userID: parseInt(userID),
        teamID: parseInt(teamID)
    };

    $.post('/api/comments/quick-store', data)
    .done(res => {

        commentableDOM.find('.item.form textarea[name="body"]').val('');
        //commentableDOM.find('.item.form').slideToggle();

        commentableDOM.find('.item.comment').each((index, comment) => {

            comment.remove();

        });

        if(res.allComments){

            const commentDOM = res.allComments.map(comment => {

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

                            <div class="col-8">
                                ${comment.body}
                            </div>

                        </div>

                    </div>
                `;

            });

            commentableDOM.find('.row:first').after(commentDOM);

            res.allComments.forEach(comment => {

                const userDOM = $('#js-username-' + comment.id);        

                $.post('/api/users/get-user-name', { userID: comment.user_id })
                .done(function(res){
                    userDOM.text(res.name);
                });

            });

        }


        sendFlashMessage(res.message, res.type);

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