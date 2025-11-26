<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment Page</title>
  <style>
    * { box-sizing: border-box; }

    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 20px;
    }

    .payment-container {
      background: #fff;
      border-radius: 12px;
      max-width: 600px;
      margin: auto;
      padding: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 1;
    }

    .billing-title {
      text-align: center;
      font-size: 28px;
      margin-bottom: 10px;
    }

    .billing-amount {
      text-align: center;
      font-size: 20px;
      color: #555;
      margin-bottom: 20px;
    }

    .tabs {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .tab {
      border: 1px solid #ccc;
      border-radius: 10px;
      overflow: visible;
      position: relative;
      background: #f9f9f9;
    }

    .tab-header {
      background-color: #eafbea;
      padding: 12px;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }

    .tab-header img {
      width: 30px;
    }

    .tab-body {
      display: none;
      padding: 15px;
    }

    .tab.active .tab-body {
      display: block;
    }

    .custom-dropdown {
      position: relative;
      width: 100%;
      margin-top: 10px;
      z-index: 1000;
    }

    .dropdown-selected {
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      background-color: #fff;
    }

    .dropdown-selected img {
      width: 30px;
      height: 30px;
    }

    .dropdown-options {
      position: absolute;
      top: 110%;
      left: 0;
      right: 0;
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 8px;
      display: none;
      max-height: 200px;
      overflow-y: auto;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      z-index: 9999;
    }

    .dropdown-option {
      padding: 10px;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }

    .dropdown-option:hover {
      background-color: #e8f0e8;
    }

    .footer {
      margin-top: 30px;
      text-align: center;
    }

    .footer p {
      font-size: 14px;
      color: #666;
      margin-bottom: 15px;
    }

    .footer a {
      color: #025a02;
      text-decoration: none;
    }

    .pay-button {
      padding: 10px 20px;
      background-color: #025a02;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    .tab-header img{
      width: 50px;
      height: 50px;
      object-fit: contain;
    }

    .dropdown-selected img,
    .dropdown-option img {
      width: 40px;
      height: 40px;
      object-fit: contain;
    }
  </style>
</head>
<body>
  <div class="payment-container">
    <h1 class="billing-title">Billing</h1>
    <p class="billing-amount">
        <input type="hidden" value="{{$total_amount}}" id="payAmount">
        <input type="hidden" value="{{$business_id}}" id="business_id">
      <span>Amount:</span> ₱ <span id="currentPayAmount">{{$total_amount}}</span>
    </p>

    <div class="tabs">
      <!-- Category 1 -->
      @foreach ($data as $item)
          <div class="tab">
            <div class="tab-header" onclick="toggleTab(this)">
            <img src="{{ asset('public/fiuu_payment/' . $item['icon']) }}" />
              <span>{{$item['type']}}</span>
            </div>
            <div class="tab-body">
              <label>Select Bank</label>
              <div class="custom-dropdown">
                <div class="dropdown-selected" onclick="event.stopPropagation(); toggleDropdown(this);">
                  <img src="https://cdn-icons-png.flaticon.com/512/197/197615.png" alt="Default" />
                  <span>Select Bank</span>
                </div>
                <div class="dropdown-options">
                    @foreach ($item['channels'] as $bank)
                      <div class="dropdown-option" channel_id="{{$bank['channel_id']}}" onclick="selectBank(this,'<?=$bank['formula']?>')" >
                        <img src="{{ asset('public/fiuu_payment_channel/' . $bank['channel_icon']) }}" />
                        <span>{{$bank['name']}}</span>
                      </div>
                    @endforeach
                </div>
              </div>
            </div>
          </div>
      @endforeach
    </div>

    <div class="footer">
      <p>
        By continuing, you agree to the
        <a href="#">Terms and Conditions</a> &
        <a href="#">Privacy Policy</a>
      </p>
      <button class="pay-button" id="payBtn">Pay</button>

    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    const BASE_URL = "{{ url('/') }}";
    var channel_id='';
    document.getElementById('payBtn').addEventListener('click', function() {
        const activeTab = document.querySelector('.tab.active');
        if (!activeTab) {
            alert('Please select a payment category.');
            return;
        }
        const selectedText = activeTab.querySelector('.dropdown-selected span').textContent.trim();
        const defaults = ['Select Bank', 'Select Wallet'];
        if (defaults.includes(selectedText)) {
            alert('Please select bank');
            return;
        }

        $.ajax({
          url: BASE_URL + '/addpayment',
          data : {
              channel_id:channel_id,
              amount:$("#payAmount").val(),
              business_id:$("#business_id").val()
          },
          type: 'GET',
          dataType:"JSON",
          success: function(res) {
             console.log("Server Response:", res);
              var transaction_id = res.data.transaction_id;
              if(transaction_id!=''){
                  const urlToOpen = BASE_URL + '/payment-view-page/'+transaction_id; 
                  openPaymentPage(urlToOpen,transaction_id)
              }
          }
      });
    });
    function openPaymentPage(url) {
      const subWindowDetails = getSubWindowDetails();
      const windowName = subWindowDetails.windowName;
      const windowFeatures = subWindowDetails.windowFeatures;
      // Open the new window
      const newWindow = window.open(url, windowName, windowFeatures);
      const inter = setInterval(() => {
        if (newWindow?.closed) {
          clearInterval(inter);
          onWindowClosedNew()
        }
      }, 100);
    }

    function onWindowClosedNew(){
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
    }

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

    function toggleTab(header) {
      const allTabs = document.querySelectorAll(".tab");
      allTabs.forEach(tab => {
        // Close all tabs
        tab.classList.remove("active");

        // Reset bank selection in all dropdowns
        const dropdown = tab.querySelector(".custom-dropdown");
        if (dropdown) {
          dropdown.querySelector(".dropdown-selected img").src = "https://cdn-icons-png.flaticon.com/512/197/197615.png";
          dropdown.querySelector(".dropdown-selected span").textContent = "Select Bank";
        }
      });

      // Open clicked tab
      const tab = header.parentElement;
      tab.classList.add("active");
    }

    function toggleDropdown(selected) {
      const options = selected.nextElementSibling;
      const isOpen = options.style.display === "block";

      // Close all dropdowns
      document.querySelectorAll(".dropdown-options").forEach(opt => opt.style.display = "none");

      // Toggle this one
      options.style.display = isOpen ? "none" : "block";
    }

    function selectBank(option,formula) {
        const dropdown = option.closest(".custom-dropdown");
        const selected = dropdown.querySelector(".dropdown-selected");
        const img = option.querySelector("img").src;
        const text = option.querySelector("span").textContent;
        channel_id = option.getAttribute('channel_id');
        selected.querySelector("img").src = img;
        selected.querySelector("span").textContent = text;

        dropdown.querySelector(".dropdown-options").style.display = "none";

        if (!formula || !formula.includes('x')) {
            console.error('Formula does not contain "x" or is undefined');
            return;
        }

        try {
            var payAmount = $("#payAmount").val();
            const hasPercentage = /(\d+(\.\d+)?)%/.test(formula);
            let sanitizedFormula = formula.replace(/(\d+(\.\d+)?)%/g, "($1/100)");
            sanitizedFormula = sanitizedFormula.replace(/x/g, payAmount.toString());
            let finalAmount = eval(sanitizedFormula);

            if (isNaN(finalAmount) || finalAmount == null) {
                console.error('Calculation error: finalAmount is invalid', finalAmount);
                finalAmount = 0;
            }
            var currentPayAmount = hasPercentage ? Number(finalAmount) + Number(payAmount) : Number(finalAmount);

            $("#currentPayAmount").html(currentPayAmount.toFixed(2))
        } catch (error) {
          console.error('Invalid formula:', formula, error);
        }
    }

    // Close dropdowns when clicking outside
    window.addEventListener("click", () => {
      document.querySelectorAll(".dropdown-options").forEach(d => d.style.display = "none");
    });
  </script>
</body>
</html>
