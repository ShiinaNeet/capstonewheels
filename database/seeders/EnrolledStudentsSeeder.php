<?php

namespace Database\Seeders;

use App\Libraries\SharedFunctions;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Requirement;
use App\Models\Room;
use App\Models\Schedule\ServiceSchedule;
use App\Models\Service;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnrolledStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::transaction(function() {
            $date = Carbon::parse("2023-08-01 00:00:00");
            $start = $date->startOfMonth()->format('Y-m-d H:i:s');
            $end = $date->endOfMonth()->format('Y-m-d H:i:s');

            Enrollment::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            Payment::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            PaymentItem::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            Requirement::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            Room::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            ServiceSchedule::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            Service::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            User::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            UserDetail::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();
            Vehicle::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->forceDelete();

            $instructor = new User();
            $instructor->user_type = User::TYPE_INSTRUCTOR;
            $instructor->email = "testinstructor2021@email.com";
            $instructor->password = bcrypt("password");
            $instructor->created_at = $start;
            $instructor->updated_at = $start;
            $instructor->save();

            $instructor_details = new UserDetail();
            $instructor_details->user_id = $instructor->id;
            $instructor_details->firstname = "Vincent";
            $instructor_details->lastname = "Fabron";
            $instructor_details->gender = 1;
            $instructor_details->address = "Str.";
            $instructor_details->barangay = "Bar.";
            $instructor_details->city = "Cit.";
            $instructor_details->province = "Prv.";
            $instructor_details->birthdate = "1997-03-07";
            $instructor_details->mobile = "0";
            $instructor_details->created_at = $start;
            $instructor_details->updated_at = $start;
            $instructor_details->save();

            $requirement = new Requirement();
            $requirement->name = "Sample Requirement (Test)";
            $requirement->created_at = $start;
            $requirement->updated_at = $start;
            $requirement->save();

            $room = new Room();
            $room->name = "Sample Room (Test)";
            $room->created_at = $start;
            $room->updated_at = $start;
            $room->save();

            $vehicle_a = new Vehicle();
            $vehicle_a->name = "Automatic Vehicle (Test)";
            $vehicle_a->capacity = 4;
            $vehicle_a->transmission = 0;
            $vehicle_a->created_at = $start;
            $vehicle_a->updated_at = $start;
            $vehicle_a->save();

            $vehicle_m = new Vehicle();
            $vehicle_m->name = "Manual Vehicle (Test)";
            $vehicle_m->capacity = 4;
            $vehicle_m->transmission = 1;
            $vehicle_m->created_at = $start;
            $vehicle_m->updated_at = $start;
            $vehicle_m->save();

            $service_r = new Service();
            $service_r->room_id = $room->id;
            $service_r->type = Service::TYPE_ROOM;
            $service_r->name = "Room (Test)";
            $service_r->price = 800;
            $service_r->duration = 8;
            $service_r->max_capacity = 30;
            $service_r->requirements = $requirement->id;
            $service_r->created_at = $start;
            $service_r->updated_at = $start;
            $service_r->save();

            $service_av = new Service();
            $service_av->vehicle_id = $vehicle_a->id;
            $service_av->type = Service::TYPE_VEHICLE;
            $service_av->name = "Automatic Driving Course (Test)";
            $service_av->price = 3000;
            $service_av->duration = 8;
            $service_av->max_capacity = 1;
            $service_av->requirements = $requirement->id;
            $service_av->created_at = $start;
            $service_av->updated_at = $start;
            $service_av->save();

            $service_mv = new Service();
            $service_mv->vehicle_id = $vehicle_m->id;
            $service_mv->type = Service::TYPE_VEHICLE;
            $service_mv->name = "Manual Driving Course (Test)";
            $service_mv->price = 5000;
            $service_mv->duration = 8;
            $service_mv->max_capacity = 1;
            $service_mv->requirements = $requirement->id;
            $service_mv->created_at = $start;
            $service_mv->updated_at = $start;
            $service_mv->save();

            $new_date = Carbon::parse("2023-08-01 00:00:00");
            $date_end = Carbon::parse("2023-08-08 23:59:59");
            $current_date = $new_date;
            while ($current_date <= $date_end) {
                if ($current_date->dayOfWeek !== Carbon::SATURDAY && $current_date->dayOfWeek !== Carbon::SUNDAY) {
                    $service_sched_1 = new ServiceSchedule();
                    $service_sched_1->instructor_id = $instructor->id;
                    $service_sched_1->service_id = $service_r->id;
                    $service_sched_1->day_of_week = $current_date->format('Y-m-d');
                    $service_sched_1->time_start = "09:00:00";
                    $service_sched_1->time_end = "17:00:00";
                    $service_sched_1->status = ServiceSchedule::STATUS_COMPLETE;
                    $service_sched_1->created_at = $start;
                    $service_sched_1->updated_at = $start;
                    $service_sched_1->save();
                }
                $current_date->addDay();
            }

            $new_date = Carbon::parse("2023-08-11 00:00:00");
            $date_end = Carbon::parse("2023-08-22 23:59:59");
            $current_date = $new_date;
            while ($current_date <= $date_end) {
                if ($current_date->dayOfWeek !== Carbon::SATURDAY && $current_date->dayOfWeek !== Carbon::SUNDAY) {
                    $service_sched_2 = new ServiceSchedule();
                    $service_sched_2->instructor_id = $instructor->id;
                    $service_sched_2->service_id = $service_av->id;
                    $service_sched_2->day_of_week = $current_date->format('Y-m-d');
                    $service_sched_2->time_start = "09:00:00";
                    $service_sched_2->time_end = "17:00:00";
                    $service_sched_2->status = ServiceSchedule::STATUS_COMPLETE;
                    $service_sched_2->created_at = $start;
                    $service_sched_2->updated_at = $start;
                    $service_sched_2->save();
                }
                $current_date->addDay();
            }

            $new_date = Carbon::parse("2023-08-25 00:00:00");
            $date_end = Carbon::parse("2023-08-29 23:59:59");
            $current_date = $new_date;
            while ($current_date <= $date_end) {
                if ($current_date->dayOfWeek !== Carbon::SATURDAY && $current_date->dayOfWeek !== Carbon::SUNDAY) {
                    $service_sched_3 = new ServiceSchedule();
                    $service_sched_3->instructor_id = $instructor->id;
                    $service_sched_3->service_id = $service_mv->id;
                    $service_sched_3->day_of_week = $current_date->format('Y-m-d');
                    $service_sched_3->time_start = "09:00:00";
                    $service_sched_3->time_end = "17:00:00";
                    $service_sched_3->status = ServiceSchedule::STATUS_COMPLETE;
                    $service_sched_3->created_at = $start;
                    $service_sched_3->updated_at = $start;
                    $service_sched_3->save();
                }
                $current_date->addDay();
            }

            # STUDENT A -----------------------------------
            $student_a = new User();
            $student_a->user_type = User::TYPE_STUDENT;
            $student_a->email = "student_a@email.com";
            $student_a->password = bcrypt("password");
            $student_a->created_at = $start;
            $student_a->updated_at = $start;
            $student_a->save();

            $student_a_details = new UserDetail();
            $student_a_details->user_id = $student_a->id;
            $student_a_details->firstname = "Student A";
            $student_a_details->lastname = "Test";
            $student_a_details->gender = 1;
            $student_a_details->address = "Str.";
            $student_a_details->barangay = "Bar.";
            $student_a_details->city = "Cit.";
            $student_a_details->province = "Prv.";
            $student_a_details->birthdate = "1997-03-07";
            $student_a_details->mobile = "0";
            $student_a_details->created_at = $start;
            $student_a_details->updated_at = $start;
            $student_a_details->save();

            $reference_number = "2023-".SharedFunctions::generate_random_string(10, true);
            $payment = new Payment();
            $payment->student_id = $student_a->id;
            $payment->reference_no = $reference_number;
            $payment->mode_of_payment = Payment::MOP_CASH;
            $payment->status = Payment::STATUS_VERIFIED;
            $payment->created_at = $start;
            $payment->updated_at = $start;
            $payment->save();

            $enrollment = new Enrollment();
            $enrollment->service_id = $service_r->id;
            $enrollment->student_id = $student_a->id;
            $enrollment->status = Enrollment::STATUS_COMPLETED;
            $enrollment->created_at = $start;
            $enrollment->updated_at = $start;
            $enrollment->save();

            $payment_item = new PaymentItem();
            $payment_item->enrollment_id = $enrollment->id;
            $payment_item->payment_id = $payment->id;
            $payment_item->created_at = $start;
            $payment_item->updated_at = $start;
            $payment_item->save();

            $enrollment = new Enrollment();
            $enrollment->service_id = $service_av->id;
            $enrollment->student_id = $student_a->id;
            $enrollment->status = Enrollment::STATUS_COMPLETED;
            $enrollment->created_at = $start;
            $enrollment->updated_at = $start;
            $enrollment->save();

            $payment_item = new PaymentItem();
            $payment_item->enrollment_id = $enrollment->id;
            $payment_item->payment_id = $payment->id;
            $payment_item->created_at = $start;
            $payment_item->updated_at = $start;
            $payment_item->save();

            $enrollment = new Enrollment();
            $enrollment->service_id = $service_mv->id;
            $enrollment->student_id = $student_a->id;
            $enrollment->status = Enrollment::STATUS_COMPLETED;
            $enrollment->created_at = $start;
            $enrollment->updated_at = $start;
            $enrollment->save();

            $payment_item = new PaymentItem();
            $payment_item->enrollment_id = $enrollment->id;
            $payment_item->payment_id = $payment->id;
            $payment_item->created_at = $start;
            $payment_item->updated_at = $start;
            $payment_item->save();

            # STUDENT B -----------------------------------
            $student_a = new User();
            $student_a->user_type = User::TYPE_STUDENT;
            $student_a->email = "student_b@email.com";
            $student_a->password = bcrypt("password");
            $student_a->created_at = $start;
            $student_a->updated_at = $start;
            $student_a->save();

            $student_a_details = new UserDetail();
            $student_a_details->user_id = $student_a->id;
            $student_a_details->firstname = "Student B";
            $student_a_details->lastname = "Test";
            $student_a_details->gender = 1;
            $student_a_details->address = "Str.";
            $student_a_details->barangay = "Bar.";
            $student_a_details->city = "Cit.";
            $student_a_details->province = "Prv.";
            $student_a_details->birthdate = "1997-03-07";
            $student_a_details->mobile = "0";
            $student_a_details->created_at = $start;
            $student_a_details->updated_at = $start;
            $student_a_details->save();

            $reference_number = "2023-".SharedFunctions::generate_random_string(10, true);
            $payment = new Payment();
            $payment->student_id = $student_a->id;
            $payment->reference_no = $reference_number;
            $payment->mode_of_payment = Payment::MOP_CASH;
            $payment->status = Payment::STATUS_VERIFIED;
            $payment->created_at = $start;
            $payment->updated_at = $start;
            $payment->save();

            $enrollment = new Enrollment();
            $enrollment->service_id = $service_r->id;
            $enrollment->student_id = $student_a->id;
            $enrollment->status = Enrollment::STATUS_COMPLETED;
            $enrollment->created_at = $start;
            $enrollment->updated_at = $start;
            $enrollment->save();

            $payment_item = new PaymentItem();
            $payment_item->enrollment_id = $enrollment->id;
            $payment_item->payment_id = $payment->id;
            $payment_item->created_at = $start;
            $payment_item->updated_at = $start;
            $payment_item->save();

            $enrollment = new Enrollment();
            $enrollment->service_id = $service_av->id;
            $enrollment->student_id = $student_a->id;
            $enrollment->status = Enrollment::STATUS_COMPLETED;
            $enrollment->created_at = $start;
            $enrollment->updated_at = $start;
            $enrollment->save();

            $payment_item = new PaymentItem();
            $payment_item->enrollment_id = $enrollment->id;
            $payment_item->payment_id = $payment->id;
            $payment_item->created_at = $start;
            $payment_item->updated_at = $start;
            $payment_item->save();

            $enrollment = new Enrollment();
            $enrollment->service_id = $service_mv->id;
            $enrollment->student_id = $student_a->id;
            $enrollment->status = Enrollment::STATUS_COMPLETED;
            $enrollment->created_at = $start;
            $enrollment->updated_at = $start;
            $enrollment->save();

            $payment_item = new PaymentItem();
            $payment_item->enrollment_id = $enrollment->id;
            $payment_item->payment_id = $payment->id;
            $payment_item->created_at = $start;
            $payment_item->updated_at = $start;
            $payment_item->save();
        });
        Schema::enableForeignKeyConstraints();
    }
}
