<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<ConvertPointsToCash xmlns="http://www.paybackloyalty.com/">
			<partnerId><?=$partnerId?></partnerId>
			<requestId><?=$requestId?></requestId>
			<storeId><?=$storeId?></storeId>
			<userId><?=$userId?></userId>
			<memberId><?=$memberId?></memberId>
			<cardNo><?=$cardNo?></cardNo>
			<points><?=$points?></points>
		</ConvertPointsToCash>
	</soap:Body>
</soap:Envelope>