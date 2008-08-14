<?php

/*
+---------------------------------------------------------------------------+
| OpenX  v${RELEASE_MAJOR_MINOR}                                            |
| =======${RELEASE_MAJOR_MINOR_DOUBLE_UNDERLINE}                            |
|                                                                           |
| Copyright (c) 2003-2008 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

/**
 * @package    OpenX
 * @author     Andriy Petlyovanyy <apetlyovanyy@lohika.com>
 *
 */

// Require the XMLRPC classes
require_once MAX_PATH . '/lib/pear/XML/RPC/Server.php';

// Require the Pear::Date class
require_once MAX_PATH . '/lib/pear/Date.php';

/**
 * XmlRpc methods class description.
 *
 */
class XmlRpcUtils
{
    /**
     * Generate Error message.
     *
     * @access public
     *
     * @param string $errorMessage
     *
     * @return XML_RPC_Response
     */
    function generateError($errorMessage)
    {
        // import user errcode value
        global $XML_RPC_erruser;

        $errorCode = $XML_RPC_erruser + 1;
        return new XML_RPC_Response(0, $errorCode, $errorMessage);
    }

    /**
     * Response string.
     *
     * @access public
     *
     * @param string $string
     *
     * @return XML_RPC_Response
     */
    function stringTypeResponse($string)
    {
        $value = new XML_RPC_Value($string, $GLOBALS['XML_RPC_String']);
        return new XML_RPC_Response($value);
    }

    /**
     * Response boolean.
     *
     * @access public
     *
     * @param boolean $boolean
     *
     * @return XML_RPC_Response
     */
    function booleanTypeResponse($boolean)
    {
        $value = new XML_RPC_Value($boolean, $GLOBALS['XML_RPC_Boolean']);
        return new XML_RPC_Response($value);
    }

    /**
     * Response integer.
     *
     * @access public
     *
     * @param integer $integer
     *
     * @return XML_RPC_Response
     */
    function integerTypeResponse($integer)
    {
        $value = new XML_RPC_Value($integer, $GLOBALS['XML_RPC_Int']);
        return new XML_RPC_Response($value);
    }

    /**
     * Convert RecordSet into the array of XML_RPC_Response structures.
     *
     * @access public
     *
     * @param array $aFieldTypes  field name - field type
     * @param RecordSet &$rsAllData   Record Set with all data
     *
     * @return XML_RPC_Response
     */
    function arrayOfStructuresResponse($aFieldTypes, &$rsAllData)
    {
        $rsAllData->find();
        $cRecords = 0;

           while($rsAllData->fetch()) {
               $aRowData = $rsAllData->toArray();
            foreach ($aRowData as $databaseFieldName => $fieldValue) {
                foreach ($aFieldTypes as $fieldName => $fieldType) {
                    if (strtolower($fieldName) == strtolower($databaseFieldName)) {
                        $aReturnData[$cRecords][$fieldName] = XmlRpcUtils::_setRPCTypeWithDefaultValues(
                                                                $fieldType, $fieldValue);
                    }
                }

            }

            $aReturnData[$cRecords] = new XML_RPC_Value($aReturnData[$cRecords],
                                                           $GLOBALS['XML_RPC_Struct']);
            $cRecords++;
        }

        $value = new XML_RPC_Value($aReturnData, $GLOBALS['XML_RPC_Array']);

        return new XML_RPC_Response($value);
    }

    /**
     * Converts Info Object into XML_RPC_Value
     *
     * @access public
     *
     * @param object &$oInfoObject
     *
     * @return XML_RPC_Value
     */
    function getEntity(&$oInfoObject)
    {
        $aInfoData = (array) $oInfoObject;
        $aReturnData = array();

        foreach ($aInfoData as $fieldName => $fieldValue) {
            $aReturnData[$fieldName] = XmlRpcUtils::_setRPCTypeForField(
                        $oInfoObject->getFieldType($fieldName), $fieldValue);
        }
        return new XML_RPC_Value($aReturnData,
                                            $GLOBALS['XML_RPC_Struct']);
    }

