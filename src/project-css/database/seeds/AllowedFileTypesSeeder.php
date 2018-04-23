<?php

use Illuminate\Database\Seeder;

class AllowedFileTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // insert stopwords into table
        $AllowedFileTypes = array("txt", "doc", "docx", "pdf", "xls", "xlsx", "ppt", "pptx", "jpg");

        foreach ($AllowedFileTypes as $key => $extension) {
            DB::table('allowedfiletypes')->insert([
                'extension' => $extension
            ]);
        }
    }
}
