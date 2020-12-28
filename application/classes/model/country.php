<?php defined('SYSPATH') or die('No direct script access.');

class Model_Country extends Model {

	public static $countries =
			array (
			  'AD' => array (  'dial_code' => '376', 'name' => 'Andorra', 'id' => 'AD', 'alpha3' =>'AND', 'iso' => '020'),
			  'AE' => array (  'dial_code' => '971', 'name' => 'United Arab Emirates', 'id' => 'AE','alpha3' =>'ARE', 'iso' => '784'),
			  'AF' => array (  'dial_code' => '93', 'name' => 'Afghanistan', 'id' => 'AF', 'alpha3' => 'AFG', 'iso' => '004'),
			  'AG' => array (  'dial_code' => '1268', 'name' => 'Antigua And Barbuda', 'id' => 'AG','alpha3' =>'ATG', 'iso' => '028'),
			  'AI' => array (  'dial_code' => '1264', 'name' => 'Anguilla', 'id' => 'AI','alpha3' =>'AIA', 'iso' => '660'),
			  'AL' => array (  'dial_code' => '355', 'name' => 'Albania', 'id' => 'AL','alpha3' =>'ALB', 'iso' => '008'),
			  'AM' => array (  'dial_code' => '374', 'name' => 'Armenia', 'id' => 'AM','alpha3' =>'ARM', 'iso' => '051'),
			  'AN' => array (  'dial_code' => '599', 'name' => 'Netherlands Antilles', 'id' => 'AN','alpha3' =>'ANT', 'iso' => '530'),
			  'AO' => array (  'dial_code' => '244', 'name' => 'Angola', 'id' => 'AO','alpha3' =>'AGO', 'iso' => '024'),
			  'AQ' => array (  'dial_code' => '672', 'name' => 'Antarctica', 'id' => 'AQ','alpha3' =>'ATA', 'iso' => '010'),
			  'AR' => array (  'dial_code' => '54', 'name' => 'Argentina', 'id' => 'AR','alpha3' =>'ARG', 'iso' => '032'),
			  'AS' => array (  'dial_code' => '1684', 'name' => 'American Samoa', 'id' => 'AS','alpha3' =>'ASM', 'iso' => '016'),
			  'AT' => array (  'dial_code' => '43', 'name' => 'Austria', 'id' => 'AT','alpha3' =>'AUT', 'iso' => '040'),
			  'AU' => array (  'dial_code' => '61', 'name' => 'Australia', 'id' => 'AU','alpha3' =>'AUS', 'iso' => '036'),
			  'AW' => array (  'dial_code' => '297', 'name' => 'Aruba', 'id' => 'AW', 'alpha3' =>'ABW', 'iso' => '533'),
			  'AZ' => array (  'dial_code' => '994', 'name' => 'Azerbaijan', 'id' => 'AZ','alpha3' =>'AZE', 'iso' => '031'),
			  'BA' => array (  'dial_code' => '387', 'name' => 'Bosnia And Herzegovina', 'id' => 'BA','alpha3' =>'BIH', 'iso' => '070'),
			  'BB' => array (  'dial_code' => '1246', 'name' => 'Barbados', 'id' => 'BB','alpha3' =>'BRB', 'iso' => '052'),
			  'BD' => array (  'dial_code' => '880', 'name' => 'Bangladesh', 'id' => 'BD','alpha3' =>'BGD', 'iso' => '050'),
			  'BE' => array (  'dial_code' => '32', 'name' => 'Belgium', 'id' => 'BE','alpha3' =>'BEL', 'iso' => '056'),
			  'BF' => array (  'dial_code' => '226', 'name' => 'Burkina Faso', 'id' => 'BF','alpha3' =>'BFA', 'iso' => '854'),
			  'BG' => array (  'dial_code' => '359', 'name' => 'Bulgaria', 'id' => 'BG','alpha3' =>'BGR', 'iso' => '100'),
			  'BH' => array (  'dial_code' => '973', 'name' => 'Bahrain', 'id' => 'BH','alpha3' =>'BHR', 'iso' => '048'),
			  'BI' => array (  'dial_code' => '257', 'name' => 'Burundi', 'id' => 'BI','alpha3' =>'BDI', 'iso' => '108'),
			  'BJ' => array (  'dial_code' => '229', 'name' => 'Benin', 'id' => 'BJ','alpha3' =>'BEN', 'iso' => '204'),
			  'BL' => array (  'dial_code' => '590', 'name' => 'Saint Barthelemy', 'id' => 'BL','alpha3' =>'BLM', 'iso' => '652'),
			  'BM' => array (  'dial_code' => '1441', 'name' => 'Bermuda', 'id' => 'BM','alpha3' =>'BMU', 'iso' => '060'),
			  'BN' => array (  'dial_code' => '673', 'name' => 'Brunei Darussalam', 'id' => 'BN','alpha3' =>'BRN', 'iso' => '096'),
			  'BO' => array (  'dial_code' => '591', 'name' => 'Bolivia', 'id' => 'BO','alpha3' =>'BOL', 'iso' => '068'),
			  'BR' => array (  'dial_code' => '55', 'name' => 'Brazil', 'id' => 'BR','alpha3' =>'BRA', 'iso' => '076'),
			  'BS' => array (  'dial_code' => '1242', 'name' => 'Bahamas', 'id' => 'BS','alpha3' =>'BHS', 'iso' => '044'),
			  'BT' => array (  'dial_code' => '975', 'name' => 'Bhutan', 'id' => 'BT','alpha3' =>'BTN', 'iso' => '064'),
			  'BW' => array (  'dial_code' => '267', 'name' => 'Botswana', 'id' => 'BW','alpha3' =>'BWA', 'iso' => '072'),
			  'BY' => array (  'dial_code' => '375', 'name' => 'Belarus', 'id' => 'BY','alpha3' =>'BLR', 'iso' => '112'),
			  'BZ' => array (  'dial_code' => '501', 'name' => 'Belize', 'id' => 'BZ','alpha3' =>'BLZ', 'iso' => '084'),
			  'CA' => array (  'dial_code' => '1', 'name' => 'Canada', 'id' => 'CA','alpha3' =>'CAN', 'iso' => '124'),
			  'CC' => array (  'dial_code' => '61', 'name' => 'Cocos (keeling) Islands', 'id' => 'CC','alpha3' =>'CCK', 'iso' => '166'),
			  'CD' => array (  'dial_code' => '243', 'name' => 'Congo, The Democratic Republic Of The', 'id' => 'CD','alpha3' =>'COD', 'iso' => '180'),
			  'CF' => array (  'dial_code' => '236', 'name' => 'Central African Republic', 'id' => 'CF','alpha3' =>'CAF', 'iso' => '140'),
			  'CG' => array (  'dial_code' => '242', 'name' => 'Congo', 'id' => 'CG','alpha3' =>'COG', 'iso' => '178'),
			  'CH' => array (  'dial_code' => '41', 'name' => 'Switzerland', 'id' => 'CH','alpha3' =>'CHE', 'iso' => '756'),
			  'CI' => array (  'dial_code' => '225', 'name' => 'Cote D Ivoire', 'id' => 'CI','alpha3' =>'CIV', 'iso' => '384'),
			  'CK' => array (  'dial_code' => '682', 'name' => 'Cook Islands', 'id' => 'CK','alpha3' =>'COK', 'iso' => '184'),
			  'CL' => array (  'dial_code' => '56', 'name' => 'Chile', 'id' => 'CL','alpha3' =>'CHL', 'iso' => '152'),
			  'CM' => array (  'dial_code' => '237', 'name' => 'Cameroon', 'id' => 'CM','alpha3' =>'CMR', 'iso' => '120'),
			  'CN' => array (  'dial_code' => '86', 'name' => 'China', 'id' => 'CN','alpha3' =>'CHN', 'iso' => '156'),
			  'CO' => array (  'dial_code' => '57', 'name' => 'Colombia', 'id' => 'CO','alpha3' =>'COL', 'iso' => '170'),
			  'CR' => array (  'dial_code' => '506', 'name' => 'Costa Rica', 'id' => 'CR','alpha3' =>'CRI', 'iso' => '188'),
			  'CU' => array (  'dial_code' => '53', 'name' => 'Cuba', 'id' => 'CU','alpha3' =>'CUB', 'iso' => '192'),
			  'CV' => array (  'dial_code' => '238', 'name' => 'Cabo Verde', 'id' => 'CV','alpha3' =>'CPV', 'iso' => '132'),
			  'CW' => array (  'dial_code' => '599', 'name' => 'Curasao', 'id' => 'CW', 'alpha3' => 'CUW', 'iso' => '531'),
			  'CX' => array (  'dial_code' => '61', 'name' => 'Christmas Island', 'id' => 'CX','alpha3' =>'CXR', 'iso' => '162'),
			  'CY' => array (  'dial_code' => '357', 'name' => 'Cyprus', 'id' => 'CY', 'alpha3' => 'CYP', 'iso' => '196'),
			  'CZ' => array (  'dial_code' => '420', 'name' => 'Czech Republic', 'id' => 'CZ','alpha3' => 'CZE', 'iso' => '203'),
			  'DE' => array (  'dial_code' => '49', 'name' => 'Germany', 'id' => 'DE', 'alpha3' =>'DEU', 'iso' => '276'),
			  'DJ' => array (  'dial_code' => '253', 'name' => 'Djibouti', 'id' => 'DJ', 'alpha3' => 'DJI', 'iso' => '262'),
			  'DK' => array (  'dial_code' => '45', 'name' => 'Denmark', 'id' => 'DK','alpha3' => 'DNK', 'iso' => '208'),
			  'DM' => array (  'dial_code' => '1767', 'name' => 'Dominica', 'id' => 'DM','alpha3' =>'DMA', 'iso' => '212'),
			  'DO' => array (  'dial_code' => '1809', 'name' => 'Dominican Republic', 'id' => 'DO','alpha3' => 'DOM', 'iso' => '214'),
			  'DZ' => array (  'dial_code' => '213', 'name' => 'Algeria', 'id' => 'DZ', 'alpha3' =>'DZA', 'iso' => '012'),
			  'EC' => array (  'dial_code' => '593', 'name' => 'Ecuador', 'id' => 'EC', 'alpha3' => 'ECU', 'iso' => '218'),
			  'EE' => array (  'dial_code' => '372', 'name' => 'Estonia', 'id' => 'EE', 'alpha3' => 'EST', 'iso' => '233'),
			  'EG' => array (  'dial_code' => '20', 'name' => 'Egypt', 'id' => 'EG', 'alpha3' => 'EGY', 'iso' => '818'),
			  'EH' => array (  'dial_code' => '212', 'name' => 'Western Sahara', 'id'=> 'EH', 'alpha3' => 'ESH', 'iso' => '732'),
			  'ER' => array (  'dial_code' => '291', 'name' => 'Eritrea', 'id' => 'ER', 'alpha3' => 'ERI', 'iso' => '232'),
			  'ES' => array (  'dial_code' => '34', 'name' => 'Spain', 'id' => 'ES', 'alpha3' =>'ESP', 'iso' => '724'),
			  'ET' => array (  'dial_code' => '251', 'name' => 'Ethiopia', 'id' => 'ET', 'alpha3' => 'ETH', 'iso' => '231'),
			  'FI' => array (  'dial_code' => '358', 'name' => 'Finland', 'id' => 'FI','alpha3' => 'FIN', 'iso' => '246'),
			  'FJ' => array (  'dial_code' => '679', 'name' => 'Fiji', 'id' => 'FJ','alpha3' =>'FJI', 'iso' => '242'),
			  'FK' => array (  'dial_code' => '500', 'name' => 'Falkland Islands (malvinas)', 'id' => 'FK','alpha3' =>'FLK', 'iso' => '238'),
			  'FM' => array (  'dial_code' => '691', 'name' => 'Micronesia, Federated States Of', 'id' => 'FM','alpha3' =>'FSM', 'iso' => '583'),
			  'FO' => array (  'dial_code' => '298', 'name' => 'Faroe Islands', 'id' => 'FO','alpha3' =>'FRO', 'iso' => '234'),
			  'FR' => array (  'dial_code' => '33', 'name' => 'France', 'id' => 'FR','alpha3' =>'FRA', 'iso' => '250'),
			  'GA' => array (  'dial_code' => '241', 'name' => 'Gabon', 'id' => 'GA','alpha3' =>'GAB', 'iso' => '266'),
			  'GB' => array (  'dial_code' => '44', 'name' => 'United Kingdom', 'id' => 'GB','alpha3' =>'GBR', 'iso' => '826'),
              'GD' => array (  'dial_code' => '1473', 'name' => 'Grenada', 'id' => 'GD','alpha3' =>'GRD', 'iso' => '308'),
			  'GE' => array (  'dial_code' => '995', 'name' => 'Georgia', 'id' => 'GE','alpha3' =>'GEO', 'iso' => '268'),
			  'GF' => array (  'dial_code' => '594', 'name' => 'French Guiana', 'id' => 'GF', 'alpha3' => 'GUF', 'iso' => '254'),
			  'GH' => array (  'dial_code' => '233', 'name' => 'Ghana', 'id' => 'GH','alpha3' =>'GHA', 'iso' => '288'),
			  'GI' => array (  'dial_code' => '350', 'name' => 'Gibraltar', 'id' => 'GI','alpha3' =>'GIB', 'iso' => '292'),
			  'GL' => array (  'dial_code' => '299', 'name' => 'Greenland', 'id' => 'GL','alpha3' =>'GRL', 'iso' => '304'),
			  'GM' => array (  'dial_code' => '220', 'name' => 'Gambia', 'id' => 'GM','alpha3' =>'GMB', 'iso' => '270'),
			  'GN' => array (  'dial_code' => '224', 'name' => 'Guinea', 'id' => 'GN','alpha3' =>'GIN', 'iso' => '324'),
			  'GP' => array (  'dial_code' => '590', 'name' => 'Guadeloupe', 'id' => 'GP', 'alpha3' => 'GLP', 'iso' =>'312'),
			  'GS' => array (  'dial_code' => '500', 'name' => 'South Georgia and the South Sandwich Islands' , 'id' => 'GS', 'alpha3' => 'SGS', 'iso' => '239'),
			  'GQ' => array (  'dial_code' => '240', 'name' => 'Equatorial Guinea', 'id' => 'GQ','alpha3' =>'GNQ', 'iso' => '226'),
			  'GR' => array (  'dial_code' => '30', 'name' => 'Greece', 'id' => 'GR','alpha3' =>'GRC', 'iso' => '300'),
			  'GT' => array (  'dial_code' => '502', 'name' => 'Guatemala', 'id' => 'GT','alpha3' =>'GTM', 'iso' => '320'),
			  'GU' => array (  'dial_code' => '1671', 'name' => 'Guam', 'id' => 'GU','alpha3' =>'GUM', 'iso' => '316'),
			  'GW' => array (  'dial_code' => '245', 'name' => 'Guinea-bissau', 'id' => 'GW','alpha3' =>'GNB', 'iso' => '624'),
			  'GY' => array (  'dial_code' => '592', 'name' => 'Guyana', 'id' => 'GY','alpha3' =>'GUY', 'iso' => '328'),
			  'HK' => array (  'dial_code' => '852', 'name' => 'Hong Kong', 'id' => 'HK','alpha3' =>'HKG', 'iso' => '344'),
			  'HM' => array (  'dial_code' => '672', 'name' => 'Heard Island and McDonald Islands', 'id' => 'HM', 'alpha3' => 'HMD', 'iso'=>'334'),
			  'HN' => array (  'dial_code' => '504', 'name' => 'Honduras', 'id' => 'HN','alpha3' =>'HND', 'iso' => '340'),
			  'HR' => array (  'dial_code' => '385', 'name' => 'Croatia', 'id' => 'HR','alpha3' =>'HRV', 'iso' => '191'),
			  'HT' => array (  'dial_code' => '509', 'name' => 'Haiti', 'id' => 'HT','alpha3' =>'HTI', 'iso' => '332'),
			  'HU' => array (  'dial_code' => '36', 'name' => 'Hungary', 'id' => 'HU','alpha3' =>'HUN', 'iso' => '348'),
			  'ID' => array (  'dial_code' => '62', 'name' => 'Indonesia', 'id' => 'ID','alpha3' =>'IDN', 'iso' => '360'),
			  'IO' => array (  'dial_code' => '246', 'name' => 'British Indian Ocean Territory', 'id' => 'IO', 'alpha' =>'IOT', 'iso'=> '086' ),
			  'IE' => array (  'dial_code' => '353', 'name' => 'Ireland', 'id' => 'IE','alpha3' =>'IRL', 'iso' => '372'),
			  'IL' => array (  'dial_code' => '972', 'name' => 'Israel', 'id' => 'IL','alpha3' =>'ISR', 'iso' => '376'),
			  'IN' => array (  'dial_code' => '91', 'name' => 'India', 'id' => 'IN','alpha3' =>'IND', 'iso' => '356'),
			  'IQ' => array (  'dial_code' => '964', 'name' => 'Iraq', 'id' => 'IQ','alpha3' =>'IRQ', 'iso' => '368'),
			  'IR' => array (  'dial_code' => '98', 'name' => 'Iran, Islamic Republic Of', 'id' => 'IR','alpha3' =>'IRN', 'iso' => '364'),
			  'IS' => array (  'dial_code' => '354', 'name' => 'Iceland', 'id' => 'IS','alpha3' =>'ISL', 'iso' => '352'),
			  'IT' => array (  'dial_code' => '39', 'name' => 'Italy', 'id' => 'IT','alpha3' =>'ITA', 'iso' => '380'),
			  'JM' => array (  'dial_code' => '1876', 'name' => 'Jamaica', 'id' => 'JM','alpha3' =>'JAM', 'iso' => '388'),
			  'JO' => array (  'dial_code' => '962', 'name' => 'Jordan', 'id' => 'JO','alpha3' =>'JOR', 'iso' => '400'),
			  'JP' => array (  'dial_code' => '81', 'name' => 'Japan', 'id' => 'JP','alpha3' =>'JPN', 'iso' => '392'),
			  'KE' => array (  'dial_code' => '254', 'name' => 'Kenya', 'id' => 'KE','alpha3' =>'KEN', 'iso' => '404'),
			  'KG' => array (  'dial_code' => '996', 'name' => 'Kyrgyzstan', 'id' => 'KG','alpha3' =>'KGZ', 'iso' => '417'),
			  'KH' => array (  'dial_code' => '855', 'name' => 'Cambodia', 'id' => 'KH','alpha3' =>'KHM', 'iso' => '116'),
			  'KI' => array (  'dial_code' => '686', 'name' => 'Kiribati', 'id' => 'KI','alpha3' =>'KIR', 'iso' => '296'),
			  'KM' => array (  'dial_code' => '269', 'name' => 'Comoros', 'id' => 'KM','alpha3' =>'COM', 'iso' => '174'),
			  'KN' => array (  'dial_code' => '1869', 'name' => 'Saint Kitts And Nevis', 'id' => 'KN','alpha3' =>'KNA', 'iso' => '659'),
			  'KP' => array (  'dial_code' => '850', 'name' => 'Korea Democratic Peoples Republic Of', 'id' => 'KP','alpha3' =>'PRK', 'iso' => '408'),
			  'KR' => array (  'dial_code' => '82', 'name' => 'Korea Republic Of', 'id' => 'KR','alpha3' =>'KOR', 'iso' => '410'),
			  'KW' => array (  'dial_code' => '965', 'name' => 'Kuwait', 'id' => 'KW','alpha3' =>'KWT', 'iso' => '414'),
			  'KY' => array (  'dial_code' => '1345', 'name' => 'Cayman Islands', 'id' => 'KY','alpha3' =>'CYM', 'iso' => '136'),
			  'KZ' => array (  'dial_code' => '7', 'name' => 'Kazakhstan', 'id' => 'KZ','alpha3' =>'KAZ', 'iso' => '398'),
			  'LA' => array (  'dial_code' => '856', 'name' => 'Lao Peoples Democratic Republic', 'id' => 'LA','alpha3' =>'LAO', 'iso' => '418'),
			  'LB' => array (  'dial_code' => '961', 'name' => 'Lebanon', 'id' => 'LB','alpha3' =>'LBN', 'iso' => '422'),
			  'LC' => array (  'dial_code' => '1758', 'name' => 'Saint Lucia', 'id' => 'LC','alpha3' =>'LCA', 'iso' => '662'),
			  'LI' => array (  'dial_code' => '423', 'name' => 'Liechtenstein', 'id' => 'LI','alpha3' =>'LIE', 'iso' => '438'),
			  'LK' => array (  'dial_code' => '94', 'name' => 'Sri Lanka', 'id' => 'LK','alpha3' =>'LKA', 'iso' => '144'),
			  'LR' => array (  'dial_code' => '231', 'name' => 'Liberia', 'id' => 'LR','alpha3' =>'LBR', 'iso' => '430'),
			  'LS' => array (  'dial_code' => '266', 'name' => 'Lesotho', 'id' => 'LS','alpha3' =>'LSO', 'iso' => '426'),
			  'LT' => array (  'dial_code' => '370', 'name' => 'Lithuania', 'id' => 'LT','alpha3' =>'LTU', 'iso' => '440'),
			  'LU' => array (  'dial_code' => '352', 'name' => 'Luxembourg', 'id' => 'LU','alpha3' =>'LUX', 'iso' => '442'),
			  'LV' => array (  'dial_code' => '371', 'name' => 'Latvia', 'id' => 'LV','alpha3' =>'LVA', 'iso' => '428'),
			  'LY' => array (  'dial_code' => '218', 'name' => 'Libyan Arab Jamahiriya', 'id' => 'LY','alpha3' =>'LBY', 'iso' => '434'),
			  'MA' => array (  'dial_code' => '212', 'name' => 'Morocco', 'id' => 'MA','alpha3' =>'MAR', 'iso' => '504'),
			  'MC' => array (  'dial_code' => '377', 'name' => 'Monaco', 'id' => 'MC','alpha3' =>'MCO', 'iso' => '492'),
			  'MD' => array (  'dial_code' => '373', 'name' => 'Moldova, Republic Of', 'id' => 'MD','alpha3' =>'MDA', 'iso' => '498'),
			  'ME' => array (  'dial_code' => '382', 'name' => 'Montenegro', 'id' => 'ME','alpha3' =>'MNE', 'iso' => '499'),
			  'MF' => array (  'dial_code' => '1599', 'name' => 'Saint Martin', 'id' => 'MF','alpha3' =>'MAF', 'iso' => '663'),
			  'MG' => array (  'dial_code' => '261', 'name' => 'Madagascar', 'id' => 'MG','alpha3' =>'MDG', 'iso' => '450'),
			  'MH' => array (  'dial_code' => '692', 'name' => 'Marshall Islands', 'id' => 'MH','alpha3' =>'MHL', 'iso' => '584'),
			  'MK' => array (  'dial_code' => '389', 'name' => 'Republic of North Macedonia', 'id' => 'MK','alpha3' =>'MKD', 'iso' => '807'),
			  'ML' => array (  'dial_code' => '223', 'name' => 'Mali', 'id' => 'ML','alpha3' =>'MLI', 'iso' => '466'),
			  'MM' => array (  'dial_code' => '95', 'name' => 'Myanmar', 'id' => 'MM','alpha3' =>'MMR', 'iso' => '104'),
			  'MN' => array (  'dial_code' => '976', 'name' => 'Mongolia', 'id' => 'MN','alpha3' =>'MNG', 'iso' => '496'),
			  'MO' => array (  'dial_code' => '853', 'name' => 'Macao', 'id' => 'MO','alpha3' =>'MAC', 'iso' => '446'),
			  'MP' => array (  'dial_code' => '1670', 'name' => 'Northern Mariana Islands', 'id' => 'MP','alpha3' =>'MNP', 'iso' => '580'),
			  'MQ' => array (  'dial_code' => '596', 'name' => 'Martinique', 'id' => 'MQ', 'alpha3' => 'MTQ', 'iso' => '474'),
			  'MR' => array (  'dial_code' => '222', 'name' => 'Mauritania', 'id' => 'MR','alpha3' =>'MRT', 'iso' => '478'),
			  'MS' => array (  'dial_code' => '1664', 'name' => 'Montserrat', 'id' => 'MS','alpha3' =>'MSR', 'iso' => '500'),
			  'MT' => array (  'dial_code' => '356', 'name' => 'Malta', 'id' => 'MT','alpha3' =>'MLT', 'iso' => '470'),
			  'MU' => array (  'dial_code' => '230', 'name' => 'Mauritius', 'id' => 'MU','alpha3' =>'MUS', 'iso' => '480'),
			  'MV' => array (  'dial_code' => '960', 'name' => 'Maldives', 'id' => 'MV','alpha3' =>'MDV', 'iso' => '462'),
			  'MW' => array (  'dial_code' => '265', 'name' => 'Malawi', 'id' => 'MW','alpha3' =>'MWI', 'iso' => '454'),
			  'MX' => array (  'dial_code' => '52', 'name' => 'Mexico', 'id' => 'MX','alpha3' =>'MEX', 'iso' => '484'),
			  'MY' => array (  'dial_code' => '60', 'name' => 'Malaysia', 'id' => 'MY', 'alpha3' =>'MYS', 'iso' => '458'),
			  'MZ' => array (  'dial_code' => '258', 'name' => 'Mozambique', 'id' => 'MZ','alpha3' =>'MOZ', 'iso' => '508'),
			  'NA' => array (  'dial_code' => '264', 'name' => 'Namibia', 'id' => 'NA','alpha3' =>'NAM', 'iso' => '516'),
			  'NC' => array (  'dial_code' => '687', 'name' => 'New Caledonia', 'id' => 'NC','alpha3' =>'NCL', 'iso' => '540'),
			  'NE' => array (  'dial_code' => '227', 'name' => 'Niger', 'id' => 'NE','alpha3' =>'NER', 'iso' => '562'),
			  'NF' => array (  'dial_code' => '672', 'name' => 'Norfolk Island', 'id' => 'NF', 'alpha3' => 'NFK', 'iso'=> '574'),
			  'NG' => array (  'dial_code' => '234', 'name' => 'Nigeria', 'id' => 'NG','alpha3' =>'NGA', 'iso' => '566'),
			  'NI' => array (  'dial_code' => '505', 'name' => 'Nicaragua', 'id' => 'NI','alpha3' =>'NIC', 'iso' => '558'),
			  'NL' => array (  'dial_code' => '31', 'name' => 'Netherlands', 'id' => 'NL','alpha3' =>'NLD', 'iso' => '528'),
			  'NO' => array (  'dial_code' => '47', 'name' => 'Norway', 'id' => 'NO','alpha3' =>'NOR', 'iso' => '578'),
			  'NP' => array (  'dial_code' => '977', 'name' => 'Nepal', 'id' => 'NP','alpha3' =>'NPL', 'iso' => '524'),
			  'NR' => array (  'dial_code' => '674', 'name' => 'Nauru', 'id' => 'NR','alpha3' =>'NRU', 'iso' => '520'),
			  'NU' => array (  'dial_code' => '683', 'name' => 'Niue', 'id' => 'NU','alpha3' =>'NIU', 'iso' => '570'),
			  'NZ' => array (  'dial_code' => '64', 'name' => 'New Zealand', 'id' => 'NZ','alpha3' =>'NZL', 'iso' => '554'),
			  'OM' => array (  'dial_code' => '968', 'name' => 'Oman', 'id' => 'OM','alpha3' =>'OMN', 'iso' => '512'),
			  'PA' => array (  'dial_code' => '507', 'name' => 'Panama', 'id' => 'PA','alpha3' =>'PAN', 'iso' => '591'),
			  'PE' => array (  'dial_code' => '51', 'name' => 'Peru', 'id' => 'PE','alpha3' =>'PER', 'iso' => '604'),
			  'PF' => array (  'dial_code' => '689', 'name' => 'French Polynesia', 'id' => 'PF','alpha3' =>'PYF', 'iso' => '258'),
			  'PG' => array (  'dial_code' => '675', 'name' => 'Papua New Guinea', 'id' => 'PG','alpha3' =>'PNG', 'iso' => '598'),
			  'PH' => array (  'dial_code' => '63', 'name' => 'Philippines', 'id' => 'PH','alpha3' =>'PHL', 'iso' => '608'),
			  'PK' => array (  'dial_code' => '92', 'name' => 'Pakistan', 'id' => 'PK','alpha3' =>'PAK', 'iso' => '586'),
			  'PL' => array (  'dial_code' => '48', 'name' => 'Poland', 'id' => 'PL','alpha3' =>'POL', 'iso' => '616'),
			  'PM' => array (  'dial_code' => '508', 'name' => 'Saint Pierre And Miquelon', 'id' => 'PM','alpha3' =>'SPM', 'iso' => '666'),
			  'PN' => array (  'dial_code' => '870', 'name' => 'Pitcairn', 'id' => 'PN','alpha3' =>'PCN', 'iso' => '612'),
			  'PR' => array (  'dial_code' => '1', 'name' => 'Puerto Rico', 'id' => 'PR','alpha3' =>'PRI', 'iso' => '630'),
			  'PS' => array (  'dial_code' => '970', 'name' => 'Palestine', 'id' => 'PS', 'alpha3'=> 'PSE', 'iso' => '275'),
			  'PT' => array (  'dial_code' => '351', 'name' => 'Portugal', 'id' => 'PT','alpha3' =>'PRT', 'iso' => '620'),
			  'PW' => array (  'dial_code' => '680', 'name' => 'Palau', 'id' => 'PW','alpha3' =>'PLW', 'iso' => '585'),
			  'PY' => array (  'dial_code' => '595', 'name' => 'Paraguay', 'id' => 'PY','alpha3' =>'PRY', 'iso' => '600'),
			  'QA' => array (  'dial_code' => '974', 'name' => 'Qatar', 'id' => 'QA','alpha3' =>'QAT', 'iso' => '634'),
			  'RE' => array (  'dial_code' => '262', 'name' => 'Reunion', 'id' => 'RE', 'alpha3' => 'REU', 'iso' => '638'),
			  'RO' => array (  'dial_code' => '40', 'name' => 'Romania', 'id' => 'RO','alpha3' =>'ROU', 'iso' => '642'),
			  'RS' => array (  'dial_code' => '381', 'name' => 'Serbia', 'id' => 'RS','alpha3' =>'SRB', 'iso' => '688'),
			  'RU' => array (  'dial_code' => '7', 'name' => 'Russian Federation', 'id' => 'RU','alpha3' =>'RUS', 'iso' => '643'),
			  'RW' => array (  'dial_code' => '250', 'name' => 'Rwanda', 'id' => 'RW','alpha3' =>'RWA', 'iso' => '646'),
			  'SA' => array (  'dial_code' => '966', 'name' => 'Saudi Arabia', 'id' => 'SA','alpha3' =>'SAU', 'iso' => '682'),
			  'SB' => array (  'dial_code' => '677', 'name' => 'Solomon Islands', 'id' => 'SB','alpha3' =>'SLB', 'iso' => '090'),
			  'SC' => array (  'dial_code' => '248', 'name' => 'Seychelles', 'id' => 'SC','alpha3' =>'SYC', 'iso' => '690'),
			  'SD' => array (  'dial_code' => '249', 'name' => 'Sudan', 'id' => 'SD','alpha3' =>'SDN', 'iso' => '729'),
			  'SE' => array (  'dial_code' => '46', 'name' => 'Sweden', 'id' => 'SE','alpha3' =>'SWE', 'iso' => '752'),
			  'SG' => array (  'dial_code' => '65', 'name' => 'Singapore', 'id' => 'SG','alpha3' =>'SGP', 'iso' => '702'),
			  'SH' => array (  'dial_code' => '290', 'name' => 'Saint Helena', 'id' => 'SH','alpha3' =>'SHN', 'iso' => '654'),
			  'SI' => array (  'dial_code' => '386', 'name' => 'Slovenia', 'id' => 'SI','alpha3' =>'SVN', 'iso' => '705'),
			  'SJ' => array (  'dial_code' => '47', 'name' => 'Svalbard and Jan Mayen', 'id' => 'SJ', 'alpha3' => 'SJM', 'iso' => '744'),
			  'SK' => array (  'dial_code' => '421', 'name' => 'Slovakia', 'id' => 'SK','alpha3' =>'SVK', 'iso' => '703'),
			  'SL' => array (  'dial_code' => '232', 'name' => 'Sierra Leone', 'id' => 'SL','alpha3' =>'SLE', 'iso' => '694'),
			  'SM' => array (  'dial_code' => '378', 'name' => 'San Marino', 'id' => 'SM','alpha3' =>'SMR', 'iso' => '674'),
			  'SN' => array (  'dial_code' => '221', 'name' => 'Senegal', 'id' => 'SN','alpha3' =>'SEN', 'iso' => '686'),
			  'SO' => array (  'dial_code' => '252', 'name' => 'Somalia', 'id' => 'SO','alpha3' =>'SOM', 'iso' => '706'),
			  'SR' => array (  'dial_code' => '597', 'name' => 'Suriname', 'id' => 'SR','alpha3' =>'SUR', 'iso' => '740'),
			  'SS' => array (  'dial_code' => '211',  'name' => 'South Sudan', 'id' => 'SS', 'alpha3' => 'SSD', 'iso' => '728'),
			  'ST' => array (  'dial_code' => '239', 'name' => 'Sao Tome And Principe', 'id' => 'ST','alpha3' =>'STP', 'iso' => '678'),
			  'SV' => array (  'dial_code' => '503', 'name' => 'El Salvador', 'id' => 'SV','alpha3' =>'SLV', 'iso' => '222'),
			  'SY' => array (  'dial_code' => '963', 'name' => 'Syrian Arab Republic', 'id' => 'SY','alpha3' =>'SYR', 'iso' => '760'),
			  'SZ' => array (  'dial_code' => '268', 'name' => 'Eswatini', 'id' => 'SZ','alpha3' =>'SWZ', 'iso' => '748'),
			  'TC' => array (  'dial_code' => '1649', 'name' => 'Turks And Caicos Islands', 'id' => 'TC','alpha3' =>'TCA', 'iso' => '796'),
			  'TD' => array (  'dial_code' => '235', 'name' => 'Chad', 'id' => 'TD','alpha3' =>'TCD', 'iso' => '148'),
              'TF' => array (  'dial_code' => '262', 'name' => 'French Southern Territories', 'id' => 'TF', 'alpha3'=> 'ATF', 'iso' => '260' ),
			  'TG' => array (  'dial_code' => '228', 'name' => 'Togo', 'id' => 'TG','alpha3' =>'TGO', 'iso' => '768'),
			  'TH' => array (  'dial_code' => '66', 'name' => 'Thailand', 'id' => 'TH','alpha3' =>'THA', 'iso' => '764'),
			  'TJ' => array (  'dial_code' => '992', 'name' => 'Tajikistan', 'id' => 'TJ','alpha3' =>'TJK', 'iso' => '762'),
			  'TK' => array (  'dial_code' => '690', 'name' => 'Tokelau', 'id' => 'TK','alpha3' =>'TKL', 'iso' => '772'),
			  'TL' => array (  'dial_code' => '670', 'name' => 'Timor-Leste', 'id' => 'TL','alpha3' =>'TLS', 'iso' => '626'),
			  'TM' => array (  'dial_code' => '993', 'name' => 'Turkmenistan', 'id' => 'TM','alpha3' =>'TKM', 'iso' => '795'),
			  'TN' => array (  'dial_code' => '216', 'name' => 'Tunisia', 'id' => 'TN','alpha3' =>'TUN', 'iso' => '788'),
			  'TO' => array (  'dial_code' => '676', 'name' => 'Tonga', 'id' => 'TO','alpha3' =>'TON', 'iso' => '776'),
			  'TR' => array (  'dial_code' => '90', 'name' => 'Turkey', 'id' => 'TR','alpha3' =>'TUR', 'iso' => '792'),
			  'TT' => array (  'dial_code' => '1868', 'name' => 'Trinidad And Tobago', 'id' => 'TT','alpha3' =>'TTO', 'iso' => '780'),
			  'TV' => array (  'dial_code' => '688', 'name' => 'Tuvalu', 'id' => 'TV','alpha3' =>'TUV', 'iso' => '798'),
			  'TW' => array (  'dial_code' => '886', 'name' => 'Taiwan, Province Of China', 'id' => 'TW','alpha3' =>'TWN', 'iso' => '158'),
			  'TZ' => array (  'dial_code' => '255', 'name' => 'Tanzania, United Republic Of', 'id' => 'TZ','alpha3' =>'TZA', 'iso' => '834'),
			  'UA' => array (  'dial_code' => '380', 'name' => 'Ukraine', 'id' => 'UA','alpha3' =>'UKR', 'iso' => '804'),
			  'UG' => array (  'dial_code' => '256', 'name' => 'Uganda', 'id' => 'UG','alpha3' =>'UGA', 'iso' => '800'),
			  'UM' => array (  'dial_code' => '246' ,'name' => 'United States Minor Outlying Islands', 'id' => 'UM', 'alpha3' => 'UMI', 'iso' => '581'),
			  'US' => array (  'dial_code' => '1', 'name' => 'United States of America', 'id' => 'US','alpha3' =>'USA', 'iso' => '840'),
			  'UY' => array (  'dial_code' => '598', 'name' => 'Uruguay', 'id' => 'UY','alpha3' =>'URY', 'iso' => '858'),
			  'UZ' => array (  'dial_code' => '998', 'name' => 'Uzbekistan', 'id' => 'UZ','alpha3' =>'UZB', 'iso' => '860'),
			  'VA' => array (  'dial_code' => '39', 'name' => 'Holy See (vatican City State)', 'id' => 'VA','alpha3' =>'VAT', 'iso' => '336'),
			  'VC' => array (  'dial_code' => '1784', 'name' => 'Saint Vincent And The Grenadines', 'id' => 'VC','alpha3' =>'VCT', 'iso' => '670'),
			  'VE' => array (  'dial_code' => '58', 'name' => 'Venezuela', 'id' => 'VE','alpha3' =>'VEN', 'iso' => '862'),
			  'VG' => array (  'dial_code' => '1284', 'name' => 'Virgin Islands, British', 'id' => 'VG','alpha3' =>'VGB', 'iso'=>'092'),
			  'VI' => array (  'dial_code' => '1340', 'name' => 'Virgin Islands, U.s.', 'id' => 'VI','alpha3' =>'VIR', 'iso' => '850'),
			  'VN' => array (  'dial_code' => '84', 'name' => 'Viet Nam', 'id' => 'VN','alpha3' =>'VNM', 'iso' => '704'),
			  'VU' => array (  'dial_code' => '678', 'name' => 'Vanuatu', 'id' => 'VU','alpha3' =>'VUT', 'iso' => '548'),
			  'WF' => array (  'dial_code' => '681', 'name' => 'Wallis And Futuna', 'id' => 'WF','alpha3' =>'WLF', 'iso' => '876'),
			  'WS' => array (  'dial_code' => '685', 'name' => 'Samoa', 'id' => 'WS','alpha3' =>'WSM', 'iso' => '882'),
			  'YE' => array (  'dial_code' => '967', 'name' => 'Yemen', 'id' => 'YE','alpha3' =>'YEM', 'iso' => '887'),
			  'YT' => array (  'dial_code' => '262', 'name' => 'Mayotte', 'id' => 'YT','alpha3' =>'MYT', 'iso' => '175'),
			  'ZA' => array (  'dial_code' => '27', 'name' => 'South Africa', 'id' => 'ZA','alpha3' =>'ZAF', 'iso' => '710'),
			  'ZM' => array (  'dial_code' => '260', 'name' => 'Zambia', 'id' => 'ZM','alpha3' =>'ZMB', 'iso' => '894'),
			  'ZW' => array (  'dial_code' => '263', 'name' => 'Zimbabwe', 'id' => 'ZW','alpha3' =>'ZWE', 'iso' => '716'),

			);
	public static $alpha_countries =
			array (
			  'AND' => array (  'dial_code' => '376', 'name' => 'Andorra', 'id' => 'AD', 'alpha3' =>'AND', 'iso' => '020'),
			  'ARE' => array (  'dial_code' => '971', 'name' => 'United Arab Emirates', 'id' => 'AE','alpha3' =>'ARE', 'iso' => '784'),
			  'AFG' => array (  'dial_code' => '93', 'name' => 'Afghanistan', 'id' => 'AF', 'alpha3' => 'AFG', 'iso' => '004'),
			  'ATG' => array (  'dial_code' => '1268', 'name' => 'Antigua And Barbuda', 'id' => 'AG','alpha3' =>'ATG', 'iso' => '028'),
			  'AIA' => array (  'dial_code' => '1264', 'name' => 'Anguilla', 'id' => 'AI','alpha3' =>'AIA', 'iso' => '660'),
			  'ALB' => array (  'dial_code' => '355', 'name' => 'Albania', 'id' => 'AL','alpha3' =>'ALB', 'iso' => '008'),
			  'ARM' => array (  'dial_code' => '374', 'name' => 'Armenia', 'id' => 'AM','alpha3' =>'ARM', 'iso' => '051'),
			  'ANT' => array (  'dial_code' => '599', 'name' => 'Netherlands Antilles', 'id' => 'AN','alpha3' =>'ANT', 'iso' => '530'),
			  'AGO' => array (  'dial_code' => '244', 'name' => 'Angola', 'id' => 'AO','alpha3' =>'AGO', 'iso' => '024'),
			  'ATA' => array (  'dial_code' => '672', 'name' => 'Antarctica', 'id' => 'AQ','alpha3' =>'ATA', 'iso' => '010'),
			  'ARG' => array (  'dial_code' => '54', 'name' => 'Argentina', 'id' => 'AR','alpha3' =>'ARG', 'iso' => '032'),
			  'ASM' => array (  'dial_code' => '1684', 'name' => 'American Samoa', 'id' => 'AS','alpha3' =>'ASM', 'iso' => '016'),
			  'AUT' => array (  'dial_code' => '43', 'name' => 'Austria', 'id' => 'AT','alpha3' =>'AUT', 'iso' => '040'),
			  'AUS' => array (  'dial_code' => '61', 'name' => 'Australia', 'id' => 'AU','alpha3' =>'AUS', 'iso' => '036'),
			  'ABW' => array (  'dial_code' => '297', 'name' => 'Aruba', 'id' => 'AW', 'alpha3' =>'ABW', 'iso' => '533'),
			  'AZE' => array (  'dial_code' => '994', 'name' => 'Azerbaijan', 'id' => 'AZ','alpha3' =>'AZE', 'iso' => '031'),
			  'BIH' => array (  'dial_code' => '387', 'name' => 'Bosnia And Herzegovina', 'id' => 'BA','alpha3' =>'BIH', 'iso' => '070'),
			  'BRB' => array (  'dial_code' => '1246', 'name' => 'Barbados', 'id' => 'BB','alpha3' =>'BRB', 'iso' => '052'),
			  'BGD' => array (  'dial_code' => '880', 'name' => 'Bangladesh', 'id' => 'BD','alpha3' =>'BGD', 'iso' => '050'),
			  'BEL' => array (  'dial_code' => '32', 'name' => 'Belgium', 'id' => 'BE','alpha3' =>'BEL', 'iso' => '056'),
			  'BFA' => array (  'dial_code' => '226', 'name' => 'Burkina Faso', 'id' => 'BF','alpha3' =>'BFA', 'iso' => '854'),
			  'BGR' => array (  'dial_code' => '359', 'name' => 'Bulgaria', 'id' => 'BG','alpha3' =>'BGR', 'iso' => '100'),
			  'BHR' => array (  'dial_code' => '973', 'name' => 'Bahrain', 'id' => 'BH','alpha3' =>'BHR', 'iso' => '048'),
			  'BDI' => array (  'dial_code' => '257', 'name' => 'Burundi', 'id' => 'BI','alpha3' =>'BDI', 'iso' => '108'),
			  'BEN' => array (  'dial_code' => '229', 'name' => 'Benin', 'id' => 'BJ','alpha3' =>'BEN', 'iso' => '204'),
			  'BLM' => array (  'dial_code' => '590', 'name' => 'Saint Barthelemy', 'id' => 'BL','alpha3' =>'BLM', 'iso' => '652'),
			  'BMU' => array (  'dial_code' => '1441', 'name' => 'Bermuda', 'id' => 'BM','alpha3' =>'BMU', 'iso' => '060'),
			  'BRN' => array (  'dial_code' => '673', 'name' => 'Brunei Darussalam', 'id' => 'BN','alpha3' =>'BRN', 'iso' => '096'),
			  'BOL' => array (  'dial_code' => '591', 'name' => 'Bolivia', 'id' => 'BO','alpha3' =>'BOL', 'iso' => '068'),
			  'BRA' => array (  'dial_code' => '55', 'name' => 'Brazil', 'id' => 'BR','alpha3' =>'BRA', 'iso' => '076'),
			  'BHS' => array (  'dial_code' => '1242', 'name' => 'Bahamas', 'id' => 'BS','alpha3' =>'BHS', 'iso' => '044'),
			  'BTN' => array (  'dial_code' => '975', 'name' => 'Bhutan', 'id' => 'BT','alpha3' =>'BTN', 'iso' => '064'),
			  'BWA' => array (  'dial_code' => '267', 'name' => 'Botswana', 'id' => 'BW','alpha3' =>'BWA', 'iso' => '072'),
			  'BLR' => array (  'dial_code' => '375', 'name' => 'Belarus', 'id' => 'BY','alpha3' =>'BLR', 'iso' => '112'),
			  'BLZ' => array (  'dial_code' => '501', 'name' => 'Belize', 'id' => 'BZ','alpha3' =>'BLZ', 'iso' => '084'),
			  'CAN' => array (  'dial_code' => '1', 'name' => 'Canada', 'id' => 'CA','alpha3' =>'CAN', 'iso' => '124'),
			  'CCK' => array (  'dial_code' => '61', 'name' => 'Cocos (keeling) Islands', 'id' => 'CC','alpha3' =>'CCK', 'iso' => '166'),
			  'COD' => array (  'dial_code' => '243', 'name' => 'Congo, The Democratic Republic Of The', 'id' => 'CD','alpha3' =>'COD', 'iso' => '180'),
			  'CAF' => array (  'dial_code' => '236', 'name' => 'Central African Republic', 'id' => 'CF','alpha3' =>'CAF', 'iso' => '140'),
			  'COG' => array (  'dial_code' => '242', 'name' => 'Congo', 'id' => 'CG','alpha3' =>'COG', 'iso' => '178'),
			  'CHE' => array (  'dial_code' => '41', 'name' => 'Switzerland', 'id' => 'CH','alpha3' =>'CHE', 'iso' => '756'),
			  'CIV' => array (  'dial_code' => '225', 'name' => 'Cote D Ivoire', 'id' => 'CI','alpha3' =>'CIV', 'iso' => '384'),
			  'COK' => array (  'dial_code' => '682', 'name' => 'Cook Islands', 'id' => 'CK','alpha3' =>'COK', 'iso' => '184'),
			  'CHL' => array (  'dial_code' => '56', 'name' => 'Chile', 'id' => 'CL','alpha3' =>'CHL', 'iso' => '152'),
			  'CMR' => array (  'dial_code' => '237', 'name' => 'Cameroon', 'id' => 'CM','alpha3' =>'CMR', 'iso' => '120'),
			  'CHN' => array (  'dial_code' => '86', 'name' => 'China', 'id' => 'CN','alpha3' =>'CHN', 'iso' => '156'),
			  'COL' => array (  'dial_code' => '57', 'name' => 'Colombia', 'id' => 'CO','alpha3' =>'COL', 'iso' => '170'),
			  'CRI' => array (  'dial_code' => '506', 'name' => 'Costa Rica', 'id' => 'CR','alpha3' =>'CRI', 'iso' => '188'),
			  'CUB' => array (  'dial_code' => '53', 'name' => 'Cuba', 'id' => 'CU','alpha3' =>'CUB', 'iso' => '192'),
			  'CPV' => array (  'dial_code' => '238', 'name' => 'Cabo Verde', 'id' => 'CV','alpha3' =>'CPV', 'iso' => '132'),
              'CUW' => array (  'dial_code' => '599', 'name' => 'Curasao', 'id' => 'CW', 'alpha3' => 'CUW', 'iso' => '531'),
              'CXR' => array (  'dial_code' => '61', 'name' => 'Christmas Island', 'id' => 'CX','alpha3' =>'CXR', 'iso' => '162'),
			  'CYP' => array (  'dial_code' => '357', 'name' => 'Cyprus', 'id' => 'CY', 'alpha3' => 'CYP', 'iso' => '196'),
			  'CZE' => array (  'dial_code' => '420', 'name' => 'Czech Republic', 'id' => 'CZ','alpha3' => 'CZE', 'iso' => '203'),
			  'DEU' => array (  'dial_code' => '49', 'name' => 'Germany', 'id' => 'DE', 'alpha3' =>'DEU', 'iso' => '276'),
			  'DJI' => array (  'dial_code' => '253', 'name' => 'Djibouti', 'id' => 'DJ', 'alpha3' => 'DJI', 'iso' => '262'),
			  'DNK' => array (  'dial_code' => '45', 'name' => 'Denmark', 'id' => 'DK','alpha3' => 'DNK', 'iso' => '208'),
			  'DMA' => array (  'dial_code' => '1767', 'name' => 'Dominica', 'id' => 'DM','alpha3' =>'DMA', 'iso' => '212'),
			  'DOM' => array (  'dial_code' => '1809', 'name' => 'Dominican Republic', 'id' => 'DO','alpha3' => 'DOM', 'iso' => '214'),
			  'DZA' => array (  'dial_code' => '213', 'name' => 'Algeria', 'id' => 'DZ', 'alpha3' =>'DZA', 'iso' => '012'),
			  'ECU' => array (  'dial_code' => '593', 'name' => 'Ecuador', 'id' => 'EC', 'alpha3' => 'ECU', 'iso' => '218'),
			  'EST' => array (  'dial_code' => '372', 'name' => 'Estonia', 'id' => 'EE', 'alpha3' => 'EST', 'iso' => '233'),
			  'EGY' => array (  'dial_code' => '20', 'name' => 'Egypt', 'id' => 'EG', 'alpha3' => 'EGY', 'iso' => '818'),
              'ESH' => array (  'dial_code' => '212', 'name' => 'Western Sahara', 'id'=> 'EH', 'alpha3' => 'ESH', 'iso' => '732'),
              'ERI' => array (  'dial_code' => '291', 'name' => 'Eritrea', 'id' => 'ER', 'alpha3' => 'ERI', 'iso' => '232'),
			  'ESP' => array (  'dial_code' => '34', 'name' => 'Spain', 'id' => 'ES', 'alpha3' =>'ESP', 'iso' => '724'),
			  'ETH' => array (  'dial_code' => '251', 'name' => 'Ethiopia', 'id' => 'ET', 'alpha3' => 'ETH', 'iso' => '231'),
			  'FIN' => array (  'dial_code' => '358', 'name' => 'Finland', 'id' => 'FI','alpha3' => 'FIN', 'iso' => '246'),
			  'FJI' => array (  'dial_code' => '679', 'name' => 'Fiji', 'id' => 'FJ','alpha3' =>'FJI', 'iso' => '242'),
			  'FLK' => array (  'dial_code' => '500', 'name' => 'Falkland Islands (malvinas)', 'id' => 'FK','alpha3' =>'FLK', 'iso' => '238'),
			  'FSM' => array (  'dial_code' => '691', 'name' => 'Micronesia, Federated States Of', 'id' => 'FM','alpha3' =>'FSM', 'iso' => '583'),
			  'FRO' => array (  'dial_code' => '298', 'name' => 'Faroe Islands', 'id' => 'FO','alpha3' =>'FRO', 'iso' => '234'),
			  'FRA' => array (  'dial_code' => '33', 'name' => 'France', 'id' => 'FR','alpha3' =>'FRA', 'iso' => '250'),
			  'GAB' => array (  'dial_code' => '241', 'name' => 'Gabon', 'id' => 'GA','alpha3' =>'GAB', 'iso' => '266'),
			  'GBR' => array (  'dial_code' => '44', 'name' => 'United Kingdom', 'id' => 'GB','alpha3' =>'GBR', 'iso' => '826'),
			  'UK'  => array (  'dial_code' => '44', 'name' => 'United Kingdom', 'id' => 'GB','alpha3' =>'GBR', 'iso' => '826'),
			  'ENG' => array (  'dial_code' => '44', 'name' => 'England', 'id' => 'ENG','alpha3' =>'ENG', 'iso' => '826'),
              'NIR' => array ( 'dial_code' => '44', 'name' => 'Northern Ireland', 'id' => 'NIR','alpha3' =>'NIR', 'iso' => '826'),
              'WLS' => array ( 'dial_code' => '44', 'name' => 'Wales', 'id' => 'WLS','alpha3' =>'WLS', 'iso' => '826'),
              'SCT' => array (  'dial_code' => '44', 'name' => 'United Kingdom', 'id' => 'SCT','alpha3' =>'SCT', 'iso' => '826'),
              'GRD' => array (  'dial_code' => '1473', 'name' => 'Grenada', 'id' => 'GD','alpha3' =>'GRD', 'iso' => '308'),
			  'GEO' => array (  'dial_code' => '995', 'name' => 'Georgia', 'id' => 'GE','alpha3' =>'GEO', 'iso' => '268'),
			  'GHA' => array (  'dial_code' => '233', 'name' => 'Ghana', 'id' => 'GH','alpha3' =>'GHA', 'iso' => '288'),
			  'GIB' => array (  'dial_code' => '350', 'name' => 'Gibraltar', 'id' => 'GI','alpha3' =>'GIB', 'iso' => '292'),
              'GLP' => array (  'dial_code' => '590', 'name' => 'Guadeloupe', 'id' => 'GP', 'alpha3' => 'GLP', 'iso' =>'312'),
              'GRL' => array (  'dial_code' => '299', 'name' => 'Greenland', 'id' => 'GL','alpha3' =>'GRL', 'iso' => '304'),
			  'GMB' => array (  'dial_code' => '220', 'name' => 'Gambia', 'id' => 'GM','alpha3' =>'GMB', 'iso' => '270'),
			  'GIN' => array (  'dial_code' => '224', 'name' => 'Guinea', 'id' => 'GN','alpha3' =>'GIN', 'iso' => '324'),
			  'GNQ' => array (  'dial_code' => '240', 'name' => 'Equatorial Guinea', 'id' => 'GQ','alpha3' =>'GNQ', 'iso' => '226'),
			  'GRC' => array (  'dial_code' => '30', 'name' => 'Greece', 'id' => 'GR','alpha3' =>'GRC', 'iso' => '300'),
			  'GTM' => array (  'dial_code' => '502', 'name' => 'Guatemala', 'id' => 'GT','alpha3' =>'GTM', 'iso' => '320'),
              'GUF' => array (  'dial_code' => '594', 'name' => 'French Guiana', 'id' => 'GF', 'alpha3' => 'GUF', 'iso' => '254'),
              'GUM' => array (  'dial_code' => '1671', 'name' => 'Guam', 'id' => 'GU','alpha3' =>'GUM', 'iso' => '316'),
			  'GNB' => array (  'dial_code' => '245', 'name' => 'Guinea-bissau', 'id' => 'GW','alpha3' =>'GNB', 'iso' => '624'),
			  'GUY' => array (  'dial_code' => '592', 'name' => 'Guyana', 'id' => 'GY','alpha3' =>'GUY', 'iso' => '328'),
			  'HKG' => array (  'dial_code' => '852', 'name' => 'Hong Kong', 'id' => 'HK','alpha3' =>'HKG', 'iso' => '344'),
              'HMD' => array (  'dial_code' => '672', 'name' => 'Heard Island and McDonald Islands', 'id' => 'HM', 'alpha3' => 'HMD', 'iso'=>'334'),
              'HND' => array (  'dial_code' => '504', 'name' => 'Honduras', 'id' => 'HN','alpha3' =>'HND', 'iso' => '340'),
			  'HRV' => array (  'dial_code' => '385', 'name' => 'Croatia', 'id' => 'HR','alpha3' =>'HRV', 'iso' => '191'),
			  'HTI' => array (  'dial_code' => '509', 'name' => 'Haiti', 'id' => 'HT','alpha3' =>'HTI', 'iso' => '332'),
			  'HUN' => array (  'dial_code' => '36', 'name' => 'Hungary', 'id' => 'HU','alpha3' =>'HUN', 'iso' => '348'),
			  'IDN' => array (  'dial_code' => '62', 'name' => 'Indonesia', 'id' => 'ID','alpha3' =>'IDN', 'iso' => '360'),
              'IOT' => array (  'dial_code' => '246', 'name' => 'British Indian Ocean Territory', 'id' => 'IO', 'alpha' =>'IOT', 'iso'=> '086' ),
              'IRL' => array (  'dial_code' => '353', 'name' => 'Ireland', 'id' => 'IE','alpha3' =>'IRL', 'iso' => '372'),
			  'ISR' => array (  'dial_code' => '972', 'name' => 'Israel', 'id' => 'IL','alpha3' =>'ISR', 'iso' => '376'),
			  'IMN' => array (  'dial_code' => '44', 'name' => 'Island Of Man', 'id' => 'IM','alpha3' =>'IMN', 'iso' => '833'),
			  'IND' => array (  'dial_code' => '91', 'name' => 'India', 'id' => 'IN','alpha3' =>'IND', 'iso' => '356'),
			  'IRQ' => array (  'dial_code' => '964', 'name' => 'Iraq', 'id' => 'IQ','alpha3' =>'IRQ', 'iso' => '368'),
			  'IRN' => array (  'dial_code' => '98', 'name' => 'Iran, Islamic Republic Of', 'id' => 'IR','alpha3' =>'IRN', 'iso' => '364'),
			  'ISL' => array (  'dial_code' => '354', 'name' => 'Iceland', 'id' => 'IS','alpha3' =>'ISL', 'iso' => '352'),
			  'ITA' => array (  'dial_code' => '39', 'name' => 'Italy', 'id' => 'IT','alpha3' =>'ITA', 'iso' => '380'),
			  'JAM' => array (  'dial_code' => '1876', 'name' => 'Jamaica', 'id' => 'JM','alpha3' =>'JAM', 'iso' => '388'),
			  'JOR' => array (  'dial_code' => '962', 'name' => 'Jordan', 'id' => 'JO','alpha3' =>'JOR', 'iso' => '400'),
			  'JPN' => array (  'dial_code' => '81', 'name' => 'Japan', 'id' => 'JP','alpha3' =>'JPN', 'iso' => '392'),
			  'KEN' => array (  'dial_code' => '254', 'name' => 'Kenya', 'id' => 'KE','alpha3' =>'KEN', 'iso' => '404'),
			  'KGZ' => array (  'dial_code' => '996', 'name' => 'Kyrgyzstan', 'id' => 'KG','alpha3' =>'KGZ', 'iso' => '417'),
			  'KHM' => array (  'dial_code' => '855', 'name' => 'Cambodia', 'id' => 'KH','alpha3' =>'KHM', 'iso' => '116'),
			  'KIR' => array (  'dial_code' => '686', 'name' => 'Kiribati', 'id' => 'KI','alpha3' =>'KIR', 'iso' => '296'),
			  'COM' => array (  'dial_code' => '269', 'name' => 'Comoros', 'id' => 'KM','alpha3' =>'COM', 'iso' => '174'),
			  'KNA' => array (  'dial_code' => '1869', 'name' => 'Saint Kitts And Nevis', 'id' => 'KN','alpha3' =>'KNA', 'iso' => '659'),
			  'PRK' => array (  'dial_code' => '850', 'name' => 'Korea Democratic Peoples Republic Of', 'id' => 'KP','alpha3' =>'PRK', 'iso' => '408'),
			  'KOR' => array (  'dial_code' => '82', 'name' => 'Korea Republic Of', 'id' => 'KR','alpha3' =>'KOR', 'iso' => '410'),
			  'KWT' => array (  'dial_code' => '965', 'name' => 'Kuwait', 'id' => 'KW','alpha3' =>'KWT', 'iso' => '414'),
			  'CYM' => array (  'dial_code' => '1345', 'name' => 'Cayman Islands', 'id' => 'KY','alpha3' =>'CYM', 'iso' => '136'),
			  'KAZ' => array (  'dial_code' => '7', 'name' => 'Kazakhstan', 'id' => 'KZ','alpha3' =>'KAZ', 'iso' => '398'),
			  'LAO' => array (  'dial_code' => '856', 'name' => 'Lao Peoples Democratic Republic', 'id' => 'LA','alpha3' =>'LAO', 'iso' => '418'),
			  'LBN' => array (  'dial_code' => '961', 'name' => 'Lebanon', 'id' => 'LB','alpha3' =>'LBN', 'iso' => '422'),
			  'LCA' => array (  'dial_code' => '1758', 'name' => 'Saint Lucia', 'id' => 'LC','alpha3' =>'LCA', 'iso' => '662'),
			  'LIE' => array (  'dial_code' => '423', 'name' => 'Liechtenstein', 'id' => 'LI','alpha3' =>'LIE', 'iso' => '438'),
			  'LKA' => array (  'dial_code' => '94', 'name' => 'Sri Lanka', 'id' => 'LK','alpha3' =>'LKA', 'iso' => '144'),
			  'LBR' => array (  'dial_code' => '231', 'name' => 'Liberia', 'id' => 'LR','alpha3' =>'LBR', 'iso' => '430'),
			  'LSO' => array (  'dial_code' => '266', 'name' => 'Lesotho', 'id' => 'LS','alpha3' =>'LSO', 'iso' => '426'),
			  'LTU' => array (  'dial_code' => '370', 'name' => 'Lithuania', 'id' => 'LT','alpha3' =>'LTU', 'iso' => '440'),
			  'LUX' => array (  'dial_code' => '352', 'name' => 'Luxembourg', 'id' => 'LU','alpha3' =>'LUX', 'iso' => '442'),
			  'LVA' => array (  'dial_code' => '371', 'name' => 'Latvia', 'id' => 'LV','alpha3' =>'LVA', 'iso' => '428'),
			  'LBY' => array (  'dial_code' => '218', 'name' => 'Libyan Arab Jamahiriya', 'id' => 'LY','alpha3' =>'LBY', 'iso' => '434'),
			  'MAR' => array (  'dial_code' => '212', 'name' => 'Morocco', 'id' => 'MA','alpha3' =>'MAR', 'iso' => '504'),
			  'MCO' => array (  'dial_code' => '377', 'name' => 'Monaco', 'id' => 'MC','alpha3' =>'MCO', 'iso' => '492'),
			  'MDA' => array (  'dial_code' => '373', 'name' => 'Moldova, Republic Of', 'id' => 'MD','alpha3' =>'MDA', 'iso' => '498'),
			  'MNE' => array (  'dial_code' => '382', 'name' => 'Montenegro', 'id' => 'ME','alpha3' =>'MNE', 'iso' => '499'),
			  'MAF' => array (  'dial_code' => '1599', 'name' => 'Saint Martin', 'id' => 'MF','alpha3' =>'MAF', 'iso' => '663'),
			  'MDG' => array (  'dial_code' => '261', 'name' => 'Madagascar', 'id' => 'MG','alpha3' =>'MDG', 'iso' => '450'),
			  'MHL' => array (  'dial_code' => '692', 'name' => 'Marshall Islands', 'id' => 'MH','alpha3' =>'MHL', 'iso' => '584'),
			  'MKD' => array (  'dial_code' => '389', 'name' => 'Republic of North Macedonia', 'id' => 'MK','alpha3' =>'MKD', 'iso' => '807'),
			  'MLI' => array (  'dial_code' => '223', 'name' => 'Mali', 'id' => 'ML','alpha3' =>'MLI', 'iso' => '466'),
			  'MMR' => array (  'dial_code' => '95', 'name' => 'Myanmar', 'id' => 'MM','alpha3' =>'MMR', 'iso' => '104'),
			  'MNG' => array (  'dial_code' => '976', 'name' => 'Mongolia', 'id' => 'MN','alpha3' =>'MNG', 'iso' => '496'),
			  'MAC' => array (  'dial_code' => '853', 'name' => 'Macao', 'id' => 'MO','alpha3' =>'MAC', 'iso' => '446'),
			  'MNP' => array (  'dial_code' => '1670', 'name' => 'Northern Mariana Islands', 'id' => 'MP','alpha3' =>'MNP', 'iso' => '580'),
			  'MRT' => array (  'dial_code' => '222', 'name' => 'Mauritania', 'id' => 'MR','alpha3' =>'MRT', 'iso' => '478'),
              'MSR' => array (  'dial_code' => '1664', 'name' => 'Montserrat', 'id' => 'MS','alpha3' =>'MSR', 'iso' => '500'),
              'MTQ' => array (  'dial_code' => '596', 'name' => 'Martinique', 'id' => 'MQ', 'alpha3' => 'MTQ', 'iso' => '474'),
              'MLT' => array (  'dial_code' => '356', 'name' => 'Malta', 'id' => 'MT','alpha3' =>'MLT', 'iso' => '470'),
			  'MUS' => array (  'dial_code' => '230', 'name' => 'Mauritius', 'id' => 'MU','alpha3' =>'MUS', 'iso' => '480'),
			  'MDV' => array (  'dial_code' => '960', 'name' => 'Maldives', 'id' => 'MV','alpha3' =>'MDV', 'iso' => '462'),
			  'MWI' => array (  'dial_code' => '265', 'name' => 'Malawi', 'id' => 'MW','alpha3' =>'MWI', 'iso' => '454'),
			  'MEX' => array (  'dial_code' => '52', 'name' => 'Mexico', 'id' => 'MX','alpha3' =>'MEX', 'iso' => '484'),
			  'MYS' => array (  'dial_code' => '60', 'name' => 'Malaysia', 'id' => 'MY', 'alpha3' =>'MYS', 'iso' => '458'),
			  'MOZ' => array (  'dial_code' => '258', 'name' => 'Mozambique', 'id' => 'MZ','alpha3' =>'MOZ', 'iso' => '508'),
			  'NAM' => array (  'dial_code' => '264', 'name' => 'Namibia', 'id' => 'NA','alpha3' =>'NAM', 'iso' => '516'),
			  'NCL' => array (  'dial_code' => '687', 'name' => 'New Caledonia', 'id' => 'NC','alpha3' =>'NCL', 'iso' => '540'),
			  'NER' => array (  'dial_code' => '227', 'name' => 'Niger', 'id' => 'NE','alpha3' =>'NER', 'iso' => '562'),
              'NFK' => array (  'dial_code' => '672', 'name' => 'Norfolk Island', 'id' => 'NF', 'alpha3' => 'NFK', 'iso'=> '574'),
              'NGA' => array (  'dial_code' => '234', 'name' => 'Nigeria', 'id' => 'NG','alpha3' =>'NGA', 'iso' => '566'),
			  'NIC' => array (  'dial_code' => '505', 'name' => 'Nicaragua', 'id' => 'NI','alpha3' =>'NIC', 'iso' => '558'),
			  'NLD' => array (  'dial_code' => '31', 'name' => 'Netherlands', 'id' => 'NL','alpha3' =>'NLD', 'iso' => '528'),
			  'NOR' => array (  'dial_code' => '47', 'name' => 'Norway', 'id' => 'NO','alpha3' =>'NOR', 'iso' => '578'),
			  'NPL' => array (  'dial_code' => '977', 'name' => 'Nepal', 'id' => 'NP','alpha3' =>'NPL', 'iso' => '524'),
			  'NRU' => array (  'dial_code' => '674', 'name' => 'Nauru', 'id' => 'NR','alpha3' =>'NRU', 'iso' => '520'),
			  'NIU' => array (  'dial_code' => '683', 'name' => 'Niue', 'id' => 'NU','alpha3' =>'NIU', 'iso' => '570'),
			  'NZL' => array (  'dial_code' => '64', 'name' => 'New Zealand', 'id' => 'NZ','alpha3' =>'NZL', 'iso' => '554'),
			  'OMN' => array (  'dial_code' => '968', 'name' => 'Oman', 'id' => 'OM','alpha3' =>'OMN', 'iso' => '512'),
			  'PAN' => array (  'dial_code' => '507', 'name' => 'Panama', 'id' => 'PA','alpha3' =>'PAN', 'iso' => '591'),
			  'PER' => array (  'dial_code' => '51', 'name' => 'Peru', 'id' => 'PE','alpha3' =>'PER', 'iso' => '604'),
			  'PYF' => array (  'dial_code' => '689', 'name' => 'French Polynesia', 'id' => 'PF','alpha3' =>'PYF', 'iso' => '258'),
			  'PNG' => array (  'dial_code' => '675', 'name' => 'Papua New Guinea', 'id' => 'PG','alpha3' =>'PNG', 'iso' => '598'),
			  'PHL' => array (  'dial_code' => '63', 'name' => 'Philippines', 'id' => 'PH','alpha3' =>'PHL', 'iso' => '608'),
			  'PAK' => array (  'dial_code' => '92', 'name' => 'Pakistan', 'id' => 'PK','alpha3' =>'PAK', 'iso' => '586'),
			  'POL' => array (  'dial_code' => '48', 'name' => 'Poland', 'id' => 'PL','alpha3' =>'POL', 'iso' => '616'),
              'PSE' => array (  'dial_code' => '970', 'name' => 'Palestine', 'id' => 'PS', 'alpha3'=> 'PSE', 'iso' => '275'),
              'SPM' => array (  'dial_code' => '508', 'name' => 'Saint Pierre And Miquelon', 'id' => 'PM','alpha3' =>'SPM', 'iso' => '666'),
			  'PCN' => array (  'dial_code' => '870', 'name' => 'Pitcairn', 'id' => 'PN','alpha3' =>'PCN', 'iso' => '612'),
			  'PRI' => array (  'dial_code' => '1', 'name' => 'Puerto Rico', 'id' => 'PR','alpha3' =>'PRI', 'iso' => '630'),
			  'PRT' => array (  'dial_code' => '351', 'name' => 'Portugal', 'id' => 'PT','alpha3' =>'PRT', 'iso' => '620'),
			  'PLW' => array (  'dial_code' => '680', 'name' => 'Palau', 'id' => 'PW','alpha3' =>'PLW', 'iso' => '585'),
			  'PRY' => array (  'dial_code' => '595', 'name' => 'Paraguay', 'id' => 'PY','alpha3' =>'PRY', 'iso' => '600'),
			  'QAT' => array (  'dial_code' => '974', 'name' => 'Qatar', 'id' => 'QA','alpha3' =>'QAT', 'iso' => '634'),
			  'ROU' => array (  'dial_code' => '40', 'name' => 'Romania', 'id' => 'RO','alpha3' =>'ROU', 'iso' => '642'),
			  'SRB' => array (  'dial_code' => '381', 'name' => 'Serbia', 'id' => 'RS','alpha3' =>'SRB', 'iso' => '688'),
              'REU' => array (  'dial_code' => '262', 'name' => 'Reunion', 'id' => 'RE', 'alpha3' => 'REU', 'iso' => '638'),
              'RUS' => array (  'dial_code' => '7', 'name' => 'Russian Federation', 'id' => 'RU','alpha3' =>'RUS', 'iso' => '643'),
			  'RWA' => array (  'dial_code' => '250', 'name' => 'Rwanda', 'id' => 'RW','alpha3' =>'RWA', 'iso' => '646'),
			  'SAU' => array (  'dial_code' => '966', 'name' => 'Saudi Arabia', 'id' => 'SA','alpha3' =>'SAU', 'iso' => '682'),
			  'SLB' => array (  'dial_code' => '677', 'name' => 'Solomon Islands', 'id' => 'SB','alpha3' =>'SLB', 'iso' => '090'),
              'SJM' => array (  'dial_code' => '47', 'name' => 'Svalbard and Jan Mayen', 'id' => 'SJ', 'alpha3' => 'SJM', 'iso' => '744'),
              'SYC' => array (  'dial_code' => '248', 'name' => 'Seychelles', 'id' => 'SC','alpha3' =>'SYC', 'iso' => '690'),
			  'SDN' => array (  'dial_code' => '249', 'name' => 'Sudan', 'id' => 'SD','alpha3' =>'SDN', 'iso' => '729'),
			  'SWE' => array (  'dial_code' => '46', 'name' => 'Sweden', 'id' => 'SE','alpha3' =>'SWE', 'iso' => '752'),
			  'SGP' => array (  'dial_code' => '65', 'name' => 'Singapore', 'id' => 'SG','alpha3' =>'SGP', 'iso' => '702'),
              'SGS' => array (  'dial_code' => '500', 'name' => 'South Georgia and the South Sandwich Islands' , 'id' => 'GS', 'alpha3' => 'SGS', 'iso' => '239'),
              'SHN' => array (  'dial_code' => '290', 'name' => 'Saint Helena', 'id' => 'SH','alpha3' =>'SHN', 'iso' => '654'),
              'SSD' => array (  'dial_code' => '211',  'name' => 'South Sudan', 'id' => 'SS', 'alpha3' => 'SSD', 'iso' => '728'),
              'SVN' => array (  'dial_code' => '386', 'name' => 'Slovenia', 'id' => 'SI','alpha3' =>'SVN', 'iso' => '705'),
			  'SVK' => array (  'dial_code' => '421', 'name' => 'Slovakia', 'id' => 'SK','alpha3' =>'SVK', 'iso' => '703'),
			  'SLE' => array (  'dial_code' => '232', 'name' => 'Sierra Leone', 'id' => 'SL','alpha3' =>'SLE', 'iso' => '694'),
			  'SMR' => array (  'dial_code' => '378', 'name' => 'San Marino', 'id' => 'SM','alpha3' =>'SMR', 'iso' => '674'),
			  'SEN' => array (  'dial_code' => '221', 'name' => 'Senegal', 'id' => 'SN','alpha3' =>'SEN', 'iso' => '686'),
			  'SOM' => array (  'dial_code' => '252', 'name' => 'Somalia', 'id' => 'SO','alpha3' =>'SOM', 'iso' => '706'),
			  'SUR' => array (  'dial_code' => '597', 'name' => 'Suriname', 'id' => 'SR','alpha3' =>'SUR', 'iso' => '740'),
			  'STP' => array (  'dial_code' => '239', 'name' => 'Sao Tome And Principe', 'id' => 'ST','alpha3' =>'STP', 'iso' => '678'),
			  'SLV' => array (  'dial_code' => '503', 'name' => 'El Salvador', 'id' => 'SV','alpha3' =>'SLV', 'iso' => '222'),
			  'SYR' => array (  'dial_code' => '963', 'name' => 'Syrian Arab Republic', 'id' => 'SY','alpha3' =>'SYR', 'iso' => '760'),
			  'SWZ' => array (  'dial_code' => '268', 'name' => 'Eswatini', 'id' => 'SZ','alpha3' =>'SWZ', 'iso' => '748'),
			  'TCA' => array (  'dial_code' => '1649', 'name' => 'Turks And Caicos Islands', 'id' => 'TC','alpha3' =>'TCA', 'iso' => '796'),
			  'TCD' => array (  'dial_code' => '235', 'name' => 'Chad', 'id' => 'TD','alpha3' =>'TCD', 'iso' => '148'),
              'ATF' => array (  'dial_code' => '262', 'name' => 'French Southern Territories', 'id' => 'TF', 'alpha3'=> 'ATF', 'iso' => '260' ),
			  'TGO' => array (  'dial_code' => '228', 'name' => 'Togo', 'id' => 'TG','alpha3' =>'TGO', 'iso' => '768'),
			  'THA' => array (  'dial_code' => '66', 'name' => 'Thailand', 'id' => 'TH','alpha3' =>'THA', 'iso' => '764'),
			  'TJK' => array (  'dial_code' => '992', 'name' => 'Tajikistan', 'id' => 'TJ','alpha3' =>'TJK', 'iso' => '762'),
			  'TKL' => array (  'dial_code' => '690', 'name' => 'Tokelau', 'id' => 'TK','alpha3' =>'TKL', 'iso' => '772'),
			  'TLS' => array (  'dial_code' => '670', 'name' => 'Timor-Leste', 'id' => 'TL','alpha3' =>'TLS', 'iso' => '626'),
			  'TKM' => array (  'dial_code' => '993', 'name' => 'Turkmenistan', 'id' => 'TM','alpha3' =>'TKM', 'iso' => '795'),
			  'TUN' => array (  'dial_code' => '216', 'name' => 'Tunisia', 'id' => 'TN','alpha3' =>'TUN', 'iso' => '788'),
			  'TON' => array (  'dial_code' => '676', 'name' => 'Tonga', 'id' => 'TO','alpha3' =>'TON', 'iso' => '776'),
			  'TUR' => array (  'dial_code' => '90', 'name' => 'Turkey', 'id' => 'TR','alpha3' =>'TUR', 'iso' => '792'),
			  'TTO' => array (  'dial_code' => '1868', 'name' => 'Trinidad And Tobago', 'id' => 'TT','alpha3' =>'TTO', 'iso' => '780'),
			  'TUV' => array (  'dial_code' => '688', 'name' => 'Tuvalu', 'id' => 'TV','alpha3' =>'TUV', 'iso' => '798'),
			  'TWN' => array (  'dial_code' => '886', 'name' => 'Taiwan, Province Of China', 'id' => 'TW','alpha3' =>'TWN', 'iso' => '158'),
			  'TZA' => array (  'dial_code' => '255', 'name' => 'Tanzania, United Republic Of', 'id' => 'TZ','alpha3' =>'TZA', 'iso' => '834'),
			  'UKR' => array (  'dial_code' => '380', 'name' => 'Ukraine', 'id' => 'UA','alpha3' =>'UKR', 'iso' => '804'),
			  'UGA' => array (  'dial_code' => '256', 'name' => 'Uganda', 'id' => 'UG','alpha3' =>'UGA', 'iso' => '800'),
              'UMI' => array (  'dial_code' => '246' ,'name' => 'United States Minor Outlying Islands', 'id' => 'UM', 'alpha3' => 'UMI', 'iso' => '581'),
              'USA' => array (  'dial_code' => '1', 'name' => 'United States of America', 'id' => 'US','alpha3' =>'USA', 'iso' => '840'),
			  'URY' => array (  'dial_code' => '598', 'name' => 'Uruguay', 'id' => 'UY','alpha3' =>'URY', 'iso' => '858'),
			  'UZB' => array (  'dial_code' => '998', 'name' => 'Uzbekistan', 'id' => 'UZ','alpha3' =>'UZB', 'iso' => '860'),
			  'VAT' => array (  'dial_code' => '39', 'name' => 'Holy See (vatican City State)', 'id' => 'VA','alpha3' =>'VAT', 'iso' => '336'),
			  'VCT' => array (  'dial_code' => '1784', 'name' => 'Saint Vincent And The Grenadines', 'id' => 'VC','alpha3' =>'VCT', 'iso' => '670'),
			  'VEN' => array (  'dial_code' => '58', 'name' => 'Venezuela', 'id' => 'VE','alpha3' =>'VEN', 'iso' => '862'),
			  'VGB' => array (  'dial_code' => '1284', 'name' => 'Virgin Islands, British', 'id' => 'VG','alpha3' =>'VGB', 'iso'=>'092'),
			  'VIR' => array (  'dial_code' => '1340', 'name' => 'Virgin Islands, U.s.', 'id' => 'VI','alpha3' =>'VIR', 'iso' => '850'),
			  'VNM' => array (  'dial_code' => '84', 'name' => 'Viet Nam', 'id' => 'VN','alpha3' =>'VNM', 'iso' => '704'),
			  'VUT' => array (  'dial_code' => '678', 'name' => 'Vanuatu', 'id' => 'VU','alpha3' =>'VUT', 'iso' => '548'),
			  'WLF' => array (  'dial_code' => '681', 'name' => 'Wallis And Futuna', 'id' => 'WF','alpha3' =>'WLF', 'iso' => '876'),
			  'WSM' => array (  'dial_code' => '685', 'name' => 'Samoa', 'id' => 'WS','alpha3' =>'WSM', 'iso' => '882'),
			  'YEM' => array (  'dial_code' => '967', 'name' => 'Yemen', 'id' => 'YE','alpha3' =>'YEM', 'iso' => '887'),
			  'MYT' => array (  'dial_code' => '262', 'name' => 'Mayotte', 'id' => 'YT','alpha3' =>'MYT', 'iso' => '175'),
			  'ZAF' => array (  'dial_code' => '27', 'name' => 'South Africa', 'id' => 'ZA','alpha3' =>'ZAF', 'iso' => '710'),
			  'ZMB' => array (  'dial_code' => '260', 'name' => 'Zambia', 'id' => 'ZM','alpha3' =>'ZMB', 'iso' => '894'),
			  'ZWE' => array (  'dial_code' => '263', 'name' => 'Zimbabwe', 'id' => 'ZW','alpha3' =>'ZWE', 'iso' => '716'),

			);

