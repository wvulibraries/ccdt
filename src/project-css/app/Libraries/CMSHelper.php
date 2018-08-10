<?php

namespace App\Libraries;

use App\Models\CMSRecords;
use App\Models\Collection;

use App\Libraries\CSVHelper;
use App\Libraries\TableHelper;

class CMSHelper {
    /**
     * CMS Helper
     *
     * These are various functions that help with processing
     * cms files.
     *
     */

     public function getCMSFields($collctnId, $recordType, $fieldCount) {
       if (CMSRecords::isCMSRecord($recordType)) {

        // get collection record so we can set the cms ID
        $thisClctn = Collection::findorFail($collctnId);

        $response = CMSRecords::findCMSHeader($recordType, $fieldCount);

        // if only one item is found we return that result
        if (count($response) == 1) {
          // set cmsID in Collection is not already set
          if ($thisClctn->cmdId == null) {
            $thisClctn->setCMSId($collctnId, $response[0]->cmsId);
          }

          // first returned response should be the closest match
          return unserialize($response[0]->fieldNames);
        }
        elseif (count($response) > 1) {
          for ($arrayPos = 0; $arrayPos < count($response); $arrayPos++) {
            if ($response[$arrayPos]->cmsId === $thisClctn->cmsId) {
              return unserialize($response[$arrayPos]->fieldNames);
            }
          }
        }
       }
       return null;
     }

     // function return array of generated field names
     public function generateHeader($fieldCount) {
       $headerArray = [];
       for ($arrayPos = 0; $arrayPos < $fieldCount; $arrayPos++) {
         array_push($headerArray, 'Field'.$arrayPos);
       }
       return $headerArray;
     }

     public function createCMSTable($storageFolder, $thsFltFile, $collctnId, $tblNme) {
       // detect fields we pass false if we do not have a header row,
       // file location, number of rows to check
       $fieldTypes = (new CSVHelper)->determineTypes(false, $storageFolder.'/'.$thsFltFile, 1000);

       // get 1st row from csv file
       $schema = (new CSVHelper)->schema($storageFolder.'/'.$thsFltFile);

       // get header from database pass record type and detected field count
       $header = $this->getCMSFields($collctnId, $schema[0], count($fieldTypes));

       // if we get a null header then we generate one
       if ($header == null) {
         // generate header since we couldn't find a match
         $header = (new CSVHelper)->generateHeader(count($fieldTypes));
       }

       (new TableHelper)->createTable($storageFolder, $thsFltFile, $tblNme, $header, $fieldTypes, $collctnId);
     }
}
