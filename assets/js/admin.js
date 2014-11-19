jQuery(document).ready(function() {
    jQuery('#tv-removeall').click(function(e) {
        e.preventDefault();
        tv_obliterate();
    });
});

function tv_obliterate() {
    jQuery('#tv-removeall').attr('disabled','disabled');
    jQuery('#tv-removeall').text('Deleting all content. This may take a while...');
    jQuery('.tv-posts, .tv-terms').show();
    tv_remove_posts();  
}

function tv_remove_posts() {
    jQuery.ajax({
        url: home_url + '/wp-admin/admin-ajax.php',
        data: {'action': 'remove_posts'},
        dataType: 'json',
        type: 'GET',
        success: function(data) {
            jQuery('.tv-posts').html('');
            jQuery.each(data, function(key, value) {
                jQuery('.tv-posts').append('<p class="tv-result-' + value.result + '">' + value.message + ' (total: ' + value.count + ' - deleted: ' + value.deleted + ')</p>');
            });
        },
        error: function(jqXHR,textStatus,errorThrown) {
            jQuery('.tv-posts').html('<p class="tv-result-error">Error: ' + errorThrown + ' (maybe a server timeout?)</p>');
        },
        complete: function(jqXHR,textStatus) {
            tv_remove_terms();
        }
    }); 
}

function tv_remove_terms() {
    jQuery.ajax({
        url: home_url + '/wp-admin/admin-ajax.php',
        data: {'action': 'remove_terms'},
        type: 'GET',
        success: function(data) {
            jQuery('.tv-terms').html('');
            var objects = JSON.parse(data);
            jQuery.each(objects, function(key, value) {
                jQuery('.tv-terms').append('<p class="tv-result-' + value.result + '">' + value.message + ' (total: ' + value.count + ' - deleted: ' + value.deleted + ')</p>');
            });
        },
        error: function(jqXHR,textStatus,errorThrown) {
            jQuery('.tv-terms').html('<p class="tv-result-error">Error: ' + errorThrown + ' (maybe a server timeout?)</p>');
        },
        complete: function(jqXHR,textStatus) {
            jQuery('#tv-removeall').text('All done!');
        }        
    }); 
}