    /**
     * Converts Info Object into XML_RPC_Value and deletes null fields
     *
     * @access public
     *
     * @param object &$oInfoObject
     *
     * @return XML_RPC_Value
     */
    function getEntityWithNotNullFields(&$oInfoObject)
    {
        $aInfoData = $oInfoObject->toArray();
        $aReturnData = array();

        foreach ($aInfoData as $fieldName => $fieldValue) {
            if (!is_null($fieldValue)) {
                $aReturnData[$fieldName] = XmlRpcUtils::_setRPCTypeForField(
                            $oInfoObject->getFieldType($fieldName), $fieldValue);
            }
        }
        return new XML_RPC_Value($aReturnData,
                                            $GLOBALS['XML_RPC_Struct']);
    }

    /**
     * Converts Info Object into XML_RPC_Response structure
     *
     * @access public
     *
     * @param object &$oInfoObject
     *
     * @return XML_RPC_Response
     */
    function getEntityResponse(&$oInfoObject)
    {
        return new XML_RPC_Response(XmlRpcUtils::getEntity($oInfoObject));
    }

    /**
     * Converts Info Object into the array of  XML_RPC_Response structures
     *
     * @access public
     *
     * @param object $aInfoObjects
     *
     * @return XML_RPC_Response
     */
    function getArrayOfEntityResponse($aInfoObjects)
    {
        $cRecords = 0;

        foreach ($aInfoObjects as $oInfoObject) {
            $xmlValue[$cRecords] = XmlRpcUtils::getEntity($oInfoObject);
            $cRecords++;
        }

        $value = new XML_RPC_Value($xmlValue,
                                      $GLOBALS['XML_RPC_Array']);

        return new XML_RPC_Response($value);
    }

    /**
     * Set RPC type for variable with default values.
     *
     * @access private
     *
     * @param string $type
     * @param mixed $variable
     *
     * @return XML_RPC_Value or false
     */
    function _setRPCTypeWithDefaultValues($type, $variable)
    {
        switch ($type) {
            case 'string':
                if (is_null($variable)) {
                    $variable = '';
                }
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_String']);

            case 'integer':
                if (is_null($variable)) {
                    $variable = 0;
                }
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_Int']);

            case 'float':
                if (is_null($variable)) {
                    $variable = 0.0;
                }
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_Double']);

            case 'date':
                $dateVariable = null;
                if (isset($variable)) {

                    if (!is_string($variable)) {
                        Max::raiseError('Date for statistics should be represented as string');
                        exit;
                    }

                    if ($variable != OA_Dal::noDateValue()) {
                        $dateArr = explode('-', $variable);
                        $dateVariable = $dateArr[0] . $dateArr[1] . $dateArr[2] . 'T00:00:00';
                    }
                }

                return new XML_RPC_Value($dateVariable, $GLOBALS['XML_RPC_DateTime']);
        }
        Max::raiseError('Unsupported Xml Rpc type \'' . $type . '\'');
        exit;
    }

    /**
     * Set RPC type for variable.
     *
     * @access private
     *
     * @param string $type
     * @param mixed $variable
     *
     * @return XML_RPC_Value or false
     */
    function _setRPCTypeForField($type, $variable)
    {
        switch ($type) {
            case 'string':
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_String']);

            case 'integer':
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_Int']);

            case 'float':
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_Double']);

            case 'date':

                if (!is_object($variable) || !is_a($variable, 'Date')) {
                    Max::raiseError('Value should be PEAR::Date type');
                    exit;
                }

                $value = $variable->format('%Y%m%d') . 'T00:00:00';
                return new XML_RPC_Value($value, $GLOBALS['XML_RPC_DateTime']);

            case 'custom':
                return $variable;
        }
        Max::raiseError('Unsupported Xml Rpc type \'' . $type . '\'');
        exit;
    }

