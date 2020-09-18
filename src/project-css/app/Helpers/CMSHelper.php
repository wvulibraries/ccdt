<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Helpers;

use App\Models\CMSRecords;
use App\Models\Collection;
use App\Helpers\CSVHelper;
use App\Helpers\TableHelper;

/**
 * CMS Helper
 *
 * These are various functions that help with processing
 * cms files.
 *
 */
class CMSHelper {
     /**
      * Check the Record Types table for a matching header for the table
      *
      * @param integer $collctnID Contains the collection ID
      * @param string $recordType Contains the Record type 1A, 1B, etc.
      * @param integer $fieldCount Contains number of fields expected for the Record
      *
      * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
      *
      * @return unserialized Array if found or null
      */
     public function getCMSFields($collctnId, $recordType, $fieldCount) {
       if (CMSRecords::isCMSRecord($recordType)) {

        // get collection record so we can set the cms ID
        $thisClctn = Collection::findorFail($collctnId);

        $response = CMSRecords::findCMSHeader($recordType, $fieldCount);

        // if only one item is found we return that result
        if (count($response) == 1) {
          // set cmsID in Collection if not already set
          if ($thisClctn->cmsId == null) {
            $thisClctn->setCMSId($response[0]->cmsId);
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

     /**
      * cmsHeader finds or creates a header to be used for creating 
      * cms tables.
      *
      * @param integer $collctnID Contains the collection ID
      * @param string $recordType Contains the Record type 1A, 1B, etc.
      * @param integer $fieldCount Contains number of fields expected for the Record
      *
      * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
      * @return Array
      */     
     public function cmsHeader($collctnId, $recordType = null, $fieldCount) {
       $header = null;
       
       // verify $recordType is not null
       if ($recordType != null) {
        // get header from database pass record type and detected field count
        $header = $this->getCMSFields($collctnId, $recordType, $fieldCount);
       }

       // if we get a null header then we generate one
       if ($header == null) {
         // generate header since we couldn't find a match
         $header = (new CSVHelper)->generateHeader($fieldCount);
       }

       return $header;
     }
   
}
