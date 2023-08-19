<?php

namespace App\Http\Controllers\Admin;

use Spatie\DbDumper\Databases\MySql;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class DatabaseController
 * @package App\Http\Controllers\Admin
 *
 * @role(enabled=true)
 */
class DatabaseController extends BaseController
{
    /**
     * @role(super_user)
     */
    public function export(): BinaryFileResponse {
        // Define a unique name for the SQL file
        $filename = "database_backup_" . now()->format('Y_m_d_H_i_s') . ".sql";

        // Define a temporary path for the SQL file
        $temporaryFilePath = storage_path("app/" . $filename);

        // Dump the database to the SQL file using the config() function
        MySql::create()
            ->setDumpBinaryPath(config('database.connections.mysql.dump.dump_binary_path'))
            ->setDbName(config('database.connections.mysql.database'))
            ->setUserName(config('database.connections.mysql.username'))
            ->setPassword(config('database.connections.mysql.password'))
            ->dumpToFile($temporaryFilePath);

        // Download the SQL file and then delete it from server storage
        return response()->download($temporaryFilePath)->deleteFileAfterSend(true);
    }


    public function getModel(): ?string {
        return null;
    }
}
