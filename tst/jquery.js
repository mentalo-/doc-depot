    $('#showcharacters').click(function() {
        if ($(this).attr('checked')) {
            $('#password').replaceWith('
<input id="password" name="password" type="text" value="' + $('#password').attr('value') + '" />');
        }
        else {
            $('#password').replaceWith('
<input id="password" name="password" type="password" value="' + $('#password').attr('value') + '" />');
        }
    });
});
(v(yz(y(eyv(ez