	public static $nationalities = array('Afghan',
			'Albanian',
			'Algerian',
			'American',
			'Andorran',
			'Angolan',
			'Antiguan and Barbudan',
			'Argentine',
			'Armenian',
			'Aruban',
			'Australian',
			'Austrian',
			'Azerbaijani',
			'Bahamian',
			'Bahraini',
			'Bangladeshi',
			'Barbadian',
			'Basque',
			'Belarusian',
			'Belgian',
			'Belizean',
			'Beninese',
			'Bermudian',
			'Bhutanese',
			'Bolivian',
			'Bosniak',
			'Bosnian and Herzegovinian',
			'Botswana',
			'Brazilian',
			'Breton',
			'British',
			'British Virgin Islander',
			'Bruneian',
			'Bulgarian',
			'Macedonian Bulgarian',
			'Burkinabé',
			'Burmese',
			'Burundian',
			'Cambodian',
			'Cameroonian',
			'Canadian',
			'Catalan',
			'Cape Verdean',
			'Chadian',
			'Chilean',
			'Chinese',
			'Chuvash',
			'Colombian',
			'Comorian',
			'Congolese',
			'Costa Rican',
			'Croatian',
			'Cuban',
			'Cypriot',
			'Turkish Cypriot',
			'Czech',
			'Dane',
			'Greenlander',
			'Djiboutian',
			'Dominican',
			'Dutch',
			'East Timorese',
			'Ecuadorian',
			'Egyptian',
			'Emirati',
			'English',
			'Equatoguinean',
			'Eritrean',
			'Estonian',
			'Ethiopian',
			'Falkland Islander',
			'Faroese',
			'Fijian',
			'Finn',
			'Finnish Swedish',
			'Filipino',
			'French',
			'Gabonese',
			'Gambian',
			'Georgian',
			'German',
			'Ghanaian',
			'Gibraltarian',
			'Greek',
			'Grenadian',
			'Guatemalan',
			'Guianese',
			'Guinean',
			'Guyanese',
			'Haitian',
			'Honduran',
			'Hungarian',
			'Icelander',
			'I-Kiribati',
			'Indian',
			'Indonesian',
			'Iranian',
			'Iraqi',
			'Irish',
			'Israeli',
			'Italian',
			'Ivoirian',
			'Jamaican',
			'Japanese',
			'Jordanian',
			'Kazakh',
			'Kenyan',
			'Korean',
			'Kosovar',
			'Kuwaiti',
			'Kyrgyz',
			'Lao',
			'Latvian',
			'Lebanese',
			'Liberian',
			'Libyan',
			'Liechtensteiner',
			'Lithuanian',
			'Luxembourger',
			'Macao',
			'Macedonian',
			'Malagasy',
			'Malawian',
			'Malaysian',
			'Maldivian',
			'Malians',
			'Maltese',
			'Manx',
			'Marshallese',
			'Mauritanian',
			'Mauritian',
			'Mexican',
			'Moldovan',
			'Monégasque',
			'Mongolian',
			'Montenegrin',
			'Moroccan',
			'Mozambican',
			'Namibian',
			'Nauran',
			'Nepalese',
			'New Zealander',
			'Nicaraguan',
			'Nigerien',
			'Nigerian',
			'Norwegian',
			'Omani',
			'Pakistani',
			'Palauan',
			'Palestinian',
			'Panamanian',
			'Papua New Guinean',
			'Paraguayan',
			'Peruvian',
			'Pole',
			'Portuguese',
			'Puerto Rican',
			'Qatari',
			'Romanian',
			'Russian',
			'Rwandan',
			'Saudi',
			'Scot',
			'Senegalese',
			'Serb',
			'Seychelloi',
			'Sierra Leonean',
			'Singaporean',
			'Slovak',
			'Slovene',
			'Solomon Islander',
			'Somali',
			'Sotho',
			'South African',
			'Spaniard',
			'Sri Lankan',
			'Sudanese',
			'Surinamese',
			'Swazi',
			'Swede',
			'Swiss',
			'Syriac',
			'Syrian',
			'Taiwanese',
			'Tamil',
			'Tajik',
			'Tanzanian',
			'Tatars',
			'Thai',
			'Tibetan',
			'Tobagonian',
			'Togolese',
			'Tongan',
			'Trinidadian',
			'Tunisian',
			'Turk',
			'Tuvaluan',
			'Ugandan',
			'Ukrainian',
			'Uruguayan',
			'Uzbek',
			'Vanuatuan',
			'Venezuelan',
			'Vietnamese',
			'Vincentian',
			'Welsh',
			'Yemeni',
			'Zambian',
			'Zimbabwean');

