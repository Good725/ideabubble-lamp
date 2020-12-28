<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<ValidateMemberAccess xmlns="http://www.paybackloyalty.com/">
		  <partnerId><?=$partnerId?></partnerId>
		  <requestId><?=$requestId?></requestId>
		  <storeId><?=$storeId?></storeId>
		  <username><?=$username?></username>
		  <password><?=$password?></password>
		</ValidateMemberAccess>
	</soap:Body>
</soap:Envelope>