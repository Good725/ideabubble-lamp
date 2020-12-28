<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<RegisterNewMember_1_3 xmlns="http://www.paybackloyalty.com/">
			<partnerId><?=$partnerId?></partnerId>
			<requestId><?=$requestId?></requestId>
			<storeId><?=$storeId?></storeId>
			<userId><?=$userId?></userId>
			<language><?=$language?></language>
			<gender><?=$gender?></gender>
			<title><?=$title?></title>
			<firstname><?=$firstname?></firstname>
			<surname><?=$surname?></surname>
			<dateofbirth><?=$dateofbirth?></dateofbirth>
			<addr1><?=$addr1?></addr1>
			<addr2><?=$addr2?></addr2>
			<addr3><?=$addr3?></addr3>
			<addr4><?=$addr4?></addr4>
			<country><?=$country?></country>
			<email><?=$email?></email>
			<mobileNo><?=$mobileNo?></mobileNo>
			<contactForInfo><?=$contactForInfo?></contactForInfo>
			<contactForResearch><?=$contactForResearch?></contactForResearch>
			<contactByPartners><?=$contactByPartners?></contactByPartners>
			<contactBySMS><?=$contactBySMS?></contactBySMS>
			<nationality><?=$nationality?></nationality>
		</RegisterNewMember_1_3>
	</soap:Body>
</soap:Envelope>