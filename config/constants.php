<?php
return [
	'paymentConfig'=>[
		"country"=>"PH",
		"currency"=> "PHP",
		"merchant"=> "dynedgephils_Dev",
		"password"=> "q5m12345",
		"timeout"=>"780",
		"MerchantName"=>"Merchant A",
		"paymenturl"=>"https://pay.fiuu.com/RMS/pay/dynedgephils_Dev/GCash.php",
		"channelUrl"=>"https://pay.fiuu.com/RMS/pay/",
		"verifykey"=>"982482d41e3f9bf3bf63f50e441da171"
	],
	'paymentStatus' =>[
		'0' => 'Pending',
		'1' => 'Paid',
		'2' => 'Cancelled',
		'3' => 'Failed'
	],
	'tlpePaymentConfig'=>[
		"country"=>"PH",
		"currency"=> "PHP",
		"apiBase"=>"https://test-api.tlpe.io",
		"token"=>"MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAJ+THfo/5rl0mvDFegesZSm4hlYJW9gNQJ6SXBuR006Ac/xoPfUuQZZXEwxBGQKEuMs9bwrTHRszRIgEhil24pMCAwEAAQ==",
		"jwtSecret"=>"MIIBUwIBADANBgkqhkiG9w0BAQEFAASCAT0wggE5AgEAAkEAn5Md+j/muXSa8MV6B6xlKbiGVglb2A1AnpJcG5HTToBz/Gg99S5BllcTDEEZAoS4yz1vCtMdGzNEiASGKXbikwIDAQABAkBiX72xUseYOQxztioepObQq5MVYzudm73kg/IIhQOxdoCg/+bidHr3Oq+GY1/K/VGXk3A1+9EndhfBd97PJCuhAiEA3wVRVtbmZBZzsR9RHrws3szg8YErp5XdjxUe4L6Fy8kCIQC3K/tQsRjncw2T39DGxVdhA9ERPbG0OslThHa1RwixewIgOjKbDTw7Fvc87YWsl4anduSj9qGskKjtDj+GtUNCivECICMwMdFolC4ybhNQVd05n/WlNA6p2W+UM4T115Avmz3tAiAvz+YIpTxZ2zSjivY5hJfsZApo/HZ/QHxG8Ry251ESkg==" 
	],
	'tlpePaymentConfigProd'=>[
		"country"=>"PH",
		"currency"=> "PHP",
		"apiBase"=>"https://api.tlpe.io",
		"token"=>"MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAIlCeEa/dhoWMklKX9P6Wjdasm6ks7BpGvvmPVFY8bB0xQwWRy9Wg0Hi9Kzya/PkD4Ak0JF0u+RFebq+ViqpFakCAwEAAQ==",
		"jwtSecret"=>"MIIBVAIBADANBgkqhkiG9w0BAQEFAASCAT4wggE6AgEAAkEAiUJ4Rr92GhYySUpf0/paN1qybqSzsGka++Y9UVjxsHTFDBZHL1aDQeL0rPJr8+QPgCTQkXS75EV5ur5WKqkVqQIDAQABAkBSob+C5/STk9VGJg42sTrqpCFTVrgOddgW2e8EMAWgcmnNL6MaLVLvhoQaXzXRcY4wN5glR7mtuWK+OTfx3CcBAiEAxN7Uc9Rr+UKZK6wgy/J1V+iMGX2u/1v+sR7+OHrpQDkCIQCyfDoGRAI9tkueCwlLs61zdrNuxqMbc2BbyN9JNHCg8QIgGK77oj46/3irLb+aKRgQQGJm1ndcrqXBLEH/i+NVRXkCIH27g0icqhN9EqG/1omMhnibOuWjao1Q5qVhyOGR3nGRAiEAtoE/GwvtzG5h9qVUvJBJpD+YkuDDo1Nb1seKkF5EA7E=" 
	],
];
