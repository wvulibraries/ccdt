<?php

use Illuminate\Database\Seeder;

class RecordTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // insert stopwords into table
        $recordTypes = [
          ['tblNme' => "Constituent1A", 'recordType' => "1A", 'fieldCount' => 13, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag'))],
          ['tblNme' => "Constituent1B", 'recordType' => "1B", 'fieldCount' => 22, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Address ID', 'Address Type', 'Primary Flag', 'Default Address Flag', 'Title', 'Organization Name', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Carrier Route', 'County', 'Country', 'District', 'Precinct', 'No Mail Flag', 'Agency Code'))],
          ['tblNme' => "Constituent1C", 'recordType' => "1C", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Constituent Code Type', 'Constituent Code'))],
          ['tblNme' => "Constituent1D", 'recordType' => "1D", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', '1D Sequence Number', 'Constituent Text Type', 'Constituent Text'))],
          ['tblNme' => "Constituent1E", 'recordType' => "1E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Constituent Phone or email Type', 'Phone Number Email or URL'))],
          ['tblNme' => "Constituent2A", 'recordType' => "2A", 'fieldCount' => 13, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Correspondence Type', 'Staff', 'Date In', 'Date Out', 'Tickler date', 'Update Date', 'Response Type', 'Address ID', 'Household Flag', 'Household ID'))],
          ['tblNme' => "Constituent2B", 'recordType' => "2B", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Correspondence Code', 'Position'))],
          ['tblNme' => "Constituent2C", 'recordType' => "2C", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', '2C Sequence Number', 'Document Type', 'Correspondence Document Name', 'File Location'))],
          ['tblNme' => "Constituent2D", 'recordType' => "2D", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', '2D Sequence Number', 'Text Type', 'Correspondence Text'))],
          ['tblNme' => "Constituent2E", 'recordType' => "2E", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Merge Field Name', 'Merge Data'))],
          ['tblNme' => "Constituent3A", 'recordType' => "3A", 'fieldCount' => 11, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Staff', 'Start Date', 'End Date', 'Tickler date', 'Update Date', 'Casework Description', 'Casework Status', 'Agency ID'))],
          ['tblNme' => "Constituent3B", 'recordType' => "3B", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Casework Code'))],
          ['tblNme' => "Constituent3D", 'recordType' => "3D", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', '3D Sequence Number', 'Text Type', 'Casework Text'))],
          ['tblNme' => "Constituent3E", 'recordType' => "3E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Party ID'))],
          ['tblNme' => "Constituent4A", 'recordType' => "4A", 'fieldCount' => 15, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction Type', 'Staff', 'Start Date', 'End Date', 'Tickler date', 'Update Date', 'Response Type', 'Address ID', 'Household Flag', 'Household ID', 'Contacted Party ID'))],
          ['tblNme' => "Constituent4B", 'recordType' => "4B", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Transaction Code', 'Position'))],
          ['tblNme' => "Constituent4C", 'recordType' => "4C", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', '4C Sequence Number', 'Document Type', 'Casework Transaction Document Name', 'File Location'))],
          ['tblNme' => "Constituent4D", 'recordType' => "4D", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', '4D Sequence Number', 'Text Type', 'Transaction Text'))],
          ['tblNme' => "Constituent4E", 'recordType' => "4E", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Merge Field Name', 'Merge Data'))],
          ['tblNme' => "Constituent4F", 'recordType' => "4F", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Party ID'))],
          ['tblNme' => "Constituent5A", 'recordType' => "5A", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Household ID', 'Household Salutation', 'Constituent ID', 'Primary Contact Flag'))],
          ['tblNme' => "Constituent7A", 'recordType' => "7A", 'fieldCount' => 18, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Description', 'Contact', 'Country', 'Organization', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Location', 'Status', 'Private Flag', 'Notes', 'Schedule Date', 'Scheduled by', 'Revision Date', 'Revised By'))],
          ['tblNme' => "Constituent7B", 'recordType' => "7B", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Code'))],
          ['tblNme' => "Constituent7C", 'recordType' => "7C", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'User ID'))],
          ['tblNme' => "Constituent8A", 'recordType' => "8A", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Code Type', 'Code', 'Code Description', 'Inactive Flag'))],
          ['tblNme' => "Constituent8C", 'recordType' => "8C", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Letter Description', 'Document Name', 'Owner', 'Creation Date', 'Revision Date', 'Archive Flag'))],
          ['tblNme' => "Constituent8D", 'recordType' => "8D", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Merge Field Name'))],
          ['tblNme' => "Constituent8E", 'recordType' => "8E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Correspondence or Transaction Code', 'Code Type'))]
        ];

        foreach ($recordTypes as $type) {
            DB::table('recordtypes')->insert($type);
        }
    }
}
