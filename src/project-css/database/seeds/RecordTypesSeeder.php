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
        // insert record types into table
        $recordTypes = [
          ['cmsid' => '1', 'recordType' => "1A", 'fieldCount' => 13, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag'))],
          ['cmsid' => '1', 'recordType' => "1B", 'fieldCount' => 22, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Address ID', 'Address Type', 'Primary Flag', 'Default Address Flag', 'Title', 'Organization Name', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Carrier Route', 'County', 'Country', 'District', 'Precinct', 'No Mail Flag', 'Agency Code'))],
          ['cmsid' => '1', 'recordType' => "1C", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Constituent Code Type', 'Constituent Code'))],
          ['cmsid' => '1', 'recordType' => "1D", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', '1D Sequence Number', 'Constituent Text Type', 'Constituent Text'))],
          ['cmsid' => '1', 'recordType' => "1E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Constituent Phone or email Type', 'Phone Number Email or URL'))],
          ['cmsid' => '1', 'recordType' => "2A", 'fieldCount' => 13, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Correspondence Type', 'Staff', 'Date In', 'Date Out', 'Tickler date', 'Update Date', 'Response Type', 'Address ID', 'Household Flag', 'Household ID'))],
          ['cmsid' => '1', 'recordType' => "2B", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Correspondence Code', 'Position'))],
          ['cmsid' => '1', 'recordType' => "2C", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', '2C Sequence Number', 'Document Type', 'Correspondence Document Name', 'File Location'))],
          ['cmsid' => '1', 'recordType' => "2D", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', '2D Sequence Number', 'Text Type', 'Correspondence Text'))],
          ['cmsid' => '1', 'recordType' => "2E", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Correspondence ID', 'Merge Field Name', 'Merge Data'))],
          ['cmsid' => '1', 'recordType' => "3A", 'fieldCount' => 11, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Staff', 'Start Date', 'End Date', 'Tickler date', 'Update Date', 'Casework Description', 'Casework Status', 'Agency ID'))],
          ['cmsid' => '1', 'recordType' => "3B", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Casework Code'))],
          ['cmsid' => '1', 'recordType' => "3D", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', '3D Sequence Number', 'Text Type', 'Casework Text'))],
          ['cmsid' => '1', 'recordType' => "3E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Party ID'))],
          ['cmsid' => '1', 'recordType' => "4A", 'fieldCount' => 14, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction Type', 'Staff', 'Start Date', 'End Date', 'Tickler date', 'Update Date', 'Response Type', 'Address ID', 'Household Flag', 'Household ID', 'Contacted Party ID'))],
          ['cmsid' => '1', 'recordType' => "4B", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Transaction Code', 'Position'))],
          ['cmsid' => '1', 'recordType' => "4C", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', '4C Sequence Number', 'Document Type', 'Casework Transaction Document Name', 'File Location'))],
          ['cmsid' => '1', 'recordType' => "4D", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', '4D Sequence Number', 'Text Type', 'Transaction Text'))],
          ['cmsid' => '1', 'recordType' => "4E", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Merge Field Name', 'Merge Data'))],
          ['cmsid' => '1', 'recordType' => "4F", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Constituent ID', 'Casework ID', 'Transaction ID', 'Party ID'))],
          ['cmsid' => '1', 'recordType' => "5A", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Household ID', 'Household Salutation', 'Constituent ID', 'Primary Contact Flag'))],
          ['cmsid' => '1', 'recordType' => "7A", 'fieldCount' => 18, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Description', 'Contact', 'Country', 'Organization', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Location', 'Status', 'Private Flag', 'Notes', 'Schedule Date', 'Scheduled by', 'Revision Date', 'Revised By'))],
          ['cmsid' => '1', 'recordType' => "7B", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Code'))],
          ['cmsid' => '1', 'recordType' => "7C", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'User ID'))],
          ['cmsid' => '1', 'recordType' => "8A", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Code Type', 'Code', 'Code Description', 'Inactive Flag'))],
          ['cmsid' => '1', 'recordType' => "8C", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Letter Description', 'Document Name', 'Owner', 'Creation Date', 'Revision Date', 'Archive Flag'))],
          ['cmsid' => '1', 'recordType' => "8D", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Merge Field Name'))],
          ['cmsid' => '1', 'recordType' => "8E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Letter Code', 'Correspondence or Transaction Code', 'Code Type'))],
          ['cmsid' => '2', 'recordType' => "1A", 'fieldCount' => 16, 'fieldNames' => serialize(array("Record Type", "Person ID", "Person Type", "Prefix", "First Name", "Middle Name", "Last Name", "Suffix", "Appellation", "Organization Name", "Salutation", "Date of Birth", "No Mail Flag", "Deceased Flag", "Spouse's Name", "Email Flag"))],
          ['cmsid' => '2', 'recordType' => "1B", 'fieldCount' => 22, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Address ID', 'Address Type', 'Primary Flag', 'Default Address Flag', 'Title', 'Organization Name', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Carrier Route', 'County', 'Country', 'District', 'Precinct', 'No Mail Flag', 'Deliverability'))],
          ['cmsid' => '2', 'recordType' => "1C", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Person Code Type', 'Code'))],
          ['cmsid' => '2', 'recordType' => "1D", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Person ID', '1D Sequence Number', 'Text', 'Date', 'Time', 'User ID'))],
          ['cmsid' => '2', 'recordType' => "1E", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Person Phone or email Type', 'Phone Number, Email, or URL', 'Primary Flag', 'Invalid Flag'))],
          ['cmsid' => '2', 'recordType' => "1F", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Document ID', 'User ID', 'Attached Date', 'Text'))],
          ['cmsid' => '2', 'recordType' => "2A", 'fieldCount' => 20, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Communication ID', 'Workflow ID', 'Workflow Person ID', 'Communication Type', 'User ID', 'Approved By', 'Status*', 'Date In', 'Date Out', 'Reminder date', 'Update Date', 'Response Type', 'Address ID', 'Email Address', 'Household Flag', 'Household ID', 'Group Name', 'Salutation'))],
          ['cmsid' => '2', 'recordType' => "2B", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Communication ID', 'Communication Code', 'Position'))],
          ['cmsid' => '2', 'recordType' => "2C", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Communication ID', 'Document Type', 'Communication Document Name', 'Communication Document ID', 'File Location'))],
          ['cmsid' => '2', 'recordType' => "2D", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Communication ID', '2D Sequence Number', 'Text', 'Date', 'Time', 'User ID'))],
          ['cmsid' => '2', 'recordType' => "2E", 'fieldCount' => 5, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Communication ID', 'Fill-in Field Name', 'Fill-in Data'))],
          ['cmsid' => '2', 'recordType' => "3A", 'fieldCount' => 11, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Workflow ID', 'Workflow Type', 'User ID', 'Start Date', 'End date', 'Reminder date', 'Update Date', 'Workflow Description', 'Status'))],
          ['cmsid' => '2', 'recordType' => "3B", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Workflow ID', 'Workflow Code'))],
          ['cmsid' => '2', 'recordType' => "3D", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Workflow ID', '3D Sequence Number', 'Text', 'Date', 'Time', 'User ID'))],
          ['cmsid' => '2', 'recordType' => "3E", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Workflow ID', 'Party ID'))],
          ['cmsid' => '2', 'recordType' => "3F", 'fieldCount' => 8, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Workflow ID', 'Document Name', 'Document ID', 'User ID', 'Attached Date', 'Text'))],
          ['cmsid' => '2', 'recordType' => "3L", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Person ID', 'Workflow ID', 'Workflow Type', 'Field Name', 'Field Value'))],
          ['cmsid' => '2', 'recordType' => "5A", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Household ID', 'Household Name', 'Household Salutation'))],
          ['cmsid' => '2', 'recordType' => "5B", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Household ID', 'Person ID', 'Primary Contact Flag'))],
          ['cmsid' => '2', 'recordType' => "5C", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Household ID', 'Document Name', 'Document ID', 'User ID', 'Attached Date', 'Text'))],
          ['cmsid' => '2', 'recordType' => "5D", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Household ID', '5D Sequence Number', 'Text', 'Date', 'Time', 'User ID'))],
          ['cmsid' => '2', 'recordType' => "6A", 'fieldCount' => 17, 'fieldNames' => serialize(array('Record Type', 'Document ID', 'Version', 'Document Grouping ID', 'Document Type', 'Document Display Name', 'Document Descripton', 'Document Name', 'Created By', 'Revised By', 'Approved By', 'Creation Date', 'Revision Date', 'Last Used Date', 'Status', 'Inactive Flag', 'Virtual Directory'))],
          ['cmsid' => '2', 'recordType' => "6B", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Document ID', 'Fill-in Field Name'))],
          ['cmsid' => '2', 'recordType' => "6C", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Document ID', 'Code Type', 'Code'))],
          ['cmsid' => '2', 'recordType' => "6D", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Document ID', 'Document Name', 'User ID', 'Attached Date', 'Text', 'Form Letter Attachment Flag'))],
          ['cmsid' => '2', 'recordType' => "6E", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Document ID', '6E Sequence Number', 'Text', 'Date', 'Time', 'User ID'))],
          ['cmsid' => '2', 'recordType' => "6F", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Document ID', 'Owned By'))],
          ['cmsid' => '2', 'recordType' => "7A", 'fieldCount' => 27, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Recurrence ID', 'Event Description', 'Contact', 'Country', 'Organization', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Time Zone', 'Location', 'Status', 'Scheduled Date', 'Scheduled Date', 'Scheduled by', 'Revision Date', 'Revised By', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Country'))],
          ['cmsid' => '2', 'recordType' => "7B", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Event Code'))],
          ['cmsid' => '2', 'recordType' => "7C", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Participant Type', 'Participant ID'))],
          ['cmsid' => '2', 'recordType' => "7D", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Document Name', 'Document ID', 'User ID', 'Attached Date', 'Text'))],
          ['cmsid' => '2', 'recordType' => "7E", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Event ID', '7E Sequence Number', 'Text', 'Date', 'Time', 'User ID'))],
          ['cmsid' => '2', 'recordType' => "7F", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Workflow ID'))],
          ['cmsid' => '2', 'recordType' => "7H", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Recurrence ID', 'Start Series Date', 'End Series Date', 'Recurrence Type', 'Recurrence Interval'))],
          ['cmsid' => '2', 'recordType' => "7I", 'fieldCount' => 27, 'fieldNames' => serialize(array('Record Type', 'Recurrence ID', 'Not Used', 'Event Description', 'Contact', 'Country', 'Organization', 'Not used', 'Start Time', 'Not used', 'End Time', 'Time Zone', 'Location', 'Status', 'Private Flag', 'Scheduled Date', 'Scheduled by', 'Revision Date', 'Revised By', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Country'))],
          ['cmsid' => '2', 'recordType' => "7J", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Recurrence ID', 'Event Code'))],
          ['cmsid' => '2', 'recordType' => "7K", 'fieldCount' => 4, 'fieldNames' => serialize(array('Record Type', 'Event ID', 'Participant Type', 'Participant ID'))],
          ['cmsid' => '2', 'recordType' => "7L", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Recurrence ID', 'Document Name', 'Document ID', 'User ID', 'Attached Date', 'Text'))],
          ['cmsid' => '2', 'recordType' => "7M", 'fieldCount' => 7, 'fieldNames' => serialize(array('Record Type', 'Recurrence ID', '7M Sequence Number', 'Text', 'Date', 'Time', 'User ID'))],
          ['cmsid' => '2', 'recordType' => "7N", 'fieldCount' => 3, 'fieldNames' => serialize(array('Record Type', 'Recurrence ID', 'Workflow ID'))],
          ['cmsid' => '2', 'recordType' => "8A", 'fieldCount' => 6, 'fieldNames' => serialize(array('Record Type', 'Code Type', 'Code', 'Code Description', 'Inactive Flag', 'User ID'))],
        ];

        foreach ($recordTypes as $type) {
            DB::table('recordtypes')->insert($type);
        }
    }
}
