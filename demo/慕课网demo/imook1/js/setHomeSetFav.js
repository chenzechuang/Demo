//¼ÓÈëÊÕ²Ø

function AddFavorite(sURL, sTitle)
{
    
    sURL = encodeURI(sURL);
    try
    {
        
        window.external.addFavorite(sURL, sTitle);
        
    }
    catch (e)
    {
        
        try
        {
            
            window.sidebar.addPanel(sTitle, sURL, "");
            
        }
        catch (e)
        {
            
            alert("¼ÓÈëÊÕ²ØÊ§°Ü£¬ÇëÊ¹ÓÃCtrl+D½øÐÐÌí¼Ó,»òÊÖ¶¯ÔÚä¯ÀÀÆ÷Àï½øÐÐÉèÖÃ.");
            
        }
        
    }
    
}

//ÉèÎªÊ×Ò³

function SetHome(url)
{
    
    if (document.all)
    {
        
        document.body.style.behavior = 'url(#default#homepage)';
        
        document.body.setHomePage(url);
        
    }
    else
    {
        
        alert("ÄúºÃ,ÄúµÄä¯ÀÀÆ÷²»Ö§³Ö×Ô¶¯ÉèÖÃÒ³ÃæÎªÊ×Ò³¹¦ÄÜ,ÇëÄúÊÖ¶¯ÔÚä¯ÀÀÆ÷ÀïÉèÖÃ¸ÃÒ³ÃæÎªÊ×Ò³!");
        
    }
    
}
