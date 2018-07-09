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
          ['tblNme' => "1A", 'fieldCount' => 13, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag'))],
          ['tblNme' => "1B", 'fieldCount' => 22, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Address ID', 'Address Type', 'Primary Flag', 'Default Address Flag', 'Title', 'Organization Name', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Carrier Route', 'County', 'Country', 'District', 'Precinct', 'No Mail Flag', 'Agency Code'))],
          ['tblNme' => "1C", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Constituent Code Type', 'Constituent Code'))],
          ['tblNme' => "1D", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', '1D Sequence Number', 'Constituent Text Type', 'Constituent Text'))],
          ['tblNme' => "1E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Constituent Phone or email Type', 'Phone Number Email or URL'))],
          ['tblNme' => "2A", 'fieldCount' => 13, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Correspondence Type', 'Staff', 'Date In', 'Date Out', 'Tickler date', 'Update Date', 'Response Type', 'Address ID', 'Household Flag', 'Household ID'))],
          ['tblNme' => "2B", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Correspondence Code', 'Position'))],
          ['tblNme' => "2C", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', '2C Sequence Number', 'Document Type', 'Correspondence Document Name', 'File Location'))],
          ['tblNme' => "2D", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', '2D Sequence Number', 'Text Type', 'Correspondence Text'))],
          ['tblNme' => "2E", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Merge Field Name', 'Merge Data'))],
          ['tblNme' => "3A", 'fieldCount' => 11, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Staff', 'Start Date', 'End Date', 'Tickler date', 'Update Date', 'Casework Description', 'Casework Status', 'Agency ID'))],
          ['tblNme' => "3B", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Casework Code'))],
          ['tblNme' => "3D", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', '3D Sequence Number', 'Text Type', 'Casework Text'))],
          ['tblNme' => "3E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Party ID'))],
          ['tblNme' => "4A", 'fieldCount' => 15, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction Type', 'Staff', 'Start Date', 'End Date', 'Tickler date', 'Update Date', 'Response Type', 'Address ID', 'Household Flag', 'Household ID', 'Contacted Party ID'))],
          ['tblNme' => "4B", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Transaction Code', 'Position'))],
          ['tblNme' => "4C", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', '4C Sequence Number', 'Document Type', 'Casework Transaction Document Name', 'File Location'))],
          ['tblNme' => "4D", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', '4D Sequence Number', 'Text Type', 'Transaction Text'))],
          ['tblNme' => "4E", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Merge Field Name', 'Merge Data'))],
          ['tblNme' => "4F", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Party ID'))],
          ['tblNme' => "5A", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Household ID', 'Household Salutation', 'Constituent ID', 'Primary Contact Flag'))],
          ['tblNme' => "7A", 'fieldCount' => 18, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Description', 'Contact', 'Country', 'Organization', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Location', 'Status', 'Private Flag', 'Notes', 'Schedule Date', 'Scheduled by', 'Revision Date', 'Revised By'))],
          ['tblNme' => "7B", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Code'))],
          ['tblNme' => "7C", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'User ID'))],
          ['tblNme' => "8A", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Code Type', 'Code', 'Code Description', 'Inactive Flag'))],
          ['tblNme' => "8C", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Letter Description', 'Document Name', 'Owner', 'Creation Date', 'Revision Date', 'Archive Flag'))],
          ['tblNme' => "8D", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Merge Field Name'))],
          ['tblNme' => "8E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Correspondence or Transaction Code', 'Code Type'))]
        ];

        foreach ($recordTypes as $type) {
            DB::table('recordtypes')->insert($type);
        }
    }
}
