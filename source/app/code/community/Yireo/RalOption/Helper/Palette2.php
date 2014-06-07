<?php
/**
 * RalOption plugin for Magento 
 *
 * @package     Yireo_RalOption
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_RalOption_Helper_Palette2 extends Mage_Core_Helper_Abstract
{
    /*
     * Helper-method to return the palette-codes
     * 
     * @access public
     * @parameter null
     * @return array
     */
    public function getCodes()
    {
        return array(
            1000 => 'BEBD7F',
            1001 => 'C2B078',
            1002 => 'C6A664',
            1003 => 'E5BE01',
            1004 => 'CDA434',
            1005 => 'A98307',
            1006 => 'E4A010',
            1007 => 'DC9D00',
            1011 => '8A6642',
            1012 => 'C7B446',
            1013 => 'EAE6CA',
            1014 => 'E1CC4F',
            1015 => 'E6D690',
            1016 => 'EDFF21',
            1017 => 'F5D033',
            1018 => 'F8F32B',
            1019 => '9E9764',
            1020 => '999950',
            1021 => 'F3DA0B',
            1023 => 'FAD201',
            1024 => 'AEA04B',
            1026 => 'FFFF00',
            1027 => '9D9101',
            1028 => 'F4A900',
            1032 => 'D6AE01',
            1033 => 'F3A505',
            1034 => 'EFA94A',
            1035 => '6A5D4D',
            1036 => '705335',
            1037 => 'F39F18',
            2000 => 'ED760E',
            2001 => 'C93C20',
            2002 => 'CB2821',
            2003 => 'FF7514',
            2004 => 'F44611',
            2005 => 'FF2301',
            2007 => 'FFA420',
            2008 => 'F75E25',
            2009 => 'F54021',
            2010 => 'D84B20',
            2011 => 'EC7C26',
            2012 => 'E55137',
            2013 => 'C35831',
            3000 => 'AF2B1E',
            3001 => 'A52019',
            3002 => 'A2231D',
            3003 => '9B111E',
            3004 => '75151E',
            3005 => '5E2129',
            3007 => '412227',
            3009 => '642424',
            3011 => '781F19',
            3012 => 'C1876B',
            3013 => 'A12312',
            3014 => 'D36E70',
            3015 => 'EA899A',
            3016 => 'B32821',
            3017 => 'E63244',
            3018 => 'D53032',
            3020 => 'CC0605',
            3022 => 'D95030',
            3024 => 'F80000',
            3026 => 'FE0000',
            3027 => 'C51D34',
            3028 => 'CB3234',
            3031 => 'B32428',
            3032 => '721422',
            3033 => 'B44C43',
            4001 => '6D3F5B',
            4002 => '922B3E',
            4003 => 'DE4C8A',
            4004 => '641C34',
            4005 => '6C4675',
            4006 => 'A03472',
            4007 => '4A192C',
            4008 => '924E7D',
            4009 => 'A18594',
            4010 => 'CF3476',
            4011 => '8673A1',
            4012 => '6C6874',
            5000 => '354D73',
            5001 => '1F3438',
            5002 => '20214F',
            5003 => '1D1E33',
            5004 => '18171C',
            5005 => '1E2460',
            5007 => '3E5F8A',
            5008 => '26252D',
            5009 => '025669',
            5010 => '0E294B',
            5011 => '231A24',
            5012 => '3B83BD',
            5013 => '1E213D',
            5014 => '606E8C',
            5015 => '2271B3',
            5017 => '063971',
            5018 => '3F888F',
            5019 => '1B5583',
            5020 => '1D334A',
            5021 => '256D7B',
            5022 => '252850',
            5023 => '49678D',
            5024 => '5D9B9B',
            5025 => '2A6478',
            5026 => '102C54',
            6000 => '316650',
            6001 => '287233',
            6002 => '2D572C',
            6003 => '424632',
            6004 => '1F3A3D',
            6005 => '2F4538',
            6006 => '3E3B32',
            6007 => '343B29',
            6008 => '39352A',
            6009 => '31372B',
            6010 => '35682D',
            6011 => '587246',
            6012 => '343E40',
            6013 => '6C7156',
            6014 => '47402E',
            6015 => '3B3C36',
            6016 => '1E5945',
            6017 => '4C9141',
            6018 => '57A639',
            6019 => 'BDECB6',
            6020 => '2E3A23',
            6021 => '89AC76',
            6022 => '25221B',
            6024 => '308446',
            6025 => '3D642D',
            6026 => '015D52',
            6027 => '84C3BE',
            6028 => '2C5545',
            6029 => '20603D',
            6032 => '317F43',
            6033 => '497E76',
            6034 => '7FB5B5',
            6035 => '1C542D',
            6036 => '193737',
            6037 => '008F39',
            6038 => '00BB2D',
            7000 => '78858B',
            7001 => '8A9597',
            7002 => '7E7B52',
            7003 => '6C7059',
            7004 => '969992',
            7005 => '646B63',
            7006 => '6D6552',
            7008 => '6A5F31',
            7009 => '4D5645',
            7010 => '4C514A',
            7011 => '434B4D',
            7012 => '4E5754',
            7013 => '464531',
            7015 => '434750',
            7016 => '293133',
            7021 => '23282B',
            7022 => '332F2C',
            7023 => '686C5E',
            7024 => '474A51',
            7026 => '2F353B',
            7030 => '8B8C7A',
            7031 => '474B4E',
            7032 => 'B8B799',
            7033 => '7D8471',
            7034 => '8F8B66',
            7035 => 'D7D7D7',
            7036 => '7F7679',
            7037 => '7D7F7D',
            7038 => 'B5B8B1',
            7039 => '6C6960',
            7040 => '9DA1AA',
            7042 => '8D948D',
            7043 => '4E5452',
            7044 => 'CAC4B0',
            7045 => '909090',
            7046 => '82898F',
            7047 => 'D0D0D0',
            7048 => '898176',
            8000 => '826C34',
            8001 => '955F20',
            8002 => '6C3B2A',
            8003 => '734222',
            8004 => '8E402A',
            8007 => '59351F',
            8008 => '6F4F28',
            8011 => '5B3A29',
            8012 => '592321',
            8014 => '382C1E',
            8015 => '633A34',
            8016 => '4C2F27',
            8017 => '45322E',
            8019 => '403A3A',
            8022 => '212121',
            8023 => 'A65E2E',
            8024 => '79553D',
            8025 => '755C48',
            8028 => '4E3B31',
            8029 => '763C28',
            9001 => 'FDF4E3',
            9002 => 'E7EBDA',
            9003 => 'F4F4F4',
            9004 => '282828',
            9005 => '0A0A0A',
            9006 => 'A5A5A5',
            9007 => '8F8F8F',
            9010 => 'FAFAFA',
            9011 => '1C1C1C',
            9016 => 'F6F6F6',
            9017 => '1E1E1E',
            9018 => 'D7D7D7',
            9022 => '9C9C9C',
            9023 => '828282',
        );
    }

    /*
     * Helper-method to return the palette-codes
     * 
     * @access public
     * @parameter null
     * @return array
     */
    public function getPriceRules()
    {
        return null;
    }

    /*
     * Helper-method to return the default color
     * 
     * @access public
     * @parameter null
     * @return array
     */
    public function getDefault()
    {
        return 'F0EDE6';
    }
}
