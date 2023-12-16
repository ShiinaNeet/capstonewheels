<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\CompanyDetails;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\Help;
use App\Models\News;
use App\Models\Inquiry;
use App\Models\Message;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Requirement;
use App\Models\RescheduleEnrollment;
use App\Models\Room;
use App\Models\Schedule\ServiceSchedule;
use App\Models\Service;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rules\File;

class DatabaseController extends Controller
{
    public function export()
    {
        $rs = SharedFunctions::default_msg();

        $data = $structure = '';
        $tables = DB::select("SHOW TABLES");
        foreach ($tables as $table) {
            $keys = array_keys((array) $table);
            $dynamic_key = $keys[0];
            $select_query = "SELECT * FROM " . $table->{$dynamic_key};
            $records = DB::select($select_query);

            foreach ($records as $record) {
                $record = (array)$record;
                $table_column_array = array_keys($record);
                foreach ($table_column_array as $key => $name) {
                    $table_column_array[$key] = '`' . $table_column_array[$key] . '`';
                }
                $table_value_array = array_values($record);
                $data .= "\nINSERT INTO " . $table->{$dynamic_key} . " SET ";

                $record_column_array = [
                    "`action_user_id`", "`address`", "`barangay`",
                    "`birthdate`", "`cancel_reason`", "`city`",
                    "`content`", "`deleted_at`", "`description`",
                    "`email_verified_at`", "`expiration_date`", "`gender`",
                    "`image`", "`license_no`", "`middlename`",
                    "`province`", "`read_at`", "`region`",
                    "`restriction_codes`", "`room_id`", "`updated_at`",
                    "`update_reason`", "`vehicle_id`"
                ];

                for ($i = 0; $i < count($table_column_array); $i++) {
                    $column = $table_column_array[$i];
                    $record_column = $table_value_array[$i];

                    if (in_array($column, $record_column_array) && $record_column == "") $data .= $column . " = NULL";
                    else $data .= $column." = '" . str_replace("'", "''", $record_column) . "'";

                    if (($i + 1) != count($table_column_array)) $data .= ", ";
                } $data .= ";";
            }
        }
        $filename = database_path() . "/db-backup.sql";
        $f_handle = fopen($filename, 'w+');

        if ($f_handle) {
            $output = $structure . $data;
            $ebytes = fwrite($f_handle, $output);

            if ($ebytes !== false) {
                fclose($f_handle);
                $rs = SharedFunctions::success_msg("Database export ready");
                $rs['blob'] = base64_encode(file_get_contents($filename));
                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_DATABASE, AuditTrail::ACTION_CREATE, "Database exported"
                );
            } else {
                $rs = SharedFunctions::prompt_msg("Database export failed");
                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_DATABASE, AuditTrail::ACTION_NOTICE, "Database exported but failed"
                );
            }

            return response()->json($rs);
        } else {
            $rs = SharedFunctions::prompt_msg("Database export no write access");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_DATABASE, AuditTrail::ACTION_NOTICE, "Database exported but no write access"
            );
        }

        return response()->json($rs);
    }

    public function import(Request $request)
    {
        $rs = SharedFunctions::default_msg();

        if (isset($request->file) && $request->hasFile('file')) {
            $this->validate($request, ['file' => 'file|mimetypes:text/plain']);

            $filename = $request->file->store('temp');
            $sql_content = file_get_contents(storage_path("app/{$filename}"));

            if ($sql_content) {
                Storage::delete($filename);

                Schema::disableForeignKeyConstraints();
                $this->truncate_tables();
                DB::unprepared($sql_content);
                Schema::enableForeignKeyConstraints();

                $rs = SharedFunctions::success_msg("Database import success");
                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_DATABASE, AuditTrail::ACTION_CREATE, "Database imported"
                );
                Auth::logout();
            } else {
                $rs = SharedFunctions::prompt_msg("Database import failed");
                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_DATABASE, AuditTrail::ACTION_NOTICE, "Database imported but failed"
                );
            }

            return response()->json($rs);
        } else {
            $rs = SharedFunctions::prompt_msg("Database import read got interrupted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_DATABASE, AuditTrail::ACTION_NOTICE, "Database imported but read got interrupted"
            );
        }

        return response()->json($rs);
    }

    private function truncate_tables()
    {
        AuditTrail::truncate();
        CompanyDetails::truncate();
        Enrollment::truncate();
        Faq::truncate();
        Inquiry::truncate();
        Message::truncate();
        DB::table('migrations')->truncate();
        DB::table('notifications')->truncate();
        News::truncate();
        Payment::truncate();
        PaymentItem::truncate();
        Room::truncate();
        Service::truncate();
        Requirement::truncate();
        RescheduleEnrollment::truncate();
        ServiceSchedule::truncate();
        User::truncate();
        UserDetail::truncate();
        Vehicle::truncate();
    }
}
