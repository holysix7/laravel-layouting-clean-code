function currencyFormat(number){
    
    try{
        let formated = Number(number).toLocaleString('id-ID', {currency: 'IDR', style: 'currency'});
        return formated.replace(',00','');
    }catch(e){
        return 0;
    }
    
}  

function numberFormat(number){
    try{
        let formated = currencyFormat(number);
        return formated.replace('Rp','').trim();
    }catch(e){
        return 0;
    }
    
}

function getSpinner(param = null){
    let prop = { color: 'text-dark' };
    
    if(param) prop.color = param.color;

    return `<div class="spinner-border ${prop.color}" style="width:1rem;height:1rem;font-weight:normal!important" role="status">
            <span class="sr-only">Loading...</span>
            </div>`;
}

function setFlashMessage({
    message,
    type = 'success'
}){
    let style = type === 'success' ? 'alert-success' : 'alert-danger';

    let html = `<div class="alert alert-block ${style}">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>${message}</strong>
        </div>`;
    $('#flash-message').html(html);

    $("html, body").animate({
        scrollTop: "0"
    });
}

function copyToClipboard(text) {  
  navigator.clipboard.writeText(text);
  setFlashMessage({message:'berhasil disalin ke clipboard'})
}

function ucwords (str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}