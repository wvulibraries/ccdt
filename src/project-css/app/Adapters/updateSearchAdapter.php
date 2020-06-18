<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Adapters;

use Illuminate\Support\Facades\DB;
use Log;

class UpdateSearchAdapter {
       
    // process takes the current $srchIndex and removes duplicate information
    // and cleans up the string to be used by mysql full text search.
    // saving the result to the passed table with $id.  
    public function process($tblNme, $id, $srchIndex) {
        // remove extra characters replacing them with spaces
        // also remove .. that is in the filenames
        // remove . seen in prefix on names like Mr. Ms.
        $cleanString = preg_replace('/[^A-Za-z0-9._ ]/', ' ', str_replace('. ', ' ', str_replace('..', '', $srchIndex)));
 
        // common words that are not needed in our search index
        $stopWords = array("a","able","about","across","after","all","almost","also","am","among","an","and","any","are","as","at","be","because","been","but","by","can","cannot","could","dear","did","do","does","either","else","ever","every","for","from","get","got","had","has","have","he","her","hers","him","his","how","however","i","if","in","into","is","it","its","just","least","let","like","likely","may","me","might","most","must","my","neither","no","nor","not","of","off","often","on","only","or","other","our","own","rather","said","say","says","she","should","since","so","some","than","that","the","their","them","then","there","these","they","this","tis","to","too","twas","us","wants","was","we","were","what","when","where","which","while","who","whom","why","will","with","would","yet","you","your","ain't","aren't","can't","could've","couldn't","didn't","doesn't","don't","hasn't","he'd","he'll","he's","how'd","how'll","how's","i'd","i'll","i'm","i've","isn't","it's","might've","mightn't","must've","mustn't","shan't","she'd","she'll","she's","should've","shouldn't","that'll","that's","there's","they'd","they'll","they're","they've","wasn't","we'd","we'll","we're","weren't","what'd","what's","when'd","when'll","when's","where'd","where'll","where's","who'd","who'll","who's","why'd","why'll","why's","won't","would've","wouldn't","you'd","you'll","you're","you've");

        // Remove duplicate Keywords, common words and anything less than 2 characters
        $matchWords = array_filter(array_unique(explode(' ', $cleanString)) , function ($item) use ($stopWords) { return !($item == '' || in_array($item, $stopWords) || mb_strlen($item) < 2);});
 
        DB::table($tblNme)
                ->where('id', $id)
                ->update(['srchindex' => implode(' ', $matchWords)]);        
    }

}