    const TABLE_COUNTRIES = 'engine_countries';
    const TABLE_DIALCODES = 'engine_dialcodes';
    const TABLE_COURSE_COUNTRIES = 'plugin_courses_countries';

    public static function get_country_code($code=null, $type = 3) {
        if (empty($code)) {
            return false;
        }
        //getting alpha3 code
        if ($type = 3) {
            if (array_key_exists($code, self::$countries)) {
                return self::$countries[$code];
            } elseif($type = 2) {
                //getting alpha2 country by 3 digit code
                if (array_key_exists($code, self::$alpha_countries)) {
                    return self::$alpha_countries[$code];
                }
            }
        }
    }

	public static function get_country_as_options($selected_id = null, $code = 2) {


		$options = '';

		$countries = self::get_countries($code);

		foreach ( $countries
				 as $d)
		{
			$options .= '<option value="' . $d['id'] . '"' . ($d['id'] == $selected_id
					? 'selected="selected"' : '') . '>' . $d['name'] . '</option>';
		}

		return $options;
	}

	public static function get_nationalities_as_options($selected_nationality = NULL) {
	    $options = '';
	    foreach (self::$nationalities as  $nationality) {
            $options .= '<option value="' . $nationality  . '"' . ($nationality == $selected_nationality
                    ? 'selected="selected"' : '') . '>' . $nationality . '</option>';
        }
        return $options;
    }