    /**
     * Convert Date from iso 8601 format.
     *
     * @access private
     *
     * @param string $date  date string in ISO 8601 format
     * @param PEAR::Date &$oResult  transformed date
     * @param XML_RPC_Response &$oResponseWithError  response with error message
     *
     * @return boolean  shows true if method was executed successfully
     */
    function _convertDateFromIso8601Format($date, &$oResult, &$oResponseWithError)
    {
        $datetime = explode('T', $date);
        $year     = substr($datetime[0], 0, (strlen($datetime[0]) - 4));
        $month    = substr($datetime[0], -4, 2);
        $day      = substr($datetime[0], -2, 2);

        if (($year < 1970) || ($year > 2038)) {

            $oResponseWithError = XmlRpcUtils::generateError('Year should be in range 1970-2038');
            return false;

        } elseif (($month < 1) || ($month > 12)) {

            $oResponseWithError = XmlRpcUtils::generateError('Month should be in range 1-12');
            return false;

        } elseif (($day < 1) || ($day > 31)) {

            $oResponseWithError = XmlRpcUtils::generateError('Day should be in range 1-31');
            return false;

        } else {

            $oResult = new Date();
            $oResult->setYear($year);
            $oResult->setMonth($month);
            $oResult->setDay($day);

            return true;
        }
    }

    /**
     * Get scalar value from parameter
     *
     * @access private
     *
     * @param mixed &$result
     * @param XML_RPC_Value &$oParam
     * @param XML_RPC_Response &$oResponseWithError
     *
     * @return boolean  shows true if method was executed successfully
     */
    function _getScalarValue(&$result, &$oParam, &$oResponseWithError)
    {
        if ($oParam->scalartyp() == $GLOBALS['XML_RPC_Int']) {
            $result = (int) $oParam->scalarval();
            return true;
        } elseif ($oParam->scalartyp() == $GLOBALS['XML_RPC_DateTime']) {

            return XmlRpcUtils::_convertDateFromIso8601Format($oParam->scalarval(),
                $result, $oResponseWithError);
        } elseif ($oParam->scalartyp() == $GLOBALS['XML_RPC_Boolean']) {
            $result = (bool) $oParam->scalarval();
            return true;
        } else {
            $result = $oParam->scalarval();
            return true;
        }
    }

    /**
     * Get non-scalar value from parameter
     *
     * @access private
     *
     * @param mixed &$result
     * @param XML_RPC_Value &$oParam
     * @param XML_RPC_Response &$oResponseWithError
     *
     * @return boolean  shows true if method was executed successfully
     */
    function _getNonScalarValue(&$result, &$oParam, &$oResponseWithError)
    {
        $result = XML_RPC_decode($oParam);
        return true;
    }

    /**
     * Get required scalar value
     *
     * @access public
     *
     * @param mixed &$result
     * @param XML_RPC_Message  &$oParams
     * @param integer $idxParam
     * @param XML_RPC_Response &$oResponseWithError
     *
     * @return boolean  shows true if method was executed successfully
     */
    function getRequiredScalarValue(&$result, &$oParams, $idxParam, &$oResponseWithError)
    {
        $oParam = $oParams->getParam($idxParam);
        return XmlRpcUtils::_getScalarValue($result, $oParam, $oResponseWithError);
    }

    /**
     * Get not required scalar value
     *
     * @access private
     *
     * @param mixed &$result value or null
     * @param XML_RPC_Message  &$oParams
     * @param integer $idxParam
     * @param XML_RPC_Response &$oResponseWithError
     *
     * @return boolean  shows true if method was executed successfully
     */
    function _getNotRequiredScalarValue(&$result, &$oParams, $idxParam, &$oResponseWithError)
    {
        $cParams = $oParams->getNumParams();
        if ($cParams > $idxParam) {
            $oParam = $oParams->getParam($idxParam);

            return XmlRpcUtils::_getScalarValue($result, $oParam, $oResponseWithError);
        } else {

            $result = null;
            return true;
        }

    }

