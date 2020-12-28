<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<RequestNewCardNumber xmlns="http://www.paybackloyalty.com/">
			<partnerId><?=$partnerId?></partnerId>
			<requestId><?=$requestId?></requestId>
		</RequestNewCardNumber>
	</soap:Body>
</soap:Envelope>