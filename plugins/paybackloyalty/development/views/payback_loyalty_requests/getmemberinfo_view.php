<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<GetMemberInfo xmlns="http://www.paybackloyalty.com/">
			<partnerId><?=$partnerId?></partnerId>
			<requestId><?=$requestId?></requestId>
			<MemberId><?=$memberId?></MemberId>
			<isMemberId><?=$isMemberId?></isMemberId>
		</GetMemberInfo>
	</soap:Body>
</soap:Envelope>