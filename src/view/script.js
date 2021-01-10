if(document.querySelector('.targetlink'))
{
    // redirect to alias target
    var redir = function()
    {
        document.querySelector('.targetlink').click();
    };

    window.setTimeout(redir, 3000);
}
else if(document.querySelector('.formredir'))
{
    // submit form for referer removal
    document.querySelector('.formredircontainer').classList.add('hidden');
    document.querySelector('.formredir').submit();
}
