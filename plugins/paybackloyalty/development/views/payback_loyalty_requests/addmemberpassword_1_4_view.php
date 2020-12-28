<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<AddMemberPassword_1_4 xmlns="http://www.paybackloyalty.com/">
			<partnerId><?=$partnerId?></partnerId>
			<requestId><?=$requestId?></requestId>
			<memberId><?=$memberId?></memberId>
			<username><?=$username?></username>
			<password><?=$password?></password>
		</AddMemberPassword_1_4>
	</soap:Body>
</soap:Envelope>