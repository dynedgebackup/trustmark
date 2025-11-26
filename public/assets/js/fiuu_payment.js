$(document).ready(function() {
    if (localStorage.getItem('paymentSuccess') === 'true') {
        $('#modal-success').modal('show');
        // localStorage.removeItem('paymentSuccess');
    }
    if (localStorage.getItem('paymentError') === 'true') {
        $('#modal-error').modal('show');
        localStorage.removeItem('paymentError');
    }
            
    $("#makePayment").click(function(){
        openPaymentWindow();
    })
});

function getSubWindowDetails(){
    const windowName = 'CenteredWindow'; 
    const width = 700;  // Width of the new window
    const height = 500; // Height of the new window (adjusted for padding)

    // Calculate position to center the window
    const left = (window.innerWidth / 2) - (width / 2);
    const top = (window.innerHeight / 2) - (height / 2) + 150; // Adjust top value directly

    // Specify window features
    const windowFeatures = `width=${width},height=${height},top=${top},left=${left},menubar=no,toolbar=no,location=yes,resizable=yes`;

    const object = {
      windowName : 'CenteredWindow',
      windowFeatures,
    }
    return object;
}

function openPaymentPage(url) {
    const subWindowDetails = getSubWindowDetails();
    const windowName = subWindowDetails.windowName;
    const windowFeatures = subWindowDetails.windowFeatures;
    // Open the new window
    const newWindow = window.open(url, windowName, windowFeatures);
    const inter = setInterval(() => {
      if (newWindow?.closed) {
        clearInterval(inter);
        onWindowClosed(); 
      }
    }, 100);
}

function onWindowClosed(){
    var BASE_URL = $("#BASE_URL").val(); 
    $.ajax({
      url: BASE_URL + '/check-payment-response',
      data : {
          business_id:$("#business_id").val()
      },
      type: 'GET',
      dataType:"JSON",
      success: function(res) {
         console.log("Server Response:", res);
          var payment_status = res.data.payment_status;
          if(payment_status==1){
            localStorage.setItem('paymentSuccess', 'true');
            window.location.reload();
          }else{
            localStorage.setItem('paymentError', 'true');
            window.location.reload();
          }
      }
    });

   
    // Reload the page
    
}
function openPaymentWindow(){
    var BASE_URL = $("#BASE_URL").val();
    var business_id = $("#business_id").val();
    const urlToOpen = BASE_URL + "/payment-method/"+business_id;
    window.location.href = urlToOpen
    //openPaymentPage(urlToOpen)
}