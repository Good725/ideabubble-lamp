<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<UpdatePassword xmlns="http://www.paybackloyalty.com/">
			<partnerId><?=$partnerId?></partnerId>
			<requestId><?=$requestId?></requestId>
			<storeId><?=$storeId?></storeId>
			<memberid><?=$memberId?></memberid>
			<oldpassword><?=$oldpassword?></oldpassword>
			<newpassword><?=$newpassword?></newpassword>
			<confirmpassword><?=$confirmpassword?></confirmpassword>
		</UpdatePassword>
	</soap:Body>
</soap:Envelope>