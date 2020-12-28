<div class="color_picker_wrapper" id="<?= $data['variable'] ?>-color_picker">
	<div class="select_color_preview" title="Preview" style="background-color:<?= $data['value'] ?>"></div>
	<div class="select_color_text">(Click to select a colour)</div>
	<div class="color_palette">
		<table>
			<thead>
			<tr>
				<th colspan="8">Standard Colours</th>
			</tr>
			</thead>
			<tbody class="standard_palette">
			<tr>
				<td style="background-color:#000000;" title="rgb(0, 0, 0)"></td>
				<td style="background-color:#434343;" title="rgb(67, 67, 67)"></td>
				<td style="background-color:#666666;" title="rgb(102, 102, 102)"></td>
				<td style="background-color:#999999;" title="rgb(153, 153, 153)"></td>
				<td style="background-color:#B7B7B7;" title="rgb(183, 183, 183)"></td>
				<td style="background-color:#CCCCCC;" title="rgb(204, 204, 204)"></td>
				<td style="background-color:#D9D9D9;" title="rgb(217, 217, 217)"></td>
				<td style="background-color:#EFEFEF;" title="rgb(239, 239, 239)"></td>
				<td style="background-color:#F3F3F3;" title="rgb(243, 243, 243)"></td>
				<td style="background-color:#FFFFFF;" title="rgb(255, 255, 255)"></td>
			</tr>
			<tr>
				<td colspan="8" style="border:none;height:2px;"></td>
			</tr>
			<tr>
				<td style="background-color:#990000;" title="rgb(153, 0, 0)"></td>
				<td style="background-color:#FF0000;" title="rgb(255, 0, 0)"></td>
				<td style="background-color:#FF9900;" title="rgb(255, 153, 0)"></td>
				<td style="background-color:#FFFF00;" title="rgb(255, 255, 0)"></td>
				<td style="background-color:#00FF00;" title="rgb(0, 255, 0)"></td>
				<td style="background-color:#00FFFF;" title="rgb(0, 255, 255)"></td>
				<td style="background-color:#3399FF;" title="rgb(51, 153, 255)"></td>
				<td style="background-color:#0000FF;" title="rgb(0, 0, 255)"></td>
				<td style="background-color:#800080;" title="rgb(128, 0, 128)"></td>
				<td style="background-color:#FF00FF;" title="rgb(255, 0, 255)"></td>
			</tr>
			<tr>
				<td colspan="8" style="border:none;height:2px;"></td>
			</tr>
			<tr>
				<td style="background-color:#e6b8af;" title="rgb(230, 184, 175)"></td>
				<td style="background-color:#f4cccc;" title="rgb(244, 204, 204)"></td>
				<td style="background-color:#fce5cd;" title="rgb(252, 229, 205)"></td>
				<td style="background-color:#fff2cc;" title="rgb(255, 242, 204)"></td>
				<td style="background-color:#d9ead3;" title="rgb(217, 234, 211)"></td>
				<td style="background-color:#d0e0e3;" title="rgb(208, 224, 227)"></td>
				<td style="background-color:#c9daf8;" title="rgb(201, 218, 248)"></td>
				<td style="background-color:#cfe2f3;" title="rgb(207, 226, 243)"></td>
				<td style="background-color:#d9d2e9;" title="rgb(217, 210, 233)"></td>
				<td style="background-color:#ead1dc;" title="rgb(234, 209, 220)"></td>
			</tr>
			<tr>
				<td style="background-color:#db7e6b;" title="rgb(219, 126, 107)"></td>
				<td style="background-color:#e89898;" title="rgb(232, 152, 152)"></td>
				<td style="background-color:#f7c99b;" title="rgb(247, 201, 155)"></td>
				<td style="background-color:#fde398;" title="rgb(253, 227, 152)"></td>
				<td style="background-color:#b5d5a7;" title="rgb(181, 213, 167)"></td>
				<td style="background-color:#a1c2c7;" title="rgb(161, 194, 199)"></td>
				<td style="background-color:#a3c0f2;" title="rgb(163, 192, 242)"></td>
				<td style="background-color:#9ec3e6;" title="rgb(158, 195, 230)"></td>
				<td style="background-color:#b3a6d4;" title="rgb(179, 166, 212)"></td>
				<td style="background-color:#d3a5bc;" title="rgb(211, 165, 188)"></td>
			</tr>
			<tr>
				<td style="background-color:#ca4126;" title="rgb(202, 65, 38)"></td>
				<td style="background-color:#de6666;" title="rgb(222, 102, 102)"></td>
				<td style="background-color:#f4b16b;" title="rgb(244, 177, 107)"></td>
				<td style="background-color:#fdd766;" title="rgb(253, 215, 102)"></td>
				<td style="background-color:#92c27d;" title="rgb(146, 194, 125)"></td>
				<td style="background-color:#76a4ae;" title="rgb(118, 164, 174)"></td>
				<td style="background-color:#6d9de9;" title="rgb(109, 157, 233)"></td>
				<td style="background-color:#6fa7da;" title="rgb(111, 167, 218)"></td>
				<td style="background-color:#8d7cc1;" title="rgb(141, 124, 193)"></td>
				<td style="background-color:#c07b9f;" title="rgb(192, 123, 159)"></td>
			</tr>
			<tr>
				<td style="background-color:#a51d02;" title="rgb(165, 29, 2)"></td>
				<td style="background-color:#ca0202;" title="rgb(202, 2, 2)"></td>
				<td style="background-color:#e49039;" title="rgb(228, 144, 57)"></td>
				<td style="background-color:#efc033;" title="rgb(239, 192, 51)"></td>
				<td style="background-color:#6aa74f;" title="rgb(106, 167, 79)"></td>
				<td style="background-color:#45808d;" title="rgb(69, 128, 141)"></td>
				<td style="background-color:#3d78d6;" title="rgb(61, 120, 214)"></td>
				<td style="background-color:#3e84c4;" title="rgb(62, 132, 196)"></td>
				<td style="background-color:#674ea6;" title="rgb(103, 78, 166)"></td>
				<td style="background-color:#a54d79;" title="rgb(165, 77, 121)"></td>
			</tr>
			<tr>
				<td style="background-color:#85200c;" title="rgb(133, 32, 12)"></td>
				<td style="background-color:#990000;" title="rgb(153, 0, 0)"></td>
				<td style="background-color:#b45f06;" title="rgb(180, 95, 6)"></td>
				<td style="background-color:#bf9000;" title="rgb(191, 144, 0)"></td>
				<td style="background-color:#38761d;" title="rgb(56, 118, 29)"></td>
				<td style="background-color:#134f5c;" title="rgb(19, 79, 92)"></td>
				<td style="background-color:#1155cc;" title="rgb(17, 85, 204)"></td>
				<td style="background-color:#0b5394;" title="rgb(11, 83, 148)"></td>
				<td style="background-color:#351c75;" title="rgb(53, 28, 117)"></td>
				<td style="background-color:#741b47;" title="rgb(116, 27, 71)"></td>
			</tr>
			<tr>
				<td style="background-color:#5b0f00;" title="rgb(91, 15, 0)"></td>
				<td style="background-color:#660000;" title="rgb(102, 0, 0)"></td>
				<td style="background-color:#783f04;" title="rgb(120, 63, 4)"></td>
				<td style="background-color:#7f6000;" title="rgb(127, 96, 0)"></td>
				<td style="background-color:#274e13;" title="rgb(39, 78, 19)"></td>
				<td style="background-color:#0c343d;" title="rgb(12, 52, 61)"></td>
				<td style="background-color:#1c4587;" title="rgb(28, 69, 135)"></td>
				<td style="background-color:#073763;" title="rgb(7, 55, 99)"></td>
				<td style="background-color:#20124d;" title="rgb(32, 18, 77)"></td>
				<td style="background-color:#4c1130;" title="rgb(76, 17, 48)"></td>
			</tr>

			<tr>
				<td colspan="9">Transparent&nbsp;</td>
				<td style="background-color:transparent;" class="transparent_option" title="transparent"></td>
			</tr>
			</tbody>

			<thead>
			<tr>
				<th colspan="10">Custom Colours</th>
			</tr>
			</thead>
			<tbody class="custom_palette">
			</tbody>
			<tfoot>
			<tr>
				<td colspan="10"><div class="custom_color_link" style="text-align:left;"><input type="hidden"><a href="#">Add more colours ...</a></div></td>
			</tr>
			</tfoot>
		</table>
	</div>

</div>