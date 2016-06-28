/**
 * @author      Dennis Rogers <dennis@drogers.net>
 * @address     www.drogers.net
 */

jQuery(document).ready(function(){

    jQuery('select[name^=state]').bind('change', function(e){
        var changed = jQuery(this);
        
        jQuery.post(
            '/',
            changed.serialize(),
            function(data) {
                jQuery('body').html(data);
            }
        );      
    });
    
    jQuery('span[id^=name]').bind('click', function(e){
        
        var btn = jQuery(this);
        btn.hide();
        
        var input = document.createElement('input');
        
        input.setAttribute('id', btn.prop('id'));
        input = jQuery(input);
        input.val(btn.text());
        input.prop('name', 'name[' + btn.prop('id').split('_')[1] + ']');
        btn.parent().append(input);
        
        var wrap = document.createElement('div');
        wrap = jQuery(wrap);
        wrap.addClass('ui-input-text ui-body-inherit ui-corner-all ui-shadow-inset');
        input.wrap(wrap);
        
        input.focus();
        
    });
	
	setInterval(updatePage, 30000);
});

function updatePage() {
    
    jQuery.ajax(
       '/json.php',
        {
            dataType: 'json',
            success: function(transport){
                for(c in transport.characters) {
                    character = transport.characters[c];
                    jQuery('#energy_' + character.id).val(character.energy);
                    jQuery('#state_' + character.id).val(character.state);
                }
                jQuery('#max').val(transport.user.max);
            }
        }
    );
    
}