    public static function get_country_by_id($id, $engine = false)
    {
        $query = DB::select('*');
        if ($engine) {
            $query->from(self::TABLE_COUNTRIES);
        } else {
            $query->from(self::TABLE_COURSE_COUNTRIES);
        }
        return $query->where('id', '=', $id)->execute()->current();
    }

    public static function get_countries_from_db($engine = false, $alpha = 2) {
        $query = DB::select('*');
        if ($engine) {
            $query->from(self::TABLE_COUNTRIES);
        } else {
            $query->from(self::TABLE_COURSE_COUNTRIES);
        }
        $countries = $query->execute()->as_array();
        if (empty($result)) {
            return array();
        }
        $result = array();
        foreach ($countries as $country) {
            if ($alpha == 3) {
                $result[$country['alpha3_code']] = $country;
            } else {
                $result[$country['alpha2_code']] = $country;
            }
        }
        return $result;
    }

    public static function get_phone_codes_country_code($country_code = null, $alpha = 2, $phone_type = 'mobile') {
        $codes_query = DB::select('*')->from(self::TABLE_DIALCODES)
            ->where('type', '=', $phone_type);
        if (!empty($country_code)) {
            if ($alpha == 3) {
                $codes_query->and_where('alpha3_country_code', '=', $country_code);
            } else {
                $codes_query->and_where('alpha2_country_code', '=', $country_code);
            }
        }
        $codes = $codes_query->execute()->as_array();
        $result = array();
        if(!empty($codes)) {
            foreach($codes as $code) {
                $result[] = $code['dial_code'];
            }
        }
        return $codes;
    }

