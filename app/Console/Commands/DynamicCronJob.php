<?php

namespace App\Console\Commands;

use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DynamicCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dynamic-cron-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled Dynamic Cron Job';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //  get cron jobs from database
        $cronJobs = CronJob::where('status', 1)->get();

        foreach ($cronJobs as $cronJob) {
            $this->scheduleJob($cronJob);
        }

        $this->info('Dynamic cron job scheduling completed.');
    }

    protected function scheduleJob($cronJob)
    {
        if (!empty($cronJob->last_run_datetime)) {
            switch ($cronJob->schedule_type) {
                case '1':
                    $this->callMinuteUrl($cronJob);
                    break;
                case '2': 
                    $this->callHourlyUrl($cronJob);
                    break;
                case '3':
                    $this->callDayOfMonthUrl($cronJob);
                    break;
                case '4':
                    $this->callMonthlyUrl($cronJob);
                    break;
                case '5':
                    $this->callWeeklyUrl($cronJob);;
                    break;
                case '6':
                    $this->callYearlyUrl($cronJob);
                    break;
                case '7':
                    $this->callDailyUrl($cronJob);
                    break;
                default:
                    $this->error('Invalid schedule type for job ID: ' . $cronJob->id);
            }
        } else {
            $column = array();
            $column['last_run_datetime'] = now();
            $cronJob->updateData($cronJob->id, $column);
        }
    }
    protected function callDailyUrl($data)
    {
        $currentDate = Carbon::now();
        $previousDate = Carbon::parse($data->last_run_datetime);

        $todayDate = $currentDate->format('Y-m-d');
        $lastRunDate = $previousDate->format('Y-m-d');

        $scheduledTime = Carbon::createFromFormat('H:i', $data->hours)->setDate(
            $currentDate->year, $currentDate->month, $currentDate->day
        );
        if ($lastRunDate < $todayDate && $currentDate->greaterThanOrEqualTo($scheduledTime)) {
            $this->callUrl($data->url, $data->id);
        }
    }

    protected function callMinuteUrl($data) 
    {
        $currentDate = Carbon::now();
        $previousDate = Carbon::parse($data->last_run_datetime);
        $minuteDiff = $currentDate->diffInMinutes($previousDate);

        if ($data->schedule_value <= $minuteDiff) {
            $this->callUrl($data->url, $data->id);
        }
    }

    protected function callHourlyUrl($data)
    {
        $currentDate = Carbon::now();
        $previousDate = Carbon::parse($data->last_run_datetime);
        $hourDiff = $currentDate->diffInHours($previousDate);

        if ($data->schedule_value <= $hourDiff) {
            $this->callUrl($data->url, $data->id);
        }
    }

    protected function callDayOfMonthUrl($data)
    {
        $currentDate = Carbon::now();
        $previousDate = Carbon::parse($data->last_run_datetime);
        $dayDiff = $currentDate->diffInDays($previousDate);

        $currentDay = (int)date('d');
        $currentTime = date('H:i:s');

        if ($dayDiff > 0 && $currentDay == $data->schedule_value && $currentTime == $data->hours) {
            $this->callUrl($data->url, $data->id);
        }
    }

    protected function callMonthlyUrl($data)
    {
        $currentDate = Carbon::now();
        $previousDate = Carbon::parse($data->last_run_datetime);
        $diff = $currentDate->diffInMinutes($previousDate);
        $currentDay = (int)date("d");
        $currentHrs = date("H:i");

        $lastRunMonth = (int)$previousDate->format("m");
        $currentMonth = (int)$currentDate->format("m");

        $currentMonth = (int)date("m");
        if($diff > 0 && $currentMonth == $data->schedule_value  && $currentDay == $data->day && $currentHrs == $data->hours && $lastRunMonth < $currentMonth ){
            $this->callUrl($data->url, $data->id);
        }
    }

    protected function callWeeklyUrl($data)
    {
        $currentDate = Carbon::now();
        $previousDate = Carbon::parse($data->last_run_datetime);
        $diff = $currentDate->diffInMinutes($previousDate);

        $currentDay = (int)date("d");
        $currentHrs = date("H:i");

        $arrWeek = array('Sunday' => 1,'Monday' => 2,'Tuesday' => 3,'Wednesday' => 4,'Thursday' => 5,'Friday' => 6,'Saturday' => 7);  
        $dateName =  date('l');    

        if($diff > 0 && $arrWeek[$dateName] == $data->schedule_value && $currentHrs == $data->hours){
            $this->callUrl($data->url, $data->id);
        }
    }

    protected function callYearlyUrl($data)
    {
        $currentDate = Carbon::now();
        $previousDate = Carbon::parse($data->last_run_datetime);
        $diff = $currentDate->diffInMinutes($previousDate);
        $currentDay = (int)date("d");
        $currentHrs = date("H:i");

        $lastRunYear = (int)$previousDate->format("Y");
        $currentYear = (int)$currentDate->format("Y");

        $currentMonth = (int)date("m");
        if ($diff > 0 && $currentMonth == $data->schedule_value && $currentDay == $data->day && $currentHrs == $data->hours && $lastRunYear < $currentYear) {
            $this->callUrl($data->url, $data->id);
        }
    }

    protected function callUrl($url, $id = 0)
    {
        $response = Http::get($url);
        $this->info('HTTP GET request completed for URL: ' . $url . ' Status Code: ' . $response->status());

        $data = array();
        $data['response'] = "Status Code: " . $response->status();

        if ($response->status() == 200) 
        {
            $data['last_run_datetime'] = now();
        }

        $this->updateCronData($id, $data);
    }

    public function updateCronData($id, $data)
    {
        CronJob::where('id', $id)->update($data);
    }
}
