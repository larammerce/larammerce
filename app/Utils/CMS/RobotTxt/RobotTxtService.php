<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 1/9/19
 * Time: 11:29 AM
 */

namespace App\Utils\CMS\RobotTxt;

use App\Models\RobotTxtRecord;

class RobotTxtService
{
    const FILE_POSTFIX = "-robot-partial.txt";
    const LOCK_FILE_POSTFIX = "-robot-partial.lock";
    const SESSION_POSTFIX = "-robot-partial.session";

    private int $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function generate(): void
    {
        $robotTxtPath = 'test_robots.txt';
        $recordCategories = RobotTxtRecord::all()->groupBy('user_agent')->all();
        $contents = is_file($robotTxtPath) ? file_get_contents($robotTxtPath) : '';
        foreach ($recordCategories as $recordCategory => $records) {
            $headLine = "userAgent: $recordCategory \n";
            if (strlen(trim(exec("cat $robotTxtPath | grep $headLine"))) < 2)
                file_put_contents("$robotTxtPath", "$headLine", FILE_APPEND);
            foreach ($records as $record) {
                $line = "$record->permission:$record->url \n";
                if (strlen(trim(exec("cat $robotTxtPath | grep $line"))) < 2)
                    file_put_contents("$robotTxtPath", $line, FILE_APPEND);
                $relatedRecord = RobotTxtRecord::where("url", $record->modifiedUrl->url_new)->first();
                if (!is_null($relatedRecord)) {
                    $relatedLine = "$relatedRecord->permission:$relatedRecord->url \n";
                    if (strlen(trim(exec("cat $robotTxtPath | grep $relatedLine"))) > 1) {
                        $contents = str_replace($relatedLine, '', $contents);
                        file_put_contents($robotTxtPath, $contents);
                    }
                }
            }
            file_put_contents("$robotTxtPath", "\n", FILE_APPEND);
        }
    }
}