    public static function get_country_code_by_country_dial_code($dial_code, $alpha = 2, $from_db = false, $engine = false) {
        $country_code = 'IRL';
        if ($from_db) {
            $select_column = $alpha == 3 ?'alpha3_code' : 'alpha2_code';
            $db = $engine ? self::TABLE_COUNTRIES : self::TABLE_COURSE_COUNTRIES;
            $country_code = DB::select($select_column)
                ->from($db)
                ->where('dial_code', '=', $dial_code)
                ->execute()
                ->current();
        } else {
            if ($alpha == 3) {
                foreach(self::$alpha_countries as $alpha3 => $country) {
                    if ($country['dial_code'] == $dial_code) {
                        $country_code = $alpha3;
                        break;
                    }
                }
            } else {
                foreach(self::$countries as $alpha2 => $country) {
                    if ($country['dial_code'] == $dial_code) {
                        $country_code = $alpha2;
                        break;
                    }
                }
            }
        }
        return $country_code;
    }

    /**
	 *
	 * @static
	 * @param null $selected_id
	 * @param array|null $filter - array('IE','GB',....)
	 * @return string
	 */
	public static function get_dial_code_as_options($selected_id = null, array $filter = null, $only_codes = false) {
		$countries = self::get_countries();
		if ($filter) {
			$countries = array_intersect_key($countries, array_flip($filter));
		}

		if ($only_codes) {
            foreach ($countries as $key => $row)
            {
                $codes[$key] = $row['dial_code'];
                //array_multisort($codes, SORT_ASC, $countries);
                $country_codes = array();
                foreach ($countries	 as $country) {
                    $country_codes[$country['dial_code']] = '+' .  $country['dial_code'];
                }
                ksort($country_codes);
                $options = '<option value=""></option>';
                foreach ($country_codes	 as $key=> $d)
                {
                    $options .= '<option value="' . $key . '"' . ($key == $selected_id
                            ? 'selected="selected"' : '') . '>' . $d   . '</option>';
                }
            }
        } else {
            foreach ($countries as $key => $row)
            {
                $name[$key] = $row['name'];
            }
            //IbHelpers::die_r($name);
            array_multisort($name, SORT_ASC, $countries);
            $options = '<option value=""></option>';
            foreach ($countries	 as $d)
            {
                $options .= '<option value="' . $d['dial_code'] . '"' . ($d['dial_code'] == $selected_id
                        ? 'selected="selected"' : '') . '>+' . $d['dial_code'] . ' ' . $d['name']  . '</option>';
            }
        }




		return $options;
	}

	public static function get_countries($code = 2, $sort_by = 'name') {
	    if ($code == 3) {
            $countries = self::$alpha_countries;
        } else {
            $countries = self::$countries;
        }
		foreach ($countries as $key => $row)
		{
			$name[$key] = $row['name'];
		}
		//die('<pre>' . print_r($name, 1) . '</pre>');
		if ($sort_by == 'name') {
            array_multisort($name, SORT_ASC, $countries);
        } else {
		    ksort($countries);
        }
		//die('<pre>' . print_r($countries, 1) . '</pre>');
		return $countries;
	}

    public static function get_country($country)
    {
        return DB::select()->from('countries')->where('id', '=', $country)->or_where('code', '=',
            $country)->execute()->current();
    }
}