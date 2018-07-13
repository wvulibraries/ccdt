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
          ['recordType' => "1A", 'fieldCount' => 13, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag'))],
          ['recordType' => "1B", 'fieldCount' => 22, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Address ID', 'Address Type', 'Primary Flag', 'Default Address Flag', 'Title', 'Organization Name', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Carrier Route', 'County', 'Country', 'District', 'Precinct', 'No Mail Flag', 'Agency Code'))],
          ['recordType' => "1C", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Constituent Code Type', 'Constituent Code'))],
          ['recordType' => "1D", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', '1D Sequence Number', 'Constituent Text Type', 'Constituent Text'))],
          ['recordType' => "1E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Constituent Phone or email Type', 'Phone Number Email or URL'))],
          ['recordType' => "2A", 'fieldCount' => 13, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Correspondence Type', 'Staff', 'Date In', 'Date Out', 'Tickler date', 'Update Date', 'Response Type', 'Address ID', 'Household Flag', 'Household ID'))],
          ['recordType' => "2B", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Correspondence Code', 'Position'))],
          ['recordType' => "2C", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', '2C Sequence Number', 'Document Type', 'Correspondence Document Name', 'File Location'))],
          ['recordType' => "2D", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', '2D Sequence Number', 'Text Type', 'Correspondence Text'))],
          ['recordType' => "2E", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Merge Field Name', 'Merge Data'))],
          ['recordType' => "3A", 'fieldCount' => 11, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Staff', 'Start Date', 'End Date', 'Tickler date', 'Update Date', 'Casework Description', 'Casework Status', 'Agency ID'))],
          ['recordType' => "3B", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Casework Code'))],
          ['recordType' => "3D", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', '3D Sequence Number', 'Text Type', 'Casework Text'))],
          ['recordType' => "3E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Party ID'))],
          ['recordType' => "4A", 'fieldCount' => 15, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction Type', 'Staff', 'Start Date', 'End Date', 'Tickler date', 'Update Date', 'Response Type', 'Address ID', 'Household Flag', 'Household ID', 'Contacted Party ID'))],
          ['recordType' => "4B", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Transaction Code', 'Position'))],
          ['recordType' => "4C", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', '4C Sequence Number', 'Document Type', 'Casework Transaction Document Name', 'File Location'))],
          ['recordType' => "4D", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', '4D Sequence Number', 'Text Type', 'Transaction Text'))],
          ['recordType' => "4E", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Merge Field Name', 'Merge Data'))],
          ['recordType' => "4F", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Party ID'))],
          ['recordType' => "5A", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Household ID', 'Household Salutation', 'Constituent ID', 'Primary Contact Flag'))],
          ['recordType' => "7A", 'fieldCount' => 18, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Description', 'Contact', 'Country', 'Organization', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Location', 'Status', 'Private Flag', 'Notes', 'Schedule Date', 'Scheduled by', 'Revision Date', 'Revised By'))],
          ['recordType' => "7B", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Code'))],
          ['recordType' => "7C", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'User ID'))],
          ['recordType' => "8A", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Code Type', 'Code', 'Code Description', 'Inactive Flag'))],
          ['recordType' => "8C", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Letter Description', 'Document Name', 'Owner', 'Creation Date', 'Revision Date', 'Archive Flag'))],
          ['recordType' => "8D", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Merge Field Name'))],
          ['recordType' => "8E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Correspondence or Transaction Code', 'Code Type'))]
        ];

        foreach ($recordTypes as $type) {
            DB::table('recordtypes')->insert($type);
        }
    }
}
