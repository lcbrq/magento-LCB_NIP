<?php

/**
 * Polish NIP validation
 *
 * @category   LCB
 * @package    LCB_NIP
 * @author     Silpion Tomasz Gregorczyk <tomasz@silpion.com.pl>
 */
class LCB_NIP_Helper_Data extends Mage_Customer_Helper_Data {

    /**
     * Validate polish NIP without european registration entry or send request to VAT validation service and return validation result
     *
     * @param string $countryCode
     * @param string $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     *
     * @return Varien_Object
     */
    public function checkVatNumber($countryCode, $vatNumber, $requesterCountryCode = '', $requesterVatNumber = '')
    {

        if ($countryCode == "PL" && (empty($requesterCountryCode) || $requesterCountryCode == "PL")) {
            return $this->validatePolishNIP($vatNumber);
        } else {
            return parent::checkVatNumber($countryCode, $vatNumber, $requesterCountryCode = '', $requesterVatNumber = '');
        }
    }

    /**
     * Generate $gatewayResponse Varien_Object based on additional, non VIES validation
     * 
     * @param string $vatNumber
     * @return Varien_Object
     */
    public function validatePolishNIP($vatNumber)
    {
        $gatewayResponse = new Varien_Object(array(
            'is_valid' => false,
            'request_date' => '',
            'request_identifier' => '',
            'request_success' => true
        ));

        if ($this->CheckNIP($vatNumber)) {
            $gatewayResponse->setIsValid(true);
        }

        return $gatewayResponse;
    }

    /**
     * Check polish NIP number, thanks to PHPEdia.pl
     * 
     * @link http://phpedia.pl/wiki/Walidacja_numeru_NIP
     * @param string $vatNumber
     */
    function CheckNIP($vatNumber)
    {
        $str = preg_replace("/[^0-9]+/", "", $vatNumber);
        if (strlen($str) != 10) {
            return false;
        }

        $arrSteps = array(6, 5, 7, 2, 3, 4, 5, 6, 7);
        $intSum = 0;
        for ($i = 0; $i < 9; $i++) {
            $intSum += $arrSteps[$i] * $str[$i];
        }
        $int = $intSum % 11;

        $intControlNr = ($int == 10) ? 0 : $int;
        if ($intControlNr == $str[9]) {
            return true;
        }
        return false;
    }

}