    /**
     * Get scalar values from parameters
     *
     * @access public
     *
     * @param array $aReferencesOnVariables array of references to variables
     * @param array $aRequired array of boolean values to indicate which field is required
     * @param XML_RPC_Message  $oParams
     * @param XML_RPC_Response &$oResponseWithError
     * @param integer $idxStart Index of parameter from which values start
     *
     * @return boolean  shows true if method was executed successfully
     */
    function getScalarValues($aReferencesOnVariables, $aRequired, &$oParams, &$oResponseWithError,
        $idxStart = 0)
    {
        if (count($aReferencesOnVariables) != count($aRequired)) {
            Max::raiseError('$aReferencesOnVariables & $aRequired arrays should have the same length');
            exit;
        }

        $cVariables = count($aReferencesOnVariables);
        for ($i = 0; $i < $cVariables; $i++) {
            if ($aRequired[$i]) {
                if (!XmlRpcUtils::getRequiredScalarValue($aReferencesOnVariables[$i],
                    $oParams, $i + $idxStart, $oResponseWithError)) {
                    return false;
                }
            } else {
                if (!XmlRpcUtils::_getNotRequiredScalarValue($aReferencesOnVariables[$i],
                    $oParams, $i + $idxStart, $oResponseWithError)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Gets Structure Scalar field from XML RPC Value parameter
     *
     * @access private
     *
     * @param structure &$oStructure  to return data
     * @param XML_RPC_Value $oStructParam
     * @param string $fieldName
     * @param XML_RPC_Response &$responseWithError
     *
     * @return boolean  shows true if method was executed successfully
     */
    function _getStructureScalarField(&$oStructure, &$oStructParam, $fieldName,
        &$oResponseWithError)
    {
        $oParam = $oStructParam->structmem($fieldName);
        if (isset($oParam)) {

            if ($oParam->kindOf() == 'scalar') {

                return XmlRpcUtils::_getScalarValue($oStructure->$fieldName, $oParam, $oResponseWithError);

            } else {

                $oResponseWithError = XmlRpcUtils::generateError(
                    'Structure field \'' . $fieldName .'\' should be scalar type ');
                return false;
            }

        } else {

            return true;

        }
    }


    /**
     * Gets Structure Non Scalar field from XML RPC Value parameter
     *
     * @access private
     *
     * @param structure &$oStructure  to return data
     * @param XML_RPC_Value $oStructParam
     * @param string $fieldName
     * @param XML_RPC_Response &$responseWithError
     *
     * @return boolean  shows true if method was executed successfully
     */
    function _getStructureNonScalarField(&$oStructure, &$oStructParam, $fieldName, &$oResponseWithError)
    {
        $oParam = $oStructParam->structmem($fieldName);
        if (isset($oParam)) {
            if ($oParam->kindOf() != 'scalar') {

                return XmlRpcUtils::_getNonScalarValue($oStructure->$fieldName, $oParam, $oResponseWithError);

            } else {

                $oResponseWithError = XmlRpcUtils::generateError(
                    'Structure field \'' . $fieldName .'\' should be non-scalar type ');
                return false;
            }
        } else {

            return true;

        }
    }

    /**
     * Gets Structure Scalar fields
     *
     * @access public
     *
     * @param structure &$oStructure  to return data
     * @param XML_RPC_Message &$oParams
     * @param integer $idxParam
     * @param array $aFieldNames
     * @param XML_RPC_Response &$oResponseWithError
     *
     * @return boolean  shows true if method was executed successfully
     */
    function getStructureScalarFields(&$oStructure, &$oParams, $idxParam,
        $aFieldNames, &$oResponseWithError)
    {
        $oStructParam = $oParams->getParam($idxParam);

        foreach ($aFieldNames as $fieldName) {

            if (!XmlRpcUtils::_getStructureScalarField($oStructure, $oStructParam,
                $fieldName, $oResponseWithError)) {

                return false;
            }
        }
        return true;
    }

    /**
     * Gets Structure Scalar and non-Scalar fields
     *
     * @access public
     *
     * @param structure &$oStructure  to return data
     * @param XML_RPC_Message &$oParams
     * @param integer $idxParam
     * @param array $aScalars Field names array
     * @param array $aNonScalars Field names array
     * @param XML_RPC_Response &$oResponseWithError
     *
     * @return boolean  shows true if method was executed successfully
     */
    function getStructureScalarAndNotScalarFields(&$oStructure, &$oParams, $idxParam,
        $aScalars, $aNonScalars, &$oResponseWithError)
    {
        $result = XmlRpcUtils::getStructureScalarFields($oStructure, $oParams, $idxParam, $aScalars, $oResponseWithError);

        if ($result) {
            $oStructParam = $oParams->getParam($idxParam);

            foreach ($aNonScalars as $fieldName) {

                if (!XmlRpcUtils::_getStructureNonScalarField($oStructure, $oStructParam,
                    $fieldName, $oResponseWithError)) {

                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }
}



?>