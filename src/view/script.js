
if($('.targetlink').length>0)
{
    // redirect to alias target
    var redir = function()
    {
        $('.targetlink')[0].click();
    };
    window.setTimeout(redir, 3000);
}
else if($('.formredir').length>0)
{
    // submit form for referer removal
    $('.formredirclick').attr('disabled', 'disabled');
    $('.formredir')[0].submit();
